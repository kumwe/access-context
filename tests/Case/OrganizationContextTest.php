<?php

/**
 * Proves what an organization identifier admits and refuses.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Case;

use Kumwe\Context\Exception\InvalidContext;
use Kumwe\Context\Tests\TestCase;
use Kumwe\Context\Value\OrganizationContext;
use Kumwe\Context\Value\SiteContext;
use ReflectionClass;
use TypeError;

/**
 * Organization context grammar, normalisation, equality and type distinction.
 *
 * @since  0.1.0
 */
final class OrganizationContextTest extends TestCase
{
    /**
     * Control bytes cannot disappear through whitespace normalization.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRejectsControlBytesBeforeNormalization(): void
    {
        foreach (["\x00", "\t", "\n", "\r", "\x7f"] as $control) {
            foreach ([$control . 'scope', 'scope' . $control] as $identifier) {
                $this->assertRefused(
                    static fn (): OrganizationContext => OrganizationContext::fromString($identifier),
                    'An organization context must be a valid non-empty identifier.',
                    'Control bytes must be rejected before normalization.',
                );
            }
        }
    }

    /**
     * Identifiers are trimmed and lowercased under the shared scope grammar.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testNormalisesAndAdmitsTheDocumentedGrammar(): void
    {
        $this->assertSame('acme', OrganizationContext::fromString(' ACME ')->identifier(), 'Trim and lowercase.');
        $this->assertSame(
            'acme.eu_1:x-y',
            OrganizationContext::fromString('acme.EU_1:x-y')->identifier(),
            'The documented alphabet is admitted.',
        );
        $longest = '1' . str_repeat('z', 190);
        $this->assertSame($longest, OrganizationContext::fromString($longest)->identifier(), '191 characters fit.');
    }

    /**
     * Hostile identifiers are refused with the organization rule named.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRefusesHostileIdentifiers(): void
    {
        foreach (['', '-acme', "acme\x00", 'acme corp', 'a' . str_repeat('c', 191), 'ácme', 'acme/eu'] as $value) {
            $this->assertRefused(
                static fn (): OrganizationContext => OrganizationContext::fromString($value),
                'An organization context must be a valid non-empty identifier.',
                'The identifier ' . json_encode($value) . ' must be refused.',
            );
        }
    }

    /**
     * Equality is by normalised identifier; a site is never accepted for comparison.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testEqualityAndDistinctionFromSite(): void
    {
        $this->assertTrue(
            OrganizationContext::fromString('Acme')->equals(OrganizationContext::fromString('acme')),
            'Same organization.',
        );
        $this->assertFalse(
            OrganizationContext::fromString('acme')->equals(OrganizationContext::fromString('acme2')),
            'Other organization.',
        );
        $organization = OrganizationContext::fromString('acme');
        $site = SiteContext::fromString('acme');
        $this->assertThrows(
            static fn (): mixed => (new \ReflectionMethod($organization, 'equals'))->invoke($organization, $site),
            TypeError::class,
            'An organization cannot be compared with a site.',
        );
        $this->assertFalse(
            (new ReflectionClass(SiteContext::class))->isInstance($organization),
            'An organization is not a site.',
        );
    }

    /**
     * The value is final and readonly.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testIsImmutable(): void
    {
        $reflection = new ReflectionClass(OrganizationContext::class);
        $this->assertTrue($reflection->isFinal() && $reflection->isReadOnly(), 'Final and readonly.');
    }

    /**
     * The refusal type is the package's own.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRefusalType(): void
    {
        $this->assertThrows(
            static fn (): OrganizationContext => OrganizationContext::fromString(' '),
            InvalidContext::class,
            'The refusal is an InvalidContext.',
        );
    }
}
