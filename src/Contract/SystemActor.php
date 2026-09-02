<?php

declare(strict_types=1);

namespace Kumwe\Context\Contract;

/**
 * An unattended actor a host has explicitly chosen to issue an execution context to.
 *
 * Work that runs with no operator present still has to name who is acting. The host owns the closed set of
 * such actors and the authority each one carries; this contract only makes the choice explicit, so a system
 * context can never be mistaken for a signed-in person and audit records name one stable token per actor.
 * `ExecutionContext::issueSystem()` validates the identifier before trusting an implementation.
 *
 * @since  0.1.0
 */
interface SystemActor
{
    /**
     * The stable token that names this actor in audit records and fingerprints.
     *
     * @return  string  Non-empty, at most 191 bytes, free of control characters, such as `system:worker`.
     *
     * @since   0.1.0
     */
    public function identifier(): string;
}
