<?php

/**
 * Proves what a site identifier admits and refuses, and that a site is never an organization.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Case;

use InvalidArgumentException;
use Kumwe\Context\Exception\InvalidContext;
use Kumwe\Context\Tests\TestCase;
use Kumwe\Context\Value\OrganizationContext;
use Kumwe\Context\Value\SiteContext;
use ReflectionClass;
use TypeError;

/**
 * Site context grammar, normalisation, equality and type distinction.
 *
 * @since  0.1.0
 */
final class SiteContextTest extends TestCase
{
    /**
     * Identifiers are trimmed and lowercased, and the whole documented alphabet is admitted up to 191 characters.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testNormalisesAndAdmitsTheDocumentedGrammar(): void
    {
        $this->assertSame('shop-eu', SiteContext::fromString(' Shop-EU ')->identifier(), 'Trim and lowercase.');
        $this->assertSame('a', SiteContext::fromString('a')->identifier(), 'A single character is enough.');
        $this->assertSame('9', SiteContext::fromString('9')->identifier(), 'A leading digit is admitted.');
        $this->assertSame(
            'a.b_c:d-e',
            SiteContext::fromString('A.B_C:D-E')->identifier(),
            'Dots, underscores, colons and dashes are admitted after the first character.',
        );
        $longest = 'a' . str_repeat('b', 190);
        $this->assertSame($longest, SiteContext::fromString($longest)->identifier(), '191 characters fit.');
    }

    /**
     * Every hostile or malformed identifier is refused with the rule named and the input not echoed.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRefusesHostileIdentifiers(): void
    {
        $hostile = [
            '', ' ', '-abc', '.abc', ':abc', '_abc', 'a b', "a\x00b", "a\nb", "a\x7fb", 'ünïcode', 'a/b', 'a\\b',
            'a' . str_repeat('b', 191), 'shop@eu', 'shop eu', '(shop)', "\u{200B}shop",
        ];
        foreach ($hostile as $identifier) {
            $error = $this->assertThrows(
                static fn (): SiteContext => SiteContext::fromString($identifier),
                InvalidContext::class,
                'The identifier ' . json_encode($identifier) . ' must be refused.',
            );
            $this->assertSame(
                'A site context must be a valid non-empty identifier.',
                $error->getMessage(),
                'The refusal names the rule.',
            );
            $this->assertStringExcludes(
                trim($identifier) === '' ? "\x01" : trim($identifier),
                $error->getMessage(),
                'The refusal never echoes the input.',
            );
        }
    }

    /**
     * The refusal is also an InvalidArgumentException, so existing handlers keep working.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRefusalIsAnInvalidArgumentException(): void
    {
        $this->assertThrows(
            static fn (): SiteContext => SiteContext::fromString(''),
            InvalidArgumentException::class,
            'The package refusal extends the SPL type.',
        );
    }

    /**
     * The default site is spelled by one constant and one factory that agree.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testDefaultSiteIsTheDefaultConstant(): void
    {
        $this->assertSame('default', SiteContext::DEFAULT, 'The literal is stable.');
        $this->assertSame(SiteContext::DEFAULT, SiteContext::default()->identifier(), 'The factory uses it.');
        $this->assertTrue(
            SiteContext::default()->equals(SiteContext::fromString(' DEFAULT ')),
            'A normalised spelling of the default is the default.',
        );
    }

    /**
     * Equality is by normalised identifier and nothing else.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testEqualityIsByNormalisedIdentifier(): void
    {
        $this->assertTrue(SiteContext::fromString('Shop')->equals(SiteContext::fromString('shop ')), 'Same site.');
        $this->assertFalse(SiteContext::fromString('shop')->equals(SiteContext::fromString('shop2')), 'Other site.');
        $this->assertNotSame(SiteContext::fromString('shop'), SiteContext::fromString('shop'), 'Values, not identity.');
    }

    /**
     * A site and an organization with the same spelling are different facts that do not type-check together.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testSiteAndOrganizationAreDistinctTypes(): void
    {
        $site = SiteContext::fromString('acme');
        $organization = OrganizationContext::fromString('acme');

        $this->assertSame($site->identifier(), $organization->identifier(), 'The spelling is shared.');
        $this->assertFalse($site instanceof OrganizationContext, 'A site is not an organization.');
        $this->assertThrows(
            static fn (): bool => $site->equals($organization),
            TypeError::class,
            'Comparing a site with an organization is a type error, not a false.',
        );
    }

    /**
     * The value is a final readonly class with no public state.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testIsImmutable(): void
    {
        $reflection = new ReflectionClass(SiteContext::class);
        $this->assertTrue($reflection->isFinal() && $reflection->isReadOnly(), 'Final and readonly.');
        $this->assertSame([], $reflection->getProperties(\ReflectionProperty::IS_PUBLIC), 'No public property.');
    }
}
