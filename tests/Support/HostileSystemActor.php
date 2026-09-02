<?php

/**
 * Test-scoped system actor double that returns whatever identifier a test wants to smuggle in.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Support;

use Kumwe\Context\Contract\SystemActor;

/**
 * Actor whose identifier is a constructor input, for hostile-input tests.
 *
 * @since  0.1.0
 */
final readonly class HostileSystemActor implements SystemActor
{
    /**
     * Hold the identifier to present.
     *
     * @param  string  $identifier  Token to return, valid or not.
     *
     * @since  0.1.0
     */
    public function __construct(private string $identifier)
    {
    }

    /**
     * Return the identifier as given.
     *
     * @return  string  Token, unvalidated.
     *
     * @since   0.1.0
     */
    public function identifier(): string
    {
        return $this->identifier;
    }
}
