<?php

declare(strict_types=1);

namespace Kumwe\Context\Value;

/**
 * Kind of credential that proved the identity carried by an execution context.
 *
 * `ExecutionContext` stores this beside the actor and treats it as a consistency check rather than decoration:
 * a context built around a `SystemActor` must declare `System`, and a context built around a human principal
 * must not, so a background worker can never be mistaken for a signed-in operator. The backing value is what a
 * host writes into an audit record and mixes into the context's authorization fingerprint, so a stored
 * idempotent result cannot be replayed by a caller who authenticated differently from the one that produced it.
 *
 * @since  0.1.0
 */
enum AuthenticationStrength: string
{
    /**
     * A human proved themselves with their account password in this exchange.
     *
     * Covers an interactive login, the cookie-backed session it establishes, and console work that reads an
     * operator's password from a secret file.
     *
     * @since  0.1.0
     */
    case Password = 'password';

    /**
     * A human principal was resolved from an issued access token presented as a bearer credential.
     *
     * The actor is still a person, but no primary credential was offered in this exchange.
     *
     * @since  0.1.0
     */
    case BearerToken = 'bearer_token';

    /**
     * A signed-in human completed a fresh multi-factor challenge in the current rotated session.
     *
     * The accompanying `StepUpProof`, rather than this label alone, supplies the actor, session, scope, method
     * and freshness bindings a high-impact action must verify.
     *
     * @since  0.1.0
     */
    case MultiFactor = 'multi_factor';

    /**
     * No human is behind the request: the context was issued to a trusted in-process actor.
     *
     * Reserved for `ExecutionContext::issueSystem()`, whose authority comes from the host's choice of actor
     * rather than from a credential presented in this exchange.
     *
     * @since  0.1.0
     */
    case System = 'system';

    /**
     * Whether a person, rather than a trusted in-process actor, proved itself with this strength.
     *
     * @return  bool  True for every case except `System`.
     *
     * @since   0.1.0
     */
    public function isHuman(): bool
    {
        return $this !== self::System;
    }

    /**
     * Whether this strength meets a required minimum under the credential-presence ordering.
     *
     * Human strengths are ordered by what was presented in the current exchange: `BearerToken` presents only a
     * derived credential, `Password` presents the primary credential, and `MultiFactor` presents the primary
     * credential plus a fresh second factor, so `BearerToken` < `Password` < `MultiFactor`. `System` stands
     * outside that order: it satisfies only `System`, and no human strength satisfies it, so a requirement can
     * never be met by the wrong kind of actor.
     *
     * @param   self  $required  Minimum strength a policy demands.
     *
     * @return  bool  True when this strength is the required one or a stronger human strength.
     *
     * @since   0.1.0
     */
    public function satisfies(self $required): bool
    {
        if ($this === self::System || $required === self::System) {
            return $this === $required;
        }

        return $this->rank() >= $required->rank();
    }

    /**
     * Position of a human strength in the credential-presence ordering.
     *
     * @return  int  1 for `BearerToken`, 2 for `Password`, 3 for `MultiFactor`; `System` is unranked at 0.
     *
     * @since   0.1.0
     */
    private function rank(): int
    {
        return match ($this) {
            self::System => 0,
            self::BearerToken => 1,
            self::Password => 2,
            self::MultiFactor => 3,
        };
    }
}
