<?php

/**
 * Proves the bindings, grammars, freshness boundaries and redacted export of a step-up proof.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Case;

use DateTimeImmutable;
use Kumwe\Context\Tests\TestCase;
use Kumwe\Context\Value\OrganizationContext;
use Kumwe\Context\Value\SiteContext;
use Kumwe\Context\Value\StepUpProof;
use Kumwe\Context\Value\WorkspaceContext;
use ReflectionClass;

/**
 * Step-up proof construction, refusal, validity and export.
 *
 * @since  0.1.0
 */
final class StepUpProofTest extends TestCase
{
    /**
     * A narrower workspace cannot exist without its enclosing organization.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testWorkspaceRequiresOrganization(): void
    {
        $this->assertRefused(
            fn (): StepUpProof => $this->proof(['workspace' => WorkspaceContext::fromString('finance')]),
            'A step-up workspace requires an organization.',
            'Orphan workspace proof is refused.',
        );
    }

    /**
     * Actor subject used across the case.
     *
     * @var    string
     * @since  0.1.0
     */
    private const ACTOR = '018f22e2-7c8b-7ab0-8f3a-88e8026bb301';

    /**
     * Nonce of the minimum admitted length.
     *
     * @var    string
     * @since  0.1.0
     */
    private const NONCE = 'abcdefghijklmnopqrstuvwxyz012345';

    /**
     * Build a valid proof, overriding any argument by name.
     *
     * @param   array<string, mixed>  $overrides  Constructor arguments to replace.
     *
     * @return  StepUpProof  The proof.
     *
     * @since   0.1.0
     */
    private function proof(array $overrides = []): StepUpProof
    {
        $verifiedAt = new DateTimeImmutable('2026-01-01T10:00:00+00:00');
        $arguments = [
            'actorId' => self::ACTOR,
            'sessionId' => 'session-77',
            'site' => SiteContext::default(),
            'organization' => null,
            'method' => 'totp',
            'verifiedAt' => $verifiedAt,
            'expiresAt' => $verifiedAt->modify('+5 minutes'),
            'nonce' => self::NONCE,
            'workspace' => null,
            'purpose' => 'records.delete',
            'securityEpoch' => 1,
        ];
        /** @var array<string, mixed> $arguments */
        $arguments = array_replace($arguments, $overrides);

        return new StepUpProof(...$arguments);
    }

    /**
     * A valid proof exposes every binding it was built from, and the defaults are the documented ones.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testExposesEveryBindingAndDocumentedDefaults(): void
    {
        $proof = $this->proof([
            'organization' => OrganizationContext::fromString('acme'),
            'workspace' => WorkspaceContext::fromString('finance'),
            'securityEpoch' => 3,
        ]);

        $this->assertSame(self::ACTOR, $proof->actorId(), 'Actor.');
        $this->assertSame('session-77', $proof->sessionId(), 'Session.');
        $this->assertTrue($proof->site()->equals(SiteContext::default()), 'Site.');
        $this->assertSame('acme', $proof->organization()?->identifier(), 'Organization.');
        $this->assertSame('finance', $proof->workspace()?->identifier(), 'Workspace.');
        $this->assertSame('totp', $proof->method(), 'Method.');
        $this->assertSame('2026-01-01T10:00:00+00:00', $proof->verifiedAt()->format(DATE_ATOM), 'Verified at.');
        $this->assertSame('2026-01-01T10:05:00+00:00', $proof->expiresAt()->format(DATE_ATOM), 'Expires at.');
        $this->assertSame(self::NONCE, $proof->nonce(), 'Nonce.');
        $this->assertSame('records.delete', $proof->purpose(), 'Purpose.');
        $this->assertSame(3, $proof->securityEpoch(), 'Epoch.');

        $defaults = new StepUpProof(
            self::ACTOR,
            'session-77',
            SiteContext::default(),
            null,
            'totp',
            $proof->verifiedAt(),
            $proof->expiresAt(),
            self::NONCE,
        );
        $this->assertNull($defaults->workspace(), 'No workspace by default.');
        $this->assertSame('legacy.step_up', $defaults->purpose(), 'The preserved default purpose.');
        $this->assertSame(1, $defaults->securityEpoch(), 'The preserved default epoch.');
    }

    /**
     * Actor and session identities follow the opaque identifier grammar.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRefusesInvalidIdentities(): void
    {
        foreach (['', str_repeat('a', 192), "a\x00b", "a\x7f", "line\nbreak"] as $value) {
            $this->assertRefused(
                fn (): StepUpProof => $this->proof(['actorId' => $value]),
                'The step-up actor identity is invalid.',
                'Actor ' . json_encode($value) . ' must be refused.',
            );
            $this->assertRefused(
                fn (): StepUpProof => $this->proof(['sessionId' => $value]),
                'The step-up session identity is invalid.',
                'Session ' . json_encode($value) . ' must be refused.',
            );
        }
        $this->proof(['actorId' => str_repeat('a', 191), 'sessionId' => str_repeat('s', 191)]);
    }

    /**
     * The method is a lowercase token of 2 to 32 characters starting with a letter.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRefusesMethodOutsideGrammar(): void
    {
        foreach (['', 't', 'TOTP', '1totp', 'totp!', 'to tp', str_repeat('t', 33), 'totp.x'] as $method) {
            $this->assertRefused(
                fn (): StepUpProof => $this->proof(['method' => $method]),
                'The step-up method is invalid.',
                'Method ' . json_encode($method) . ' must be refused.',
            );
        }
        $this->proof(['method' => 'recovery_code']);
        $this->proof(['method' => str_repeat('t', 32)]);
    }

    /**
     * The freshness interval is non-empty, forward and at most fifteen minutes, tested at each boundary.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testFreshnessIntervalBoundaries(): void
    {
        $verifiedAt = new DateTimeImmutable('2026-01-01T10:00:00+00:00');
        foreach ([$verifiedAt, $verifiedAt->modify('-1 second'), $verifiedAt->modify('+15 minutes +1 second')] as $at) {
            $this->assertRefused(
                fn (): StepUpProof => $this->proof(['expiresAt' => $at]),
                'The step-up freshness interval is invalid.',
                'An expiry at ' . $at->format(DATE_ATOM) . ' must be refused.',
            );
        }
        $this->proof(['expiresAt' => $verifiedAt->modify('+1 second')]);
        $this->proof(['expiresAt' => $verifiedAt->modify('+15 minutes')]);
        $this->proof(['expiresAt' => new DateTimeImmutable('2026-01-01T12:15:00+02:00')]);
    }

    /**
     * The nonce is 32 to 128 URL-safe characters; the purpose is a lowercase dotted identifier of at most 127.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRefusesNonceAndPurposeOutsideGrammar(): void
    {
        foreach (['', str_repeat('n', 31), str_repeat('n', 129), str_repeat('n', 31) . '+', str_repeat('n', 31) . ' '] as $nonce) {
            $this->assertRefused(
                fn (): StepUpProof => $this->proof(['nonce' => $nonce]),
                'The step-up proof nonce is invalid.',
                'Nonce ' . json_encode($nonce) . ' must be refused.',
            );
        }
        $this->proof(['nonce' => str_repeat('N', 128)]);
        $this->proof(['nonce' => 'A-b_0' . str_repeat('9', 27)]);

        foreach (['', 'Records.delete', '1records', 'records delete', 'r' . str_repeat('e', 127), 'records/delete'] as $purpose) {
            $this->assertRefused(
                fn (): StepUpProof => $this->proof(['purpose' => $purpose]),
                'The step-up proof purpose is invalid.',
                'Purpose ' . json_encode($purpose) . ' must be refused.',
            );
        }
        $this->proof(['purpose' => 'r' . str_repeat('e', 126)]);
        $this->proof(['purpose' => 'a.b_c:d-e']);
    }

    /**
     * The epoch is positive.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRefusesNonPositiveEpoch(): void
    {
        foreach ([0, -1, PHP_INT_MIN] as $epoch) {
            $this->assertRefused(
                fn (): StepUpProof => $this->proof(['securityEpoch' => $epoch]),
                'The step-up proof security epoch must be positive.',
                'Epoch ' . $epoch . ' must be refused.',
            );
        }
    }

    /**
     * Validity is inclusive at verification, exclusive at expiry, and false before verification.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testValidityTimeBoundaries(): void
    {
        $proof = $this->proof();
        $check = static fn (DateTimeImmutable $now): bool => $proof->isValidFor(
            self::ACTOR,
            'session-77',
            SiteContext::default(),
            null,
            $now,
        );

        $this->assertFalse($check($proof->verifiedAt()->modify('-1 second')), 'Not yet valid.');
        $this->assertTrue($check($proof->verifiedAt()), 'Valid at the verification instant.');
        $this->assertTrue($check($proof->expiresAt()->modify('-1 second')), 'Valid just before expiry.');
        $this->assertFalse($check($proof->expiresAt()), 'Stale at the expiry instant.');
        $this->assertFalse($check($proof->expiresAt()->modify('+1 day')), 'Stale afterwards.');
        $this->assertTrue($check(new DateTimeImmutable('2026-01-01T12:02:00+02:00')), 'Offsets compare as instants.');
    }

    /**
     * Every binding must match exactly for the proof to be valid.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testValidityRequiresEveryBinding(): void
    {
        $now = new DateTimeImmutable('2026-01-01T10:01:00+00:00');
        $acme = OrganizationContext::fromString('acme');
        $scoped = $this->proof(['organization' => $acme]);
        $global = $this->proof();

        $this->assertTrue($scoped->isValidFor(self::ACTOR, 'session-77', SiteContext::default(), $acme, $now), 'Match.');
        $this->assertFalse(
            $scoped->isValidFor('018f22e2-7c8b-7ab0-8f3a-88e8026bb399', 'session-77', SiteContext::default(), $acme, $now),
            'Another actor.',
        );
        $this->assertFalse(
            $scoped->isValidFor(self::ACTOR, 'session-78', SiteContext::default(), $acme, $now),
            'Another session.',
        );
        $this->assertFalse(
            $scoped->isValidFor(self::ACTOR, 'session-77', SiteContext::fromString('other'), $acme, $now),
            'Another site.',
        );
        $this->assertFalse(
            $scoped->isValidFor(self::ACTOR, 'session-77', SiteContext::default(), null, $now),
            'Organization dropped.',
        );
        $this->assertFalse(
            $scoped->isValidFor(self::ACTOR, 'session-77', SiteContext::default(), OrganizationContext::fromString('x'), $now),
            'Another organization.',
        );
        $this->assertFalse(
            $global->isValidFor(self::ACTOR, 'session-77', SiteContext::default(), $acme, $now),
            'An organization-less proof does not cover an organization.',
        );
        $this->assertTrue(
            $global->isValidFor(self::ACTOR, 'session-77', SiteContext::default(), null, $now),
            'An organization-less proof covers organization-less work.',
        );
    }

    /**
     * The export omits the nonce and the session identity and renders instants in UTC.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testExportRedactsSecretsAndCanonicalisesTime(): void
    {
        $proof = $this->proof([
            'organization' => OrganizationContext::fromString('acme'),
            'workspace' => WorkspaceContext::fromString('finance'),
            'verifiedAt' => new DateTimeImmutable('2026-01-01T12:00:00+02:00'),
            'expiresAt' => new DateTimeImmutable('2026-01-01T12:05:00+02:00'),
        ]);

        $export = $proof->toArray();
        $this->assertSame(
            [
                'actor' => self::ACTOR,
                'site' => 'default',
                'organization' => 'acme',
                'workspace' => 'finance',
                'method' => 'totp',
                'verified_at' => '2026-01-01T10:00:00+00:00',
                'expires_at' => '2026-01-01T10:05:00+00:00',
                'purpose' => 'records.delete',
                'security_epoch' => 1,
            ],
            $export,
            'The documented shape, in UTC.',
        );
        $encoded = json_encode($export, JSON_THROW_ON_ERROR);
        $this->assertStringExcludes(self::NONCE, $encoded, 'The nonce is never exported.');
        $this->assertStringExcludes('session-77', $encoded, 'The session identity is never exported.');
    }

    /**
     * The proof is final and readonly.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testIsImmutable(): void
    {
        $reflection = new ReflectionClass(StepUpProof::class);
        $this->assertTrue($reflection->isFinal() && $reflection->isReadOnly(), 'Final and readonly.');
    }
}
