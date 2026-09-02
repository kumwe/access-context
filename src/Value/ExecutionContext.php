<?php

declare(strict_types=1);

namespace Kumwe\Context\Value;

use Kumwe\Context\Contract\Principal;
use Kumwe\Context\Contract\SystemActor;
use Kumwe\Context\Exception\InvalidContext;
use LogicException;

/**
 * Immutable envelope naming who is acting, in which site, and under which request, for one unit of work.
 *
 * A host mints one of these once, after authenticating, and supplies it explicitly to every adapter and use
 * case; from there every authorization decision, audit record and idempotency fingerprint reads it instead of
 * re-deriving the actor further down the stack. It carries exactly one identity, a human `Principal` or a
 * `SystemActor`, never both and never neither, and the authentication strength is held to agree with that
 * choice. Its provenance object ties it to the authority that issued it, which is what a host's gateway checks
 * before anything else: a context assembled anywhere but that authority authorizes nothing, whatever identity
 * it claims to carry. Nothing here reads a request, a session store, a clock or a container; every fact is an
 * explicit input, and a context that cannot be proved consistent is refused rather than built.
 *
 * @since  0.1.0
 */
final readonly class ExecutionContext
{
    /**
     * Assemble a context and enforce the identity, strength, surface, proof and identifier invariants.
     *
     * @param   object                  $provenance              Authority that issued this context; compared by
     *          object identity.
     * @param   ?Principal              $principal               Human actor, or null for a system context.
     * @param   ?SystemActor            $systemActor             System actor, or null for a human context.
     * @param   SiteContext             $site                    Site this unit of work executes in.
     * @param   AuthenticationStrength  $authenticationStrength  How the actor proved itself.
     * @param   string                  $requestId               Identifier of this single unit of work.
     * @param   string                  $correlationId           Identifier shared across one trace.
     * @param   AuthenticatedSurface    $surface                 Authenticated delivery boundary.
     * @param   ?MembershipContext      $membership              Server-resolved organization membership.
     * @param   ?string                 $sessionId               Rotated browser-session identity, when present.
     * @param   ?StepUpProof            $stepUpProof             Fresh multi-factor proof, when completed.
     *
     * @throws  InvalidContext  When both or neither identity is supplied, when the identity and the strength
     *          disagree, when a system context uses any surface but `Background`, when the strength and the
     *          proof disagree, when the proof is bound to another actor, session, site, organization, workspace
     *          or epoch, or when an identifier is empty, longer than 191 bytes or carries a control character.
     *
     * @since   0.1.0
     */
    private function __construct(
        private object $provenance,
        private ?Principal $principal,
        private ?SystemActor $systemActor,
        private SiteContext $site,
        private AuthenticationStrength $authenticationStrength,
        private string $requestId,
        private string $correlationId,
        private AuthenticatedSurface $surface,
        private ?MembershipContext $membership,
        private ?string $sessionId,
        private ?StepUpProof $stepUpProof,
    ) {
        if (($principal === null) === ($systemActor === null)) {
            throw new InvalidContext('An execution context must contain exactly one human or system identity.');
        }
        if ($principal !== null && $authenticationStrength === AuthenticationStrength::System) {
            throw new InvalidContext('A human execution context cannot use system authentication.');
        }
        if ($systemActor !== null && $authenticationStrength !== AuthenticationStrength::System) {
            throw new InvalidContext('A system execution context must use system authentication.');
        }
        if ($principal !== null) {
            self::assertIdentity($principal->subject(), 'principal subject');
            if ($principal->securityEpoch() < 1) {
                throw new InvalidContext('A principal security epoch must be positive.');
            }
        }
        if ($systemActor !== null) {
            self::assertIdentity($systemActor->identifier(), 'system actor');
        }

        self::assertIdentity($requestId, 'request');
        self::assertIdentity($correlationId, 'correlation');
        if ($sessionId !== null) {
            self::assertIdentity($sessionId, 'session');
        }
        if (($authenticationStrength === AuthenticationStrength::MultiFactor) !== ($stepUpProof !== null)) {
            throw new InvalidContext('Multi-factor authentication requires exactly one step-up proof.');
        }
        if (
            $stepUpProof !== null && (
            $principal === null
            || $sessionId === null
            || $stepUpProof->actorId() !== $principal->subject()
            || $stepUpProof->sessionId() !== $sessionId
            || !$stepUpProof->site()->equals($site)
            || $stepUpProof->organization()?->identifier() !== $membership?->organization()->identifier()
            || $stepUpProof->workspace()?->identifier() !== $membership?->workspace()?->identifier()
            || $stepUpProof->securityEpoch() !== $principal->securityEpoch()
            )
        ) {
            throw new InvalidContext('The step-up proof does not match the execution context.');
        }
        if ($systemActor !== null && $surface !== AuthenticatedSurface::Background) {
            throw new InvalidContext('A system execution context must use the background surface.');
        }
    }

    /**
     * Mint a context for a signed-in human actor.
     *
     * The principal must carry the same provenance as the context being issued, so a principal authenticated by
     * one authority cannot be re-wrapped in a context that another authority trusts. When no surface is given
     * it is derived from the strength alone, never from ambient state: a bearer token enters through the API
     * surface and every other human strength through the administrator surface.
     *
     * @param   object                  $provenance              Authority issuing the context.
     * @param   Principal               $principal               Authenticated actor, seen through the contract.
     * @param   SiteContext             $site                    Site this unit of work executes in.
     * @param   AuthenticationStrength  $authenticationStrength  How the actor proved itself; `System` is
     *          refused here.
     * @param   string                  $requestId               Identifier of this single unit of work.
     * @param   ?string                 $correlationId           Trace identifier; defaults to `$requestId`.
     * @param   ?AuthenticatedSurface   $surface                 Delivery boundary; derived from the strength
     *          when null.
     * @param   ?MembershipContext      $membership              Server-resolved organization membership.
     * @param   ?string                 $sessionId               Rotated browser-session identity.
     * @param   ?StepUpProof            $stepUpProof             Fresh proof, required for multi-factor strength.
     *
     * @return  self  A human context bound to the supplied authority.
     *
     * @throws  InvalidContext  When the principal came from a different authority, when the strength is
     *          `System`, when the proof and strength disagree or the proof is bound elsewhere, or when an
     *          identifier is invalid.
     *
     * @since   0.1.0
     */
    public static function issueHuman(
        object $provenance,
        Principal $principal,
        SiteContext $site,
        AuthenticationStrength $authenticationStrength,
        string $requestId,
        ?string $correlationId = null,
        ?AuthenticatedSurface $surface = null,
        ?MembershipContext $membership = null,
        ?string $sessionId = null,
        ?StepUpProof $stepUpProof = null,
    ): self {
        if (!$principal->hasProvenance($provenance)) {
            throw new InvalidContext('A human context requires a principal from the same authority.');
        }

        return new self(
            $provenance,
            $principal,
            null,
            $site,
            $authenticationStrength,
            $requestId,
            $correlationId ?? $requestId,
            $surface ?? match ($authenticationStrength) {
                AuthenticationStrength::BearerToken => AuthenticatedSurface::Api,
                default => AuthenticatedSurface::Administrator,
            },
            $membership,
            $sessionId,
            $stepUpProof,
        );
    }

    /**
     * Mint a context for unattended work that has no human behind it.
     *
     * The strength is fixed at `AuthenticationStrength::System` and the surface at
     * `AuthenticatedSurface::Background`, which is what tells a host to authorize against the actor's declared
     * authority rather than against a principal's grants. Which actors exist, and what each may do, is the
     * host's decision; this package only makes the choice explicit.
     *
     * @param   object       $provenance     Authority issuing the context.
     * @param   SystemActor  $actor          Which unattended actor is running.
     * @param   SiteContext  $site           Site this unit of work executes in.
     * @param   string       $requestId      Identifier of this single unit of work.
     * @param   ?string      $correlationId  Trace identifier; defaults to `$requestId`.
     *
     * @return  self  A system context carrying no human principal.
     *
     * @throws  InvalidContext  When the actor identifier, the request identifier or the correlation identifier
     *          is invalid.
     *
     * @since   0.1.0
     */
    public static function issueSystem(
        object $provenance,
        SystemActor $actor,
        SiteContext $site,
        string $requestId,
        ?string $correlationId = null,
    ): self {
        return new self(
            $provenance,
            null,
            $actor,
            $site,
            AuthenticationStrength::System,
            $requestId,
            $correlationId ?? $requestId,
            AuthenticatedSurface::Background,
            null,
            null,
            null,
        );
    }

    /**
     * Reach the human actor this context was issued for.
     *
     * @return  ?Principal  The principal, or null when a system actor holds this context.
     *
     * @since   0.1.0
     */
    public function principal(): ?Principal
    {
        return $this->principal;
    }

    /**
     * Reach the unattended actor this context was issued for.
     *
     * @return  ?SystemActor  The actor, or null when a human principal holds this context.
     *
     * @since   0.1.0
     */
    public function systemActor(): ?SystemActor
    {
        return $this->systemActor;
    }

    /**
     * Whether unattended work, rather than a person, holds this context.
     *
     * @return  bool  True exactly when `systemActor()` is non-null and the strength is `System`.
     *
     * @since   0.1.0
     */
    public function isSystem(): bool
    {
        return $this->systemActor !== null;
    }

    /**
     * Report which site this unit of work executes in.
     *
     * @return  SiteContext  Site every resource touched here must belong to, unless the action is global.
     *
     * @since   0.1.0
     */
    public function site(): SiteContext
    {
        return $this->site;
    }

    /**
     * Report how the actor proved itself for this unit of work.
     *
     * @return  AuthenticationStrength  Password, bearer token or multi-factor for a human; `System` otherwise.
     *
     * @since   0.1.0
     */
    public function authenticationStrength(): AuthenticationStrength
    {
        return $this->authenticationStrength;
    }

    /**
     * Report the authenticated delivery boundary.
     *
     * @return  AuthenticatedSurface  Surface whose session or credential issued this context.
     *
     * @since   0.1.0
     */
    public function surface(): AuthenticatedSurface
    {
        return $this->surface;
    }

    /**
     * Reach the server-resolved organization, when this unit of work is organization-scoped.
     *
     * @return  ?OrganizationContext  Organization from the membership snapshot; null means explicitly none.
     *
     * @since   0.1.0
     */
    public function organization(): ?OrganizationContext
    {
        return $this->membership?->organization();
    }

    /**
     * Reach the optional workspace nested inside the active membership.
     *
     * @return  ?WorkspaceContext  Selected workspace, or null for organization-wide or organization-less work.
     *
     * @since   0.1.0
     */
    public function workspace(): ?WorkspaceContext
    {
        return $this->membership?->workspace();
    }

    /**
     * Reach the versioned membership snapshot used by authorization.
     *
     * @return  ?MembershipContext  Membership, versions and policy generation, when organization-scoped.
     *
     * @since   0.1.0
     */
    public function membership(): ?MembershipContext
    {
        return $this->membership;
    }

    /**
     * Reach the rotated browser-session identity without exposing a cookie secret.
     *
     * @return  ?string  Stored session row identity, or null for non-session credentials.
     *
     * @since   0.1.0
     */
    public function sessionId(): ?string
    {
        return $this->sessionId;
    }

    /**
     * Reach the fresh multi-factor proof attached to this context.
     *
     * @return  ?StepUpProof  Bound proof, present exactly when the strength is `MultiFactor`.
     *
     * @since   0.1.0
     */
    public function stepUpProof(): ?StepUpProof
    {
        return $this->stepUpProof;
    }

    /**
     * Report the identifier of this single unit of work.
     *
     * @return  string  Distinct per operation; a context derived with `child()` carries a new one.
     *
     * @since   0.1.0
     */
    public function requestId(): string
    {
        return $this->requestId;
    }

    /**
     * Report the identifier shared by every unit of work in the same trace.
     *
     * @return  string  Carried unchanged into child contexts, so nested operations group in the audit trail.
     *
     * @since   0.1.0
     */
    public function correlationId(): string
    {
        return $this->correlationId;
    }

    /**
     * Name the actor that denials and audit records are attributed to.
     *
     * @return  string  The principal's subject for a human context, otherwise the system actor's identifier.
     *
     * @throws  LogicException  When neither identity is present, which the constructor invariant prevents.
     *
     * @since   0.1.0
     */
    public function actorId(): string
    {
        return $this->principal?->subject() ?? $this->systemActor?->identifier()
            ?? throw new LogicException('The execution context has no identity.');
    }

    /**
     * Return the site this execution is scoped to, as a plain identifier.
     *
     * @return  string  Canonical site identifier.
     *
     * @since   0.1.0
     */
    public function siteIdentifier(): string
    {
        return $this->site->identifier();
    }

    /**
     * Return the organization scope of this execution, when one is active.
     *
     * @return  ?string  Canonical organization identifier, or null outside an organization scope.
     *
     * @since   0.1.0
     */
    public function organizationIdentifier(): ?string
    {
        return $this->organization()?->identifier();
    }

    /**
     * Return the workspace scope of this execution, when one is active.
     *
     * @return  ?string  Canonical workspace identifier, or null outside a workspace scope.
     *
     * @since   0.1.0
     */
    public function workspaceIdentifier(): ?string
    {
        return $this->workspace()?->identifier();
    }

    /**
     * Return the delivery surface this execution entered through, as a plain value.
     *
     * @return  string  Backing value of the authenticated surface.
     *
     * @since   0.1.0
     */
    public function deliverySurface(): string
    {
        return $this->surface->value;
    }

    /**
     * Digest everything about the caller's authority that must be unchanged for a replay to be safe.
     *
     * A host stores this beside a cached idempotent response and compares it when the same key returns. A
     * replay that arrives with different authority, such as a re-issued credential, a bumped security epoch, an
     * altered grant set, another site, another surface, another membership or a different authentication
     * strength, no longer matches, so it is refused rather than served the earlier result. Identity alone would
     * not be enough, since the same user can legitimately lose authority between the two requests.
     *
     * @return  string  Lowercase hexadecimal SHA-256 over the actor's own fingerprint, the site, the strength,
     *          the surface, the membership fingerprint, a digest of the session and the proof nonce.
     *
     * @since   0.1.0
     */
    public function authorizationFingerprint(): string
    {
        $identity = $this->principal?->authorizationFingerprint()
            ?? 'system:' . ($this->systemActor?->identifier() ?? 'unknown');

        return hash('sha256', implode("\n", [
            $identity,
            $this->site->identifier(),
            $this->authenticationStrength->value,
            $this->surface->value,
            $this->membership?->fingerprint() ?? '-',
            $this->sessionId === null ? '-' : hash('sha256', $this->sessionId),
            $this->stepUpProof?->nonce() ?? '-',
        ]));
    }

    /**
     * Digest authority that remains stable across a password-to-step-up elevation of one session.
     *
     * Approval bindings use this digest so a request can wait for independent approvers and still be consumed
     * after a fresh proof and its mandatory session rotation, while any principal grant, epoch, site,
     * membership or surface change makes the binding unusable.
     *
     * @return  string  Lowercase hexadecimal SHA-256 over the actor's authority fingerprint, the site, the
     *          surface and the membership fingerprint, without ephemeral proof or session state.
     *
     * @since   0.1.0
     */
    public function approvalFingerprint(): string
    {
        $identity = $this->principal?->authorityFingerprint()
            ?? 'system:' . ($this->systemActor?->identifier() ?? 'unknown');

        return hash('sha256', implode("\n", [
            $identity,
            $this->site->identifier(),
            $this->surface->value,
            $this->membership?->fingerprint() ?? '-',
        ]));
    }

    /**
     * Check that this context, and the principal inside it, came from a given authority.
     *
     * Comparison is by object identity, not equality, so the caller has to hold the very instance the host wired
     * in. A context rebuilt from serialized data therefore cannot pass, which is what makes a gateway's
     * provenance check meaningful.
     *
     * @param   object  $provenance  Authority object to test this context against.
     *
     * @return  bool  True only when the context and its principal both originate from that authority.
     *
     * @since   0.1.0
     */
    public function hasProvenance(object $provenance): bool
    {
        return $this->provenance === $provenance
            && ($this->principal === null || $this->principal->hasProvenance($provenance));
    }

    /**
     * Derive a context for a nested operation, keeping the identity, site, strength, surface and proof.
     *
     * A host uses this so that each nested call within one session is authorized and audited under its own
     * request identifier while staying inside the caller's trace. Authority is unchanged: this is a
     * relabelling, not a way to acquire more.
     *
     * @param   string   $requestId      Identifier for the nested unit of work.
     * @param   ?string  $correlationId  Trace identifier; defaults to this context's own.
     *
     * @return  self  A context differing only in its request and correlation identifiers.
     *
     * @throws  InvalidContext  When an identifier is empty, over 191 bytes or carries a control character.
     *
     * @since   0.1.0
     */
    public function child(string $requestId, ?string $correlationId = null): self
    {
        return new self(
            $this->provenance,
            $this->principal,
            $this->systemActor,
            $this->site,
            $this->authenticationStrength,
            $requestId,
            $correlationId ?? $this->correlationId,
            $this->surface,
            $this->membership,
            $this->sessionId,
            $this->stepUpProof,
        );
    }

    /**
     * Export the context's non-secret facts for logging, an audit record or transport.
     *
     * The provenance object, the session identity, the proof nonce and every fingerprint are deliberately
     * absent: none of them is a fact about the unit of work, and each would let an exported record be replayed
     * or correlated with live state. A context cannot be rebuilt from this export by design.
     *
     * @return  array{
     *              actor: string,
     *              actor_kind: string,
     *              site: string,
     *              organization: ?string,
     *              workspace: ?string,
     *              membership: ?array{
     *                  membership_id: string,
     *                  organization: string,
     *                  workspace: ?string,
     *                  membership_version: int,
     *                  policy_generation: int
     *              },
     *              authentication_strength: string,
     *              surface: string,
     *              request_id: string,
     *              correlation_id: string,
     *              session_bound: bool,
     *              step_up: ?array{
     *                  actor: string,
     *                  site: string,
     *                  organization: ?string,
     *                  workspace: ?string,
     *                  method: string,
     *                  verified_at: string,
     *                  expires_at: string,
     *                  purpose: string,
     *                  security_epoch: int
     *              }
     *          }  `actor_kind` is `human` or `system`; `session_bound` says whether a session identity exists.
     *
     * @since   0.1.0
     */
    public function toArray(): array
    {
        return [
            'actor' => $this->actorId(),
            'actor_kind' => $this->principal === null ? 'system' : 'human',
            'site' => $this->site->identifier(),
            'organization' => $this->organizationIdentifier(),
            'workspace' => $this->workspaceIdentifier(),
            'membership' => $this->membership?->toArray(),
            'authentication_strength' => $this->authenticationStrength->value,
            'surface' => $this->surface->value,
            'request_id' => $this->requestId,
            'correlation_id' => $this->correlationId,
            'session_bound' => $this->sessionId !== null,
            'step_up' => $this->stepUpProof?->toArray(),
        ];
    }

    /**
     * Reject an identifier that could not be stored, compared or logged safely.
     *
     * @param   string  $value  Candidate identifier.
     * @param   string  $name   Which identifier is being checked, quoted in the failure message.
     *
     * @return  void
     *
     * @throws  InvalidContext  When the value is empty, over 191 bytes or has a control character.
     *
     * @since   0.1.0
     */
    private static function assertIdentity(string $value, string $name): void
    {
        if ($value === '' || strlen($value) > 191 || preg_match('/[\x00-\x1F\x7F]/', $value) === 1) {
            throw new InvalidContext(sprintf('The %s identity is invalid.', $name));
        }
    }
}
