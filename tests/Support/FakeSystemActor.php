<?php

/**
 * Test-scoped system actor double shaped like a host's closed enum of unattended identities.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Support;

use Kumwe\Context\Contract\SystemActor;

/**
 * Backed enum implementing the contract, proving a host can bind its own closed set without an adapter.
 *
 * @since  0.1.0
 */
enum FakeSystemActor: string implements SystemActor
{
    /**
     * Queue worker.
     *
     * @since  0.1.0
     */
    case Worker = 'system:worker';

    /**
     * Schema migration process.
     *
     * @since  0.1.0
     */
    case Migration = 'system:migration';

    /**
     * Return the backing value as the stable token.
     *
     * @return  string  Case value.
     *
     * @since   0.1.0
     */
    public function identifier(): string
    {
        return $this->value;
    }
}
