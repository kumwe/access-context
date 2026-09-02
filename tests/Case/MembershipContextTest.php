<?php

/**
 * Proves what a membership snapshot admits, what its fingerprint covers, and how absence is exported.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Case;

use Kumwe\Context\Tests\TestCase;
use Kumwe\Context\Value\MembershipContext;
use Kumwe\Context\Value\OrganizationContext;
use Kumwe\Context\Value\WorkspaceContext;
use ReflectionClass;

/**
 * Membership snapshot validation, fingerprinting, equality and export.
 *
 * @since  0.1.0
 */
final class MembershipContextTest extends TestCase
{
    /**
     * Canonical row identity used across the case.
     *
     * @var    string
     * @since  0.1.0
     */
    private const ROW = '018f22e2-7c8b-7ab0-8f3a-88e8026bb302';

    /**
     * A valid snapshot exposes every fact it was built from.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testExposesEveryFact(): void
    {
        $membership = new MembershipContext(
            self::ROW,
            OrganizationContext::fromString('acme'),
            WorkspaceContext::fromString('finance'),
            4,
            2,
        );

        $this->assertSame(self::ROW, $membership->membershipId(), 'Row identity.');
        $this->assertSame('acme', $membership->organization()->identifier(), 'Organization.');
        $this->assertSame('finance', $membership->workspace()?->identifier(), 'Workspace.');
        $this->assertSame(4, $membership->membershipVersion(), 'Membership version.');
        $this->assertSame(2, $membership->policyGeneration(), 'Policy generation.');
    }

    /**
     * The row identity must be a canonical lowercase UUID of version 1 to 8 with an RFC variant.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRefusesMalformedRowIdentity(): void
    {
        $malformed = [
            '018F22E2-7C8B-7AB0-8F3A-88E8026BB302',
            '00000000-0000-0000-0000-000000000000',
            '018f22e2-7c8b-7ab0-cf3a-88e8026bb302',
            '018f22e2-7c8b-9ab0-8f3a-88e8026bb302',
            '018f22e27c8b7ab08f3a88e8026bb302',
            '',
            ' ' . self::ROW,
            self::ROW . "\n",
        ];
        foreach ($malformed as $row) {
            $this->assertRefused(
                static fn (): MembershipContext => new MembershipContext(
                    $row,
                    OrganizationContext::fromString('acme'),
                    null,
                    1,
                    1,
                ),
                'A membership context requires a valid UUID.',
                'The row identity ' . json_encode($row) . ' must be refused.',
            );
        }
    }

    /**
     * Both generations must be positive.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRefusesNonPositiveGenerations(): void
    {
        foreach ([[0, 1], [1, 0], [-1, 1], [1, -1], [0, 0]] as [$version, $generation]) {
            $this->assertRefused(
                static fn (): MembershipContext => new MembershipContext(
                    self::ROW,
                    OrganizationContext::fromString('acme'),
                    null,
                    $version,
                    $generation,
                ),
                'Membership and policy generations must be positive.',
                sprintf('Version %d and generation %d must be refused.', $version, $generation),
            );
        }
    }

    /**
     * The fingerprint covers every fact, equality follows it, and absence of a workspace is explicit.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testFingerprintCoversEveryFactAndAbsenceIsExplicit(): void
    {
        $base = new MembershipContext(self::ROW, OrganizationContext::fromString('acme'), null, 1, 1);
        $same = new MembershipContext(self::ROW, OrganizationContext::fromString('ACME '), null, 1, 1);
        $variants = [
            'row' => new MembershipContext(
                '018f22e2-7c8b-7ab0-8f3a-88e8026bb303',
                OrganizationContext::fromString('acme'),
                null,
                1,
                1,
            ),
            'organization' => new MembershipContext(self::ROW, OrganizationContext::fromString('other'), null, 1, 1),
            'workspace' => new MembershipContext(
                self::ROW,
                OrganizationContext::fromString('acme'),
                WorkspaceContext::fromString('finance'),
                1,
                1,
            ),
            'version' => new MembershipContext(self::ROW, OrganizationContext::fromString('acme'), null, 2, 1),
            'generation' => new MembershipContext(self::ROW, OrganizationContext::fromString('acme'), null, 1, 2),
        ];

        $this->assertSame($base->fingerprint(), $same->fingerprint(), 'Equal facts, equal fingerprint.');
        $this->assertTrue($base->equals($same), 'Equal facts are equal.');
        $this->assertTrue(preg_match('/^[a-f0-9]{64}$/', $base->fingerprint()) === 1, 'Lowercase SHA-256.');
        foreach ($variants as $fact => $variant) {
            $this->assertNotSame($base->fingerprint(), $variant->fingerprint(), "Changing {$fact} moves it.");
            $this->assertFalse($base->equals($variant), "Changing {$fact} breaks equality.");
        }
        $this->assertNull($base->workspace(), 'No workspace is null, not empty.');
        $this->assertNull($base->toArray()['workspace'], 'The export says null explicitly.');
        $this->assertSame(
            hash('sha256', implode("\n", [self::ROW, 'acme', '-', '1', '1'])),
            $base->fingerprint(),
            'The fingerprint composition is the documented one, with `-` for an absent workspace.',
        );
    }

    /**
     * The export is a plain, complete, non-secret record of the snapshot.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testExportIsPlainAndComplete(): void
    {
        $membership = new MembershipContext(
            self::ROW,
            OrganizationContext::fromString('acme'),
            WorkspaceContext::fromString('finance'),
            4,
            2,
        );

        $this->assertSame(
            [
                'membership_id' => self::ROW,
                'organization' => 'acme',
                'workspace' => 'finance',
                'membership_version' => 4,
                'policy_generation' => 2,
            ],
            $membership->toArray(),
            'The export is the documented shape in the documented order.',
        );
        $this->assertTrue(json_encode($membership->toArray()) !== false, 'The export is JSON-encodable.');
    }

    /**
     * The snapshot is final and readonly.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testIsImmutable(): void
    {
        $reflection = new ReflectionClass(MembershipContext::class);
        $this->assertTrue($reflection->isFinal() && $reflection->isReadOnly(), 'Final and readonly.');
    }
}
