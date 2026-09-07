<?php

/**
 * Proves the closed surface vocabulary and its backing values.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Case;

use Kumwe\Context\Tests\TestCase;
use Kumwe\Context\Value\AuthenticatedSurface;

/**
 * Authenticated surface cases and values.
 *
 * @since  0.1.0
 */
final class AuthenticatedSurfaceTest extends TestCase
{
    /**
     * Exactly seven surfaces exist with the stable backing values hosts store and audit.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testCasesAndBackingValuesAreStable(): void
    {
        $values = array_map(
            static fn (AuthenticatedSurface $case): string => $case->value,
            AuthenticatedSurface::cases(),
        );
        $this->assertSame(
            ['administrator', 'portal', 'api', 'mcp', 'cli', 'background', 'recovery'],
            $values,
            'Seven stable values in declaration order.',
        );
        $this->assertSame(AuthenticatedSurface::Background, AuthenticatedSurface::from('background'), 'Round trip.');
        $this->assertNull(AuthenticatedSurface::tryFrom('Background'), 'Values are exact, not normalised.');
        $this->assertNull(AuthenticatedSurface::tryFrom('web'), 'No surface is inferred from a foreign label.');
    }
}
