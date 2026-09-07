<?php

/**
 * Proves the execution context's invariants: one explicit actor, agreeing strength and surface, bound proof,
 * validated identifiers, provenance by identity, immutable derivation, authority fingerprints and a redacted
 * export.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Case;

use DateTimeImmutable;
use Kumwe\Context\Tests\Support\FakePrincipal;
use Kumwe\Context\Tests\Support\FakeSystemActor;
use Kumwe\Context\Tests\Support\HostileSystemActor;
use Kumwe\Context\Tests\TestCase;
use Kumwe\Context\Value\AuthenticatedSurface;
use Kumwe\Context\Value\AuthenticationStrength;
use Kumwe\Context\Value\ExecutionContext;
use Kumwe\Context\Value\MembershipContext;
use Kumwe\Context\Value\OrganizationContext;
use Kumwe\Context\Value\SiteContext;
use Kumwe\Context\Value\StepUpProof;
use Kumwe\Context\Value\WorkspaceContext;
use ReflectionClass;
use stdClass;

/**
 * Execution context behaviour.
 *
 * @since  0.1.0
 */
final class ExecutionContextTest extends TestCase
{
    /**
     * Add the standard multi-factor arguments to a named fixture override.
     *
     * @param   array<string, mixed>  $overrides  Named context arguments to replace.
     *
     * @return  array<string, mixed>  Named arguments for the human factory fixture.
     *
     * @since   0.1.0
     */
    private function multi(array $overrides): array
    {
        return array_replace([
            'authenticationStrength' => AuthenticationStrength::MultiFactor,
            'sessionId' => 'session-77',
        ], $overrides);
    }

    /**
     * Actor subject used across the case.
     *
     * @var    string
     * @since  0.1.0
     */
    private const SUBJECT = '018f22e2-7c8b-7ab0-8f3a-88e8026bb301';

    /**
     * Membership row identity used across the case.
     *
     * @var    string
     * @since  0.1.0
     */
    private const ROW = '018f22e2-7c8b-7ab0-8f3a-88e8026bb302';

    /**
     * One provenance object shared by the case, as a host's composition root would hold.
     *
     * @var    object
     * @since  0.1.0
     */
    private object $provenance;

    /**
     * Prepare the shared provenance.
     *
     * @since  0.1.0
     */
    public function __construct()
    {
        $this->provenance = new stdClass();
    }

    /**
     * Build a membership snapshot for the case.
     *
     * @param   string   $organization  Organization identifier.
     * @param   ?string  $workspace     Workspace identifier, or null.
     *
     * @return  MembershipContext  Snapshot at version 1, generation 1.
     *
     * @since   0.1.0
     */
    private function membership(string $organization = 'acme', ?string $workspace = null): MembershipContext
    {
        return new MembershipContext(
            self::ROW,
            OrganizationContext::fromString($organization),
            $workspace === null ? null : WorkspaceContext::fromString($workspace),
            1,
            1,
        );
    }

    /**
     * Build a proof bound to the case's actor and a session, overriding any argument by name.
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
            'actorId' => self::SUBJECT,
            'sessionId' => 'session-77',
            'site' => SiteContext::default(),
            'organization' => null,
            'method' => 'totp',
            'verifiedAt' => $verifiedAt,
            'expiresAt' => $verifiedAt->modify('+5 minutes'),
            'nonce' => str_repeat('n', 32),
            'workspace' => null,
            'purpose' => 'records.delete',
            'securityEpoch' => 1,
        ];
        /** @var array<string, mixed> $arguments */
        $arguments = array_replace($arguments, $overrides);

        return (new ReflectionClass(StepUpProof::class))->newInstanceArgs($arguments);
    }

    /**
     * Issue a human context for the case, overriding any named argument.
     *
     * @param   array<string, mixed>  $overrides  Named arguments of `issueHuman()` to replace.
     *
     * @return  ExecutionContext  The context.
     *
     * @since   0.1.0
     */
    private function human(array $overrides = []): ExecutionContext
    {
        $arguments = [
            'provenance' => $this->provenance,
            'principal' => new FakePrincipal($this->provenance),
            'site' => SiteContext::default(),
            'authenticationStrength' => AuthenticationStrength::BearerToken,
            'requestId' => 'req-0001',
        ];
        /** @var array<string, mixed> $arguments */
        $arguments = array_replace($arguments, $overrides);

        $context = (new \ReflectionMethod(ExecutionContext::class, 'issueHuman'))->invokeArgs(null, $arguments);
        if (!$context instanceof ExecutionContext) {
            throw new \RuntimeException('The context factory returned an invalid type.');
        }

        return $context;
    }

    /**
     * A human context carries exactly the explicit facts it was issued with.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testHumanContextCarriesExplicitFacts(): void
    {
        $principal = new FakePrincipal($this->provenance);
        $membership = $this->membership('acme', 'finance');
        $context = ExecutionContext::issueHuman(
            $this->provenance,
            $principal,
            SiteContext::fromString('shop'),
            AuthenticationStrength::Password,
            'req-0001',
            'trace-0001',
            AuthenticatedSurface::Portal,
            $membership,
            'session-77',
        );

        $this->assertSame($principal, $context->principal(), 'The very principal.');
        $this->assertNull($context->systemActor(), 'No system actor.');
        $this->assertFalse($context->isSystem(), 'Human.');
        $this->assertSame(self::SUBJECT, $context->actorId(), 'Actor is the subject.');
        $this->assertSame('shop', $context->site()->identifier(), 'Site.');
        $this->assertSame(AuthenticationStrength::Password, $context->authenticationStrength(), 'Strength.');
        $this->assertSame(AuthenticatedSurface::Portal, $context->surface(), 'Surface.');
        $this->assertSame('req-0001', $context->requestId(), 'Request.');
        $this->assertSame('trace-0001', $context->correlationId(), 'Correlation.');
        $this->assertSame($membership, $context->membership(), 'Membership.');
        $this->assertSame('acme', $context->organization()?->identifier(), 'Organization via membership.');
        $this->assertSame('finance', $context->workspace()?->identifier(), 'Workspace via membership.');
        $this->assertSame('session-77', $context->sessionId(), 'Session.');
        $this->assertNull($context->stepUpProof(), 'No proof.');
    }

    /**
     * Correlation defaults to the request, and the surface is derived from the strength alone when omitted.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testDefaultsDeriveOnlyFromSuppliedFacts(): void
    {
        $bearer = $this->human();
        $this->assertSame('req-0001', $bearer->correlationId(), 'Correlation defaults to the request.');
        $this->assertSame(AuthenticatedSurface::Api, $bearer->surface(), 'A bearer token enters through the API.');
        $password = $this->human(['authenticationStrength' => AuthenticationStrength::Password]);
        $this->assertSame(AuthenticatedSurface::Administrator, $password->surface(), 'A password: administrator.');
        $multi = $this->human([
            'authenticationStrength' => AuthenticationStrength::MultiFactor,
            'sessionId' => 'session-77',
            'stepUpProof' => $this->proof(),
        ]);
        $this->assertSame(AuthenticatedSurface::Administrator, $multi->surface(), 'Multi-factor: administrator.');
        $explicit = $this->human(['surface' => AuthenticatedSurface::Mcp]);
        $this->assertSame(AuthenticatedSurface::Mcp, $explicit->surface(), 'An explicit surface wins.');
    }

    /**
     * Impossible combinations of identity, strength and surface are refused.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testImpossibleCombinationsAreRefused(): void
    {
        $this->assertRefused(
            fn (): ExecutionContext => $this->human(['authenticationStrength' => AuthenticationStrength::System]),
            'A human execution context cannot use system authentication.',
            'A person cannot authenticate as the system.',
        );
        $this->assertRefused(
            fn (): ExecutionContext => $this->human(['authenticationStrength' => AuthenticationStrength::MultiFactor]),
            'Multi-factor authentication requires exactly one step-up proof.',
            'Multi-factor without a proof.',
        );
        $this->assertRefused(
            fn (): ExecutionContext => $this->human(['sessionId' => 'session-77', 'stepUpProof' => $this->proof()]),
            'Multi-factor authentication requires exactly one step-up proof.',
            'A proof without multi-factor strength.',
        );
    }

    /**
     * A principal from another authority, or one presenting hostile claims, is refused.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testForeignOrHostilePrincipalsAreRefused(): void
    {
        $this->assertRefused(
            fn (): ExecutionContext => $this->human(['principal' => new FakePrincipal(new stdClass())]),
            'A human context requires a principal from the same authority.',
            'A principal vouched for elsewhere.',
        );
        foreach (['', str_repeat('s', 192), "sub\x00ject", "sub\x1fject", "\xff"] as $subject) {
            $this->assertRefused(
                fn (): ExecutionContext => $this->human([
                    'principal' => new FakePrincipal($this->provenance, $subject),
                ]),
                'The principal subject identity is invalid.',
                'Subject ' . json_encode($subject) . ' must be refused.',
            );
        }
        $this->assertRefused(
            fn (): ExecutionContext => $this->human([
                'principal' => new FakePrincipal($this->provenance, self::SUBJECT, 0),
            ]),
            'A principal security epoch must be positive.',
            'A zero epoch.',
        );
    }

    /**
     * Request, correlation and session identifiers follow the opaque identifier grammar.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testIdentifierGrammar(): void
    {
        $hostile = ['', str_repeat('r', 192), "req\x00", "req\n", "\x7f", "\xff", "bad\xc3"];
        foreach ($hostile as $value) {
            $this->assertRefused(
                fn (): ExecutionContext => $this->human(['requestId' => $value]),
                'The request identity is invalid.',
                'Request ' . json_encode($value) . ' must be refused.',
            );
            $this->assertRefused(
                fn (): ExecutionContext => $this->human(['correlationId' => $value]),
                'The correlation identity is invalid.',
                'Correlation ' . json_encode($value) . ' must be refused.',
            );
            $this->assertRefused(
                fn (): ExecutionContext => $this->human(['sessionId' => $value]),
                'The session identity is invalid.',
                'Session ' . json_encode($value) . ' must be refused.',
            );
        }
        $context = $this->human([
            'requestId' => str_repeat('r', 191),
            'correlationId' => 'trace ünïcode ok',
            'sessionId' => str_repeat('s', 191),
        ]);
        $this->assertSame('trace ünïcode ok', $context->correlationId(), 'Printable text of any script is fine.');
    }

    /**
     * A system context is explicit, confined to system strength and the background surface, and never human.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testSystemContextIsExplicitAndConfined(): void
    {
        $context = ExecutionContext::issueSystem(
            $this->provenance,
            FakeSystemActor::Worker,
            SiteContext::fromString('shop'),
            'job-17',
        );

        $this->assertTrue($context->isSystem(), 'System.');
        $this->assertSame(FakeSystemActor::Worker, $context->systemActor(), 'The very enum case.');
        $this->assertNull($context->principal(), 'No principal.');
        $this->assertSame('system:worker', $context->actorId(), 'Actor is the identifier.');
        $this->assertSame(AuthenticationStrength::System, $context->authenticationStrength(), 'System strength.');
        $this->assertSame(AuthenticatedSurface::Background, $context->surface(), 'Background surface.');
        $this->assertSame('job-17', $context->correlationId(), 'Correlation defaults to the request.');
        $this->assertNull($context->membership(), 'No membership.');
        $this->assertNull($context->organization(), 'No organization.');
        $this->assertNull($context->sessionId(), 'No session.');
        $this->assertNull($context->stepUpProof(), 'No proof.');
        $this->assertTrue($context->hasProvenance($this->provenance), 'Trusted by its issuer.');
        $this->assertFalse($context->hasProvenance(new stdClass()), 'Not by anyone else.');

        $child = $context->child('job-17.step-2', 'trace-9');
        $this->assertSame(FakeSystemActor::Worker, $child->systemActor(), 'The child keeps the actor.');
        $this->assertSame(AuthenticatedSurface::Background, $child->surface(), 'And the surface.');
        $this->assertSame('trace-9', $child->correlationId(), 'With the new correlation.');
    }

    /**
     * A system actor whose identifier is empty, oversized or unprintable is refused.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testHostileSystemActorIsRefused(): void
    {
        foreach (['', str_repeat('s', 192), "system:\x00worker", "system:\nworker", "\xff"] as $identifier) {
            $this->assertRefused(
                fn (): ExecutionContext => ExecutionContext::issueSystem(
                    $this->provenance,
                    new HostileSystemActor($identifier),
                    SiteContext::default(),
                    'job-1',
                ),
                'The system actor identity is invalid.',
                'Identifier ' . json_encode($identifier) . ' must be refused.',
            );
        }
        $this->assertRefused(
            fn (): ExecutionContext => ExecutionContext::issueSystem(
                $this->provenance,
                FakeSystemActor::Migration,
                SiteContext::default(),
                '',
            ),
            'The request identity is invalid.',
            'A system context validates its request identifier too.',
        );
    }

    /**
     * Absence of organization, workspace, session and proof is explicit null, and membership is the only
     * route to an organization.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testAbsenceIsExplicit(): void
    {
        $bare = $this->human();
        $this->assertNull($bare->membership(), 'No membership.');
        $this->assertNull($bare->organization(), 'No organization.');
        $this->assertNull($bare->workspace(), 'No workspace.');
        $this->assertNull($bare->organizationIdentifier(), 'No organization identifier.');
        $this->assertNull($bare->workspaceIdentifier(), 'No workspace identifier.');
        $this->assertNull($bare->sessionId(), 'No session.');
        $this->assertFalse($bare->toArray()['session_bound'], 'The export says unbound.');

        $wide = $this->human(['membership' => $this->membership('acme')]);
        $this->assertSame('acme', $wide->organizationIdentifier(), 'Organization from membership.');
        $this->assertNull($wide->workspaceIdentifier(), 'Organization-wide work has no workspace.');
    }

    /**
     * A multi-factor proof must match the actor, session, site, organization, workspace and epoch exactly.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testProofMustMatchEveryBinding(): void
    {
        $refused = 'The step-up proof does not match the execution context.';

        $bound = $this->human($this->multi(['stepUpProof' => $this->proof()]));
        $this->assertSame('session-77', $bound->stepUpProof()?->sessionId(), 'A matching proof is carried.');

        $mismatches = [
            'actor' => $this->multi([
                'stepUpProof' => $this->proof(['actorId' => '018f22e2-7c8b-7ab0-8f3a-88e8026bb399']),
            ]),
            'session missing' => $this->multi(['sessionId' => null, 'stepUpProof' => $this->proof()]),
            'session other' => $this->multi(['sessionId' => 'session-78', 'stepUpProof' => $this->proof()]),
            'site' => $this->multi(['site' => SiteContext::fromString('shop'), 'stepUpProof' => $this->proof()]),
            'organization on proof only' => $this->multi([
                'stepUpProof' => $this->proof(['organization' => OrganizationContext::fromString('acme')]),
            ]),
            'organization on membership only' => $this->multi([
                'membership' => $this->membership('acme'),
                'stepUpProof' => $this->proof(),
            ]),
            'organization differs' => $this->multi([
                'membership' => $this->membership('acme'),
                'stepUpProof' => $this->proof(['organization' => OrganizationContext::fromString('other')]),
            ]),
            'workspace' => $this->multi([
                'membership' => $this->membership('acme', 'finance'),
                'stepUpProof' => $this->proof(['organization' => OrganizationContext::fromString('acme')]),
            ]),
            'epoch' => $this->multi(['stepUpProof' => $this->proof(['securityEpoch' => 2])]),
        ];
        foreach ($mismatches as $name => $arguments) {
            $this->assertRefused(
                fn (): ExecutionContext => $this->human($arguments),
                $refused,
                "A proof mismatched on {$name} must be refused.",
            );
        }

        $scoped = $this->human($this->multi([
            'membership' => $this->membership('acme', 'finance'),
            'stepUpProof' => $this->proof([
                'organization' => OrganizationContext::fromString('acme'),
                'workspace' => WorkspaceContext::fromString('finance'),
            ]),
        ]));
        $this->assertSame('finance', $scoped->workspaceIdentifier(), 'A fully bound scoped proof is carried.');
    }

    /**
     * A child keeps every authority fact and changes only its identifiers.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testChildKeepsAuthorityAndChangesOnlyIdentifiers(): void
    {
        $parent = $this->human([
            'correlationId' => 'trace-1',
            'membership' => $this->membership('acme'),
            'sessionId' => 'session-77',
            'surface' => AuthenticatedSurface::Mcp,
        ]);
        $child = $parent->child('req-0002');

        $this->assertNotSame($parent, $child, 'A new instance.');
        $this->assertSame('req-0002', $child->requestId(), 'New request.');
        $this->assertSame('trace-1', $child->correlationId(), 'Inherited correlation.');
        $this->assertSame('req-0001', $parent->requestId(), 'The parent is unchanged.');
        $this->assertSame($parent->principal(), $child->principal(), 'Same principal.');
        $this->assertSame($parent->membership(), $child->membership(), 'Same membership.');
        $this->assertSame($parent->surface(), $child->surface(), 'Same surface.');
        $this->assertSame($parent->sessionId(), $child->sessionId(), 'Same session.');
        $this->assertSame(
            $parent->authorizationFingerprint(),
            $child->authorizationFingerprint(),
            'Identifiers are not authority, so the fingerprint is unchanged.',
        );
        $this->assertRefused(
            static fn (): ExecutionContext => $parent->child(''),
            'The request identity is invalid.',
            'A child validates its identifier.',
        );
    }

    /**
     * The authorization fingerprint moves with every authority fact and is stable otherwise.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testAuthorizationFingerprintCoversAuthorityFacts(): void
    {
        $base = $this->human();
        $this->assertSame($base->authorizationFingerprint(), $this->human()->authorizationFingerprint(), 'Stable.');
        $this->assertTrue(preg_match('/^[a-f0-9]{64}$/', $base->authorizationFingerprint()) === 1, 'SHA-256.');

        $variants = [
            'strength' => $this->human(['authenticationStrength' => AuthenticationStrength::Password]),
            'site' => $this->human(['site' => SiteContext::fromString('shop')]),
            'surface' => $this->human(['surface' => AuthenticatedSurface::Mcp]),
            'membership' => $this->human(['membership' => $this->membership('acme')]),
            'session' => $this->human(['sessionId' => 'session-77']),
            'credential' => $this->human([
                'principal' => new FakePrincipal($this->provenance, self::SUBJECT, 1, 'content.read:global', 'other'),
            ]),
            'epoch' => $this->human(['principal' => new FakePrincipal($this->provenance, self::SUBJECT, 2)]),
            'proof' => $this->human([
                'authenticationStrength' => AuthenticationStrength::MultiFactor,
                'surface' => AuthenticatedSurface::Api,
                'sessionId' => 'session-77',
                'stepUpProof' => $this->proof(),
            ]),
        ];
        foreach ($variants as $fact => $variant) {
            $this->assertNotSame(
                $base->authorizationFingerprint(),
                $variant->authorizationFingerprint(),
                "Changing {$fact} moves the fingerprint.",
            );
        }

        $withSession = $this->human(['sessionId' => 'session-77']);
        $this->assertStringExcludes(
            'session-77',
            $withSession->authorizationFingerprint(),
            'The session identity is digested, never embedded.',
        );
        $system = ExecutionContext::issueSystem(
            $this->provenance,
            FakeSystemActor::Worker,
            SiteContext::default(),
            'j',
        );
        $this->assertNotSame(
            $system->authorizationFingerprint(),
            ExecutionContext::issueSystem($this->provenance, FakeSystemActor::Migration, SiteContext::default(), 'j')
                ->authorizationFingerprint(),
            'System actors are distinguished.',
        );
    }

    /**
     * The approval fingerprint survives a session rotation and a fresh proof but not an authority change.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testApprovalFingerprintIgnoresSessionAndProof(): void
    {
        $before = $this->human([
            'authenticationStrength' => AuthenticationStrength::Password,
            'surface' => AuthenticatedSurface::Administrator,
            'membership' => $this->membership('acme'),
            'sessionId' => 'session-77',
        ]);
        $after = $this->human([
            'authenticationStrength' => AuthenticationStrength::MultiFactor,
            'surface' => AuthenticatedSurface::Administrator,
            'membership' => $this->membership('acme'),
            'sessionId' => 'session-78',
            'stepUpProof' => $this->proof([
                'sessionId' => 'session-78',
                'organization' => OrganizationContext::fromString('acme'),
            ]),
        ]);

        $this->assertSame($before->approvalFingerprint(), $after->approvalFingerprint(), 'Elevation keeps it.');
        $this->assertNotSame($before->authorizationFingerprint(), $after->authorizationFingerprint(), 'Not the other.');
        $this->assertNotSame(
            $before->approvalFingerprint(),
            $this->human(['surface' => AuthenticatedSurface::Api, 'membership' => $this->membership('acme')])
                ->approvalFingerprint(),
            'Another surface breaks it.',
        );
        $this->assertNotSame(
            $before->approvalFingerprint(),
            $this->human([
                'surface' => AuthenticatedSurface::Administrator,
                'membership' => $this->membership('acme'),
                'principal' => new FakePrincipal($this->provenance, self::SUBJECT, 2),
            ])->approvalFingerprint(),
            'Another epoch breaks it.',
        );
    }

    /**
     * Provenance is checked by object identity for the context and its principal together.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testProvenanceIsCheckedByIdentity(): void
    {
        $context = $this->human();
        $this->assertTrue($context->hasProvenance($this->provenance), 'The issuing object.');
        $this->assertFalse($context->hasProvenance(new stdClass()), 'An equal but different object.');
        $this->assertFalse($context->hasProvenance(clone $this->provenance), 'A clone.');
    }

    /**
     * The export carries the documented facts and nothing that could be replayed or correlated.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testExportRedactsSecrets(): void
    {
        $context = $this->human([
            'authenticationStrength' => AuthenticationStrength::MultiFactor,
            'correlationId' => 'trace-1',
            'surface' => AuthenticatedSurface::Administrator,
            'membership' => $this->membership('acme', 'finance'),
            'sessionId' => 'session-77',
            'stepUpProof' => $this->proof([
                'organization' => OrganizationContext::fromString('acme'),
                'workspace' => WorkspaceContext::fromString('finance'),
            ]),
        ]);

        $export = $context->toArray();
        $this->assertSame(
            [
                'actor', 'actor_kind', 'site', 'organization', 'workspace', 'membership', 'authentication_strength',
                'surface', 'request_id', 'correlation_id', 'session_bound', 'step_up',
            ],
            array_keys($export),
            'The documented keys in the documented order.',
        );
        $this->assertSame('human', $export['actor_kind'], 'Kind.');
        $this->assertSame('multi_factor', $export['authentication_strength'], 'Strength value.');
        $this->assertSame('finance', $export['workspace'], 'Workspace.');
        $this->assertTrue($export['session_bound'], 'Session bound.');
        $this->assertSame('records.delete', $export['step_up']['purpose'] ?? null, 'Proof facts.');

        $encoded = json_encode($export, JSON_THROW_ON_ERROR);
        $this->assertStringExcludes('session-77', $encoded, 'No session identity.');
        $this->assertStringExcludes(str_repeat('n', 32), $encoded, 'No nonce.');
        $this->assertStringExcludes('provenance', $encoded, 'No provenance.');
        $this->assertStringExcludes($context->authorizationFingerprint(), $encoded, 'No fingerprint.');

        $system = ExecutionContext::issueSystem(
            $this->provenance,
            FakeSystemActor::Worker,
            SiteContext::default(),
            'j',
        );
        $this->assertSame('system', $system->toArray()['actor_kind'], 'System kind.');
        $this->assertNull($system->toArray()['step_up'], 'No proof for a system context.');
    }

    /**
     * The plain-value accessors disclose exactly the coordinates the context carries.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testPlainAccessorsDiscloseExactlyTheCarriedCoordinates(): void
    {
        $context = $this->human(['membership' => $this->membership('acme', 'finance')]);

        $this->assertSame(SiteContext::DEFAULT, $context->siteIdentifier(), 'Site identifier.');
        $this->assertSame('acme', $context->organizationIdentifier(), 'Organization identifier.');
        $this->assertSame('finance', $context->workspaceIdentifier(), 'Workspace identifier.');
        $this->assertSame('api', $context->deliverySurface(), 'Surface value.');
    }

    /**
     * The context is a final readonly class with no public state; derivation is the only way to change it.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testIsImmutable(): void
    {
        $reflection = new ReflectionClass(ExecutionContext::class);
        $this->assertTrue($reflection->isFinal() && $reflection->isReadOnly(), 'Final and readonly.');
        $this->assertSame([], $reflection->getProperties(\ReflectionProperty::IS_PUBLIC), 'No public property.');
        $this->assertTrue($reflection->getConstructor()?->isPrivate() ?? false, 'Only the factories construct.');
    }
}
