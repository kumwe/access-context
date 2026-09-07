<?php

/**
 * Proves the closed strength vocabulary, its backing values and its credential-presence ordering.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Case;

use Kumwe\Context\Tests\TestCase;
use Kumwe\Context\Value\AuthenticationStrength;

/**
 * Authentication strength cases, human/system split and ordering matrix.
 *
 * @since  0.1.0
 */
final class AuthenticationStrengthTest extends TestCase
{
    /**
     * Exactly four cases exist with the stable backing values hosts store and audit.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testCasesAndBackingValuesAreStable(): void
    {
        $values = array_map(
            static fn (AuthenticationStrength $case): string => $case->value,
            AuthenticationStrength::cases(),
        );
        $this->assertSame(['password', 'bearer_token', 'multi_factor', 'system'], $values, 'Four stable values.');
        $this->assertSame(
            AuthenticationStrength::MultiFactor,
            AuthenticationStrength::from('multi_factor'),
            'Backed round trip.',
        );
        $this->assertNull(AuthenticationStrength::tryFrom('MULTI_FACTOR'), 'Values are exact, not normalised.');
    }

    /**
     * Every strength but System belongs to a person.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testHumanStrengthsAreEverythingButSystem(): void
    {
        $this->assertTrue(AuthenticationStrength::Password->isHuman(), 'Password is human.');
        $this->assertTrue(AuthenticationStrength::BearerToken->isHuman(), 'Bearer token is human.');
        $this->assertTrue(AuthenticationStrength::MultiFactor->isHuman(), 'Multi-factor is human.');
        $this->assertFalse(AuthenticationStrength::System->isHuman(), 'System is not human.');
    }

    /**
     * The ordering is BearerToken < Password < MultiFactor, with System comparable only to itself.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testOrderingMatrix(): void
    {
        $bearer = AuthenticationStrength::BearerToken;
        $password = AuthenticationStrength::Password;
        $multi = AuthenticationStrength::MultiFactor;
        $system = AuthenticationStrength::System;

        $expected = [
            [$bearer, $bearer, true], [$bearer, $password, false], [$bearer, $multi, false], [$bearer, $system, false],
            [$password, $bearer, true], [$password, $password, true], [$password, $multi, false],
            [$password, $system, false],
            [$multi, $bearer, true], [$multi, $password, true], [$multi, $multi, true], [$multi, $system, false],
            [$system, $bearer, false], [$system, $password, false], [$system, $multi, false], [$system, $system, true],
        ];
        foreach ($expected as [$actual, $required, $satisfies]) {
            $this->assertSame(
                $satisfies,
                $actual->satisfies($required),
                sprintf('%s satisfies %s must be %s.', $actual->name, $required->name, var_export($satisfies, true)),
            );
        }
    }
}
