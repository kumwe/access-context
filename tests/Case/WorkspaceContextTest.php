<?php

/**
 * Proves what a workspace identifier admits and refuses.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Case;

use Kumwe\Context\Tests\TestCase;
use Kumwe\Context\Value\OrganizationContext;
use Kumwe\Context\Value\WorkspaceContext;
use ReflectionClass;
use TypeError;

/**
 * Workspace context grammar, normalisation, equality and type distinction.
 *
 * @since  0.1.0
 */
final class WorkspaceContextTest extends TestCase
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
                    static fn (): WorkspaceContext => WorkspaceContext::fromString($identifier),
                    'A workspace context must be a valid non-empty identifier.',
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
        $this->assertSame('finance', WorkspaceContext::fromString(' Finance ')->identifier(), 'Trim and lowercase.');
        $this->assertSame(
            'fin.ops_2:a-b',
            WorkspaceContext::fromString('Fin.Ops_2:A-B')->identifier(),
            'The documented alphabet is admitted.',
        );
    }

    /**
     * Hostile identifiers are refused with the workspace rule named.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testRefusesHostileIdentifiers(): void
    {
        foreach (['', '.finance', "fin\x1fance", 'fin ance', 'f' . str_repeat('i', 191), 'финансы'] as $value) {
            $this->assertRefused(
                static fn (): WorkspaceContext => WorkspaceContext::fromString($value),
                'A workspace context must be a valid non-empty identifier.',
                'The identifier ' . json_encode($value) . ' must be refused.',
            );
        }
    }

    /**
     * Equality is by normalised identifier; an organization is never accepted for comparison.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testEqualityAndDistinctionFromOrganization(): void
    {
        $this->assertTrue(
            WorkspaceContext::fromString('Finance')->equals(WorkspaceContext::fromString('finance')),
            'Same workspace.',
        );
        $this->assertFalse(
            WorkspaceContext::fromString('finance')->equals(WorkspaceContext::fromString('legal')),
            'Other workspace.',
        );
        $workspace = WorkspaceContext::fromString('acme');
        $organization = OrganizationContext::fromString('acme');
        $this->assertThrows(
            static fn (): mixed => (new \ReflectionMethod($workspace, 'equals'))->invoke($workspace, $organization),
            TypeError::class,
            'A workspace cannot be compared with an organization.',
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
        $reflection = new ReflectionClass(WorkspaceContext::class);
        $this->assertTrue($reflection->isFinal() && $reflection->isReadOnly(), 'Final and readonly.');
    }
}
