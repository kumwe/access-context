<?php

/**
 * Issue a human execution context from facts a host has already established, then export it.
 *
 * Run with `php examples/human-context.php` after `composer install`. The host adapter below is the only thing
 * a consumer writes: an implementation of the Principal contract over its own authenticated user. Nothing in
 * this example reads a request, a session store or a container; every fact is passed in explicitly.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

use Kumwe\Context\Contract\Principal;
use Kumwe\Context\Value\AuthenticatedSurface;
use Kumwe\Context\Value\AuthenticationStrength;
use Kumwe\Context\Value\ExecutionContext;
use Kumwe\Context\Value\MembershipContext;
use Kumwe\Context\Value\OrganizationContext;
use Kumwe\Context\Value\SiteContext;
use Kumwe\Context\Value\WorkspaceContext;

require dirname(__DIR__) . '/vendor/autoload.php';

// The host's composition root owns one private provenance object; only contexts carrying it are trusted.
$provenance = new stdClass();

// A host adapter over its authenticated user: stable identifiers and claims, never grants or sessions.
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
        return 3;
    }

    public function hasProvenance(object $provenance): bool
    {
        return $this->provenance === $provenance;
    }

    public function authorizationFingerprint(): string
    {
        return hash('sha256', 'subject|credential|epoch|grants');
    }

    public function authorityFingerprint(): string
    {
        return hash('sha256', 'subject|epoch|grants');
    }
};

// Membership is a server-resolved, versioned snapshot; a submitted organization never becomes one.
$membership = new MembershipContext(
    '018f22e2-7c8b-7ab0-8f3a-88e8026bb302',
    OrganizationContext::fromString('Acme'),
    WorkspaceContext::fromString('finance'),
    membershipVersion: 4,
    policyGeneration: 2,
);

$context = ExecutionContext::issueHuman(
    $provenance,
    $principal,
    SiteContext::fromString('Shop-EU '),
    AuthenticationStrength::Password,
    requestId: 'req-0001',
    correlationId: 'trace-0001',
    surface: AuthenticatedSurface::Administrator,
    membership: $membership,
    sessionId: 'session-row-42',
);

$nested = $context->child('req-0002');

echo json_encode([
    'context' => $context->toArray(),
    'trusted' => $context->hasProvenance($provenance),
    'trusted_by_stranger' => $context->hasProvenance(new stdClass()),
    'nested_request' => $nested->requestId(),
    'nested_correlation' => $nested->correlationId(),
    'satisfies_password' => $context->authenticationStrength()->satisfies(AuthenticationStrength::Password),
    'satisfies_multi_factor' => $context->authenticationStrength()->satisfies(AuthenticationStrength::MultiFactor),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), "\n";
