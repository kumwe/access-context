<?php

/**
 * Bind a fresh multi-factor proof into a context and test its freshness at the exact boundaries.
 *
 * Run with `php examples/step-up-context.php` after `composer install`. The proof is bound to the actor, the
 * rotated session, the site, the organization and the epoch; the context refuses a proof bound elsewhere, and
 * `isValidFor()` is inclusive at verification and exclusive at expiry. Time is always supplied by the caller.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

use Kumwe\Context\Contract\Principal;
use Kumwe\Context\Exception\InvalidContext;
use Kumwe\Context\Value\AuthenticatedSurface;
use Kumwe\Context\Value\AuthenticationStrength;
use Kumwe\Context\Value\ExecutionContext;
use Kumwe\Context\Value\SiteContext;
use Kumwe\Context\Value\StepUpProof;

require $argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php';

$provenance = new stdClass();
$principal = new readonly class ($provenance) implements Principal {
    public function __construct(private object $provenance)
    {
    }

    public function subject(): string
    {
        return '018f22e2-7c8b-7ab0-8f3a-88e8026bb301';
    }

    public function securityEpoch(): int
    {
        return 1;
    }

    public function hasProvenance(object $provenance): bool
    {
        return $this->provenance === $provenance;
    }

    public function authorizationFingerprint(): string
    {
        return hash('sha256', 'authorization');
    }

    public function authorityFingerprint(): string
    {
        return hash('sha256', 'authority');
    }
};

$verifiedAt = new DateTimeImmutable('2026-01-01T10:00:00+00:00');
$expiresAt = $verifiedAt->modify('+5 minutes');
$site = SiteContext::default();

$proof = new StepUpProof(
    $principal->subject(),
    'rotated-session-77',
    $site,
    null,
    'totp',
    $verifiedAt,
    $expiresAt,
    nonce: str_repeat('n', 32),
    purpose: 'records.delete',
    securityEpoch: 1,
);

$context = ExecutionContext::issueHuman(
    $provenance,
    $principal,
    $site,
    AuthenticationStrength::MultiFactor,
    'req-0009',
    surface: AuthenticatedSurface::Administrator,
    sessionId: 'rotated-session-77',
    stepUpProof: $proof,
);

$refusal = null;
try {
    ExecutionContext::issueHuman(
        $provenance,
        $principal,
        $site,
        AuthenticationStrength::MultiFactor,
        'req-0010',
        sessionId: 'another-session',
        stepUpProof: $proof,
    );
} catch (InvalidContext $error) {
    $refusal = $error->getMessage();
}

$check = static fn (DateTimeImmutable $now): bool => $proof->isValidFor(
    $principal->subject(),
    'rotated-session-77',
    $site,
    null,
    $now,
);

echo json_encode([
    'context' => $context->toArray(),
    'valid_at_verification' => $check($verifiedAt),
    'valid_one_second_before_expiry' => $check($expiresAt->modify('-1 second')),
    'valid_at_expiry' => $check($expiresAt),
    'valid_before_verification' => $check($verifiedAt->modify('-1 second')),
    'proof_bound_to_other_session_refused_with' => $refusal,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), "\n";
