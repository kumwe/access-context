<?php

declare(strict_types=1);

namespace Kumwe\Context\Contract;

/**
 * A human actor that has already proved who it is, seen only through the facts policy needs.
 *
 * The host authenticates and owns the principal's grants, credentials and session; this contract exposes the
 * stable identifier and the claims an execution context and a policy evaluator read, and nothing that would let
 * a consumer authenticate, load authority or reach a session. An implementation is immutable for the life of a
 * unit of work and is vouched for by one provenance object, so a principal assembled anywhere else authorizes
 * nothing. `ExecutionContext::issueHuman()` validates the subject and epoch before trusting an implementation.
 *
 * @since  0.1.0
 */
interface Principal
{
    /**
     * The stable identifier of the person this principal speaks for.
     *
     * @return  string  Non-empty, at most 191 bytes, free of control characters; the host's canonical spelling.
     *
     * @since   0.1.0
     */
    public function subject(): string;

    /**
     * The authorization epoch observed while this principal was authenticated.
     *
     * @return  int  Positive; a host invalidates it whenever the actor's security state changes.
     *
     * @since   0.1.0
     */
    public function securityEpoch(): int;

    /**
     * Whether this principal was vouched for by a particular authority.
     *
     * Comparison is by object identity, never by class or value, so only the very object the authenticating
     * adapter was wired with satisfies it.
     *
     * @param   object  $provenance  Authority object to test this principal against.
     *
     * @return  bool  True only when it is the same instance the principal was issued with.
     *
     * @since   0.1.0
     */
    public function hasProvenance(object $provenance): bool;

    /**
     * Digest of the exact effective authority, bound to the credential that presented it.
     *
     * @return  string  Lowercase hexadecimal SHA-256; changes with the subject, credential, epoch or grant set.
     *
     * @since   0.1.0
     */
    public function authorizationFingerprint(): string;

    /**
     * Digest of the exact effective authority, independent of the credential that presented it.
     *
     * @return  string  Lowercase hexadecimal SHA-256; stable across a session rotation of the same authority.
     *
     * @since   0.1.0
     */
    public function authorityFingerprint(): string;
}
