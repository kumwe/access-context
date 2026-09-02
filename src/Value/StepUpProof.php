<?php

declare(strict_types=1);

namespace Kumwe\Context\Value;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Kumwe\Context\Exception\InvalidContext;

/**
 * Fresh, single-session proof produced by a successful multi-factor challenge.
 *
 * A proof is not authority by itself. A host's high-impact policy verifies its actor, rotated session, site,
 * organization, workspace, method, freshness and nonce before consuming the action or approval in one
 * transaction; `ExecutionContext` refuses a multi-factor context whose proof does not match the actor, session,
 * scope and epoch it carries. A proof is valid for at most fifteen minutes after verification and never before it.
 *
 * @since  0.1.0
 */
final readonly class StepUpProof
{
    /**
     * Longest freshness interval a provider may claim, applied to `verifiedAt` to bound `expiresAt`.
     *
     * @var    string
     * @since  0.1.0
     */
    private const MAXIMUM_FRESHNESS = '+15 minutes';

    /**
     * Validate a proof issued by the host's step-up provider.
     *
     * @param   string                $actorId        Principal subject bound to the proof.
     * @param   string                $sessionId      Rotated session identifier bound to the proof.
     * @param   SiteContext           $site           Exact site for which it is valid.
     * @param   ?OrganizationContext  $organization   Exact organization, when organization-scoped.
     * @param   string                $method         Provider method such as `totp` or `recovery_code`.
     * @param   DateTimeImmutable     $verifiedAt     Instant of successful verification.
     * @param   DateTimeImmutable     $expiresAt      Exclusive freshness boundary, within fifteen minutes.
     * @param   string                $nonce          Unpredictable proof identity used to prevent replay.
     * @param   ?WorkspaceContext     $workspace      Exact workspace, when the protected operation is narrower.
     * @param   string                $purpose        Narrow protected-operation purpose.
     * @param   int                   $securityEpoch  Actor authorization epoch at verification time.
     *
     * @throws  InvalidContext  When an actor or session identity is empty, over 191 bytes or carries a control
     *          character; when the method is not a lowercase token of 2 to 32 characters; when the freshness
     *          interval is empty, inverted or longer than fifteen minutes; when the nonce is not 32 to 128
     *          URL-safe characters; when the purpose is not a lowercase dotted identifier of at most 127
     *          characters; or when the epoch is below one.
     *
     * @since   0.1.0
     */
    public function __construct(
        private string $actorId,
        private string $sessionId,
        private SiteContext $site,
        private ?OrganizationContext $organization,
        private string $method,
        private DateTimeImmutable $verifiedAt,
        private DateTimeImmutable $expiresAt,
        private string $nonce,
        private ?WorkspaceContext $workspace = null,
        private string $purpose = 'legacy.step_up',
        private int $securityEpoch = 1,
    ) {
        foreach (['actor' => $actorId, 'session' => $sessionId] as $name => $value) {
            if ($value === '' || strlen($value) > 191 || preg_match('/[\x00-\x1F\x7F]/', $value) === 1) {
                throw new InvalidContext(sprintf('The step-up %s identity is invalid.', $name));
            }
        }
        if (preg_match('/^[a-z][a-z0-9_-]{1,31}$/D', $method) !== 1) {
            throw new InvalidContext('The step-up method is invalid.');
        }
        if ($expiresAt <= $verifiedAt || $expiresAt > $verifiedAt->modify(self::MAXIMUM_FRESHNESS)) {
            throw new InvalidContext('The step-up freshness interval is invalid.');
        }
        if (preg_match('/^[A-Za-z0-9_-]{32,128}$/D', $nonce) !== 1) {
            throw new InvalidContext('The step-up proof nonce is invalid.');
        }
        if (preg_match('/^[a-z][a-z0-9._:-]{0,126}$/D', $purpose) !== 1) {
            throw new InvalidContext('The step-up proof purpose is invalid.');
        }
        if ($securityEpoch < 1) {
            throw new InvalidContext('The step-up proof security epoch must be positive.');
        }
    }

    /**
     * Return the bound actor identity.
     *
     * @return  string  Principal subject the proof was issued for.
     *
     * @since   0.1.0
     */
    public function actorId(): string
    {
        return $this->actorId;
    }

    /**
     * Return the rotated session identity the proof is bound to.
     *
     * @return  string  Session identifier; a proof never outlives the session it was issued in.
     *
     * @since   0.1.0
     */
    public function sessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * Return the bound site.
     *
     * @return  SiteContext  Exact site the proof is valid for.
     *
     * @since   0.1.0
     */
    public function site(): SiteContext
    {
        return $this->site;
    }

    /**
     * Return the bound organization.
     *
     * @return  ?OrganizationContext  Exact organization, or null for a proof outside an organization scope.
     *
     * @since   0.1.0
     */
    public function organization(): ?OrganizationContext
    {
        return $this->organization;
    }

    /**
     * Return the verified provider method.
     *
     * @return  string  Lowercase provider token such as `totp`.
     *
     * @since   0.1.0
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Return the verification instant.
     *
     * @return  DateTimeImmutable  Instant the challenge succeeded; the proof is not valid before it.
     *
     * @since   0.1.0
     */
    public function verifiedAt(): DateTimeImmutable
    {
        return $this->verifiedAt;
    }

    /**
     * Return the freshness boundary.
     *
     * @return  DateTimeImmutable  Exclusive instant from which the proof is stale.
     *
     * @since   0.1.0
     */
    public function expiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * Return the replay nonce.
     *
     * @return  string  Unpredictable proof identity a host consumes exactly once.
     *
     * @since   0.1.0
     */
    public function nonce(): string
    {
        return $this->nonce;
    }

    /**
     * Return the bound workspace.
     *
     * @return  ?WorkspaceContext  Exact workspace, or null when the proof covers the whole organization.
     *
     * @since   0.1.0
     */
    public function workspace(): ?WorkspaceContext
    {
        return $this->workspace;
    }

    /**
     * Return the protected-operation purpose.
     *
     * @return  string  Lowercase dotted purpose the challenge was issued for.
     *
     * @since   0.1.0
     */
    public function purpose(): string
    {
        return $this->purpose;
    }

    /**
     * Return the actor authorization epoch at verification time.
     *
     * @return  int  Positive epoch; a context refuses a proof whose epoch differs from its principal's.
     *
     * @since   0.1.0
     */
    public function securityEpoch(): int
    {
        return $this->securityEpoch;
    }

    /**
     * Test every binding and freshness boundary for a high-impact decision.
     *
     * Identity comparisons are constant-time. The proof is valid from `verifiedAt` inclusive to `expiresAt`
     * exclusive; the caller supplies trusted time rather than the proof reading a clock.
     *
     * @param   string                $actorId       Expected actor.
     * @param   string                $sessionId     Current rotated session.
     * @param   SiteContext           $site          Current site.
     * @param   ?OrganizationContext  $organization  Current organization, or null outside one.
     * @param   DateTimeImmutable     $now           Current trusted time.
     *
     * @return  bool  True only while every exact binding still matches and the proof is fresh.
     *
     * @since   0.1.0
     */
    public function isValidFor(
        string $actorId,
        string $sessionId,
        SiteContext $site,
        ?OrganizationContext $organization,
        DateTimeImmutable $now,
    ): bool {
        return hash_equals($this->actorId, $actorId)
            && hash_equals($this->sessionId, $sessionId)
            && $this->site->equals($site)
            && $this->organization?->identifier() === $organization?->identifier()
            && $now >= $this->verifiedAt
            && $now < $this->expiresAt;
    }

    /**
     * Export the proof's non-secret facts for logging or an audit record.
     *
     * The nonce and the session identity are deliberately absent: the nonce is the replay secret a host consumes,
     * and the session identity is redacted so an exported proof cannot be correlated with a live session.
     *
     * @return  array{
     *              actor: string,
     *              site: string,
     *              organization: ?string,
     *              workspace: ?string,
     *              method: string,
     *              verified_at: string,
     *              expires_at: string,
     *              purpose: string,
     *              security_epoch: int
     *          }  Instants are rendered in UTC as RFC 3339 with a `Z` designator.
     *
     * @since   0.1.0
     */
    public function toArray(): array
    {
        $utc = new DateTimeZone('UTC');

        return [
            'actor' => $this->actorId,
            'site' => $this->site->identifier(),
            'organization' => $this->organization?->identifier(),
            'workspace' => $this->workspace?->identifier(),
            'method' => $this->method,
            'verified_at' => $this->verifiedAt->setTimezone($utc)->format(DateTimeInterface::RFC3339),
            'expires_at' => $this->expiresAt->setTimezone($utc)->format(DateTimeInterface::RFC3339),
            'purpose' => $this->purpose,
            'security_epoch' => $this->securityEpoch,
        ];
    }
}
