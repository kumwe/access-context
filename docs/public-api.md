# Public API

Every declared public member is documented below from its source contract. Signatures are also verified against
`resources/public-api/v1.json`. The package performs no I/O, transaction or authorization. Values are immutable;
host Principal/SystemActor implementations must remain stable for the unit of work. Null denotes explicit absence.
Errors are InvalidContext unless a signature explicitly documents another PHP exception. Methods below do not
mutate state; constructors validate and return new values. No implicit clocks or locale-dependent formatting exist.

## `Kumwe\Context\Contract\Principal`

interface; stable since 0.1.0. Source: `src/Contract/Principal.php`.

A human actor that has already proved who it is, seen only through the facts policy needs.

The host authenticates and owns the principal's grants, credentials and session; this contract exposes the
stable identifier and the claims an execution context and a policy evaluator read, and nothing that would let
a consumer authenticate, load authority or reach a session. An implementation is immutable for the life of a
unit of work and is vouched for by one provenance object, so a principal assembled anywhere else authorizes
nothing. `ExecutionContext::issueHuman()` validates the subject and epoch before trusting an implementation.

@since  0.1.0

### `authorityFingerprint(...)`

```php
public function authorityFingerprint(): string
```

Digest of the exact effective authority, independent of the credential that presented it.

@return  string  Lowercase hexadecimal SHA-256; stable across a session rotation of the same authority.

@since   0.1.0

### `authorizationFingerprint(...)`

```php
public function authorizationFingerprint(): string
```

Digest of the exact effective authority, bound to the credential that presented it.

@return  string  Lowercase hexadecimal SHA-256; changes with the subject, credential, epoch or grant set.

@since   0.1.0

### `hasProvenance(...)`

```php
public function hasProvenance(object $provenance): bool
```

Whether this principal was vouched for by a particular authority.

Comparison is by object identity, never by class or value, so only the very object the authenticating
adapter was wired with satisfies it.

@param   object  $provenance  Authority object to test this principal against.

@return  bool  True only when it is the same instance the principal was issued with.

@since   0.1.0

### `securityEpoch(...)`

```php
public function securityEpoch(): int
```

The authorization epoch observed while this principal was authenticated.

@return  int  Positive; a host invalidates it whenever the actor's security state changes.

@since   0.1.0

### `subject(...)`

```php
public function subject(): string
```

The stable identifier of the person this principal speaks for.

@return  string  Non-empty, at most 191 bytes, free of control characters; the host's canonical spelling.

@since   0.1.0

## `Kumwe\Context\Contract\SystemActor`

interface; stable since 0.1.0. Source: `src/Contract/SystemActor.php`.

An unattended actor a host has explicitly chosen to issue an execution context to.

Work that runs with no operator present still has to name who is acting. The host owns the closed set of
such actors and the authority each one carries; this contract only makes the choice explicit, so a system
context can never be mistaken for a signed-in person and audit records name one stable token per actor.
`ExecutionContext::issueSystem()` validates the identifier before trusting an implementation.

@since  0.1.0

### `identifier(...)`

```php
public function identifier(): string
```

The stable token that names this actor in audit records and fingerprints.

@return  string  Non-empty, at most 191 bytes, free of control characters, such as `system:worker`.

@since   0.1.0

## `Kumwe\Context\Exception\InvalidContext`

class; stable since 0.1.0. Source: `src/Exception/InvalidContext.php`.

Raised when a value, or the combination of values in a context, cannot represent an established fact.

Every refusal in this package is one of these, so a consumer can catch the package's own type while a caller
that already handles `InvalidArgumentException` keeps working unchanged. The message names the rule that was
broken and never echoes the offending input, so a refusal can be logged without leaking what was submitted.

@since  0.1.0

## `Kumwe\Context\Value\AuthenticatedSurface`

enum; stable since 0.1.0. Source: `src/Value/AuthenticatedSurface.php`.

Delivery boundary through which an execution context authenticated.

The surface is a fact about how the unit of work entered the host, recorded so a policy can confine an action
to the boundary it was designed for and an audit reviewer can separate browser, token and unattended traffic.
A host maps its own delivery adapters onto these cases; the package neither infers a surface from a request
nor lets one surface stand in for another.

@since  0.1.0

### `Administrator`

`case Administrator = 'administrator';`

Administrator browser session, never accepted by portal-only work.

@since  0.1.0

### `Api`

`case Api = 'api';`

Public REST application programming interface, credentialed by a bearer token.

@since  0.1.0

### `Background`

`case Background = 'background';`

Constrained unattended worker or schedule; the only surface a system context may use.

@since  0.1.0

### `Cli`

`case Cli = 'cli';`

Command-line management surface operated by a person.

@since  0.1.0

### `Mcp`

`case Mcp = 'mcp';`

Model Context Protocol tool surface, credentialed by a bearer token.

@since  0.1.0

### `Portal`

`case Portal = 'portal';`

Ordinary-user portal browser session, never accepted by administrator or recovery work.

@since  0.1.0

### `Recovery`

`case Recovery = 'recovery';`

Installation recovery surface, isolated from normal authentication.

@since  0.1.0

## `Kumwe\Context\Value\AuthenticationStrength`

enum; stable since 0.1.0. Source: `src/Value/AuthenticationStrength.php`.

Kind of credential that proved the identity carried by an execution context.

`ExecutionContext` stores this beside the actor and treats it as a consistency check rather than decoration:
a context built around a `SystemActor` must declare `System`, and a context built around a human principal
must not, so a background worker can never be mistaken for a signed-in operator. The backing value is what a
host writes into an audit record and mixes into the context's authorization fingerprint, so a stored
idempotent result cannot be replayed by a caller who authenticated differently from the one that produced it.

@since  0.1.0

### `BearerToken`

`case BearerToken = 'bearer_token';`

A human principal was resolved from an issued access token presented as a bearer credential.

The actor is still a person, but no primary credential was offered in this exchange.

@since  0.1.0

### `MultiFactor`

`case MultiFactor = 'multi_factor';`

A signed-in human completed a fresh multi-factor challenge in the current rotated session.

The accompanying `StepUpProof`, rather than this label alone, supplies the actor, session, scope, method
and freshness bindings a high-impact action must verify.

@since  0.1.0

### `Password`

`case Password = 'password';`

A human proved themselves with their account password in this exchange.

Covers an interactive login, the cookie-backed session it establishes, and console work that reads an
operator's password from a secret file.

@since  0.1.0

### `System`

`case System = 'system';`

No human is behind the request: the context was issued to a trusted in-process actor.

Reserved for `ExecutionContext::issueSystem()`, whose authority comes from the host's choice of actor
rather than from a credential presented in this exchange.

@since  0.1.0

### `isHuman(...)`

```php
public function isHuman(): bool
```

Whether a person, rather than a trusted in-process actor, proved itself with this strength.

@return  bool  True for every case except `System`.

@since   0.1.0

### `satisfies(...)`

```php
public function satisfies(self $required): bool
```

Whether this strength meets a required minimum under the credential-presence ordering.

Human strengths are ordered by what was presented in the current exchange: `BearerToken` presents only a
derived credential, `Password` presents the primary credential, and `MultiFactor` presents the primary
credential plus a fresh second factor, so `BearerToken` < `Password` < `MultiFactor`. `System` stands
outside that order: it satisfies only `System`, and no human strength satisfies it, so a requirement can
never be met by the wrong kind of actor.

@param   self  $required  Minimum strength a policy demands.

@return  bool  True when this strength is the required one or a stronger human strength.

@since   0.1.0

## `Kumwe\Context\Value\ExecutionContext`

class; stable since 0.1.0. Source: `src/Value/ExecutionContext.php`.

Immutable envelope naming who is acting, in which site, and under which request, for one unit of work.

A host mints one of these once, after authenticating, and supplies it explicitly to every adapter and use
case; from there every authorization decision, audit record and idempotency fingerprint reads it instead of
re-deriving the actor further down the stack. It carries exactly one identity, a human `Principal` or a
`SystemActor`, never both and never neither, and the authentication strength is held to agree with that
choice. Its provenance object ties it to the authority that issued it, which is what a host's gateway checks
before anything else: a context assembled anywhere but that authority authorizes nothing, whatever identity
it claims to carry. Nothing here reads a request, a session store, a clock or a container; every fact is an
explicit input, and a context that cannot be proved consistent is refused rather than built.

@since  0.1.0

### `actorId(...)`

```php
public function actorId(): string
```

Name the actor that denials and audit records are attributed to.

@return  string  The principal's subject for a human context, otherwise the system actor's identifier.

@throws  LogicException  When neither identity is present, which the constructor invariant prevents.

@since   0.1.0

### `approvalFingerprint(...)`

```php
public function approvalFingerprint(): string
```

Digest authority that remains stable across a password-to-step-up elevation of one session.

Approval bindings use this digest so a request can wait for independent approvers and still be consumed
after a fresh proof and its mandatory session rotation, while any principal grant, epoch, site,
membership or surface change makes the binding unusable.

@return  string  Lowercase hexadecimal SHA-256 over the actor's authority fingerprint, the site, the
         surface and the membership fingerprint, without ephemeral proof or session state.

@since   0.1.0

### `authenticationStrength(...)`

```php
public function authenticationStrength(): AuthenticationStrength
```

Report how the actor proved itself for this unit of work.

@return  AuthenticationStrength  Password, bearer token or multi-factor for a human; `System` otherwise.

@since   0.1.0

### `authorizationFingerprint(...)`

```php
public function authorizationFingerprint(): string
```

Digest everything about the caller's authority that must be unchanged for a replay to be safe.

A host stores this beside a cached idempotent response and compares it when the same key returns. A
replay that arrives with different authority, such as a re-issued credential, a bumped security epoch, an
altered grant set, another site, another surface, another membership or a different authentication
strength, no longer matches, so it is refused rather than served the earlier result. Identity alone would
not be enough, since the same user can legitimately lose authority between the two requests.

@return  string  Lowercase hexadecimal SHA-256 over the actor's own fingerprint, the site, the strength,
         the surface, the membership fingerprint, a digest of the session and the proof nonce.

@since   0.1.0

### `child(...)`

```php
public function child(string $requestId, ?string $correlationId = null): self
```

Derive a context for a nested operation, keeping the identity, site, strength, surface and proof.

A host uses this so that each nested call within one session is authorized and audited under its own
request identifier while staying inside the caller's trace. Authority is unchanged: this is a
relabelling, not a way to acquire more.

@param   string   $requestId      Identifier for the nested unit of work.
@param   ?string  $correlationId  Trace identifier; defaults to this context's own.

@return  self  A context differing only in its request and correlation identifiers.

@throws  InvalidContext  When an identifier is empty, over 191 bytes or carries a control character.

@since   0.1.0

### `correlationId(...)`

```php
public function correlationId(): string
```

Report the identifier shared by every unit of work in the same trace.

@return  string  Carried unchanged into child contexts, so nested operations group in the audit trail.

@since   0.1.0

### `deliverySurface(...)`

```php
public function deliverySurface(): string
```

Return the delivery surface this execution entered through, as a plain value.

@return  string  Backing value of the authenticated surface.

@since   0.1.0

### `hasProvenance(...)`

```php
public function hasProvenance(object $provenance): bool
```

Check that this context, and the principal inside it, came from a given authority.

Comparison is by object identity, not equality, so the caller has to hold the very instance the host wired
in. A context rebuilt from serialized data therefore cannot pass, which is what makes a gateway's
provenance check meaningful.

@param   object  $provenance  Authority object to test this context against.

@return  bool  True only when the context and its principal both originate from that authority.

@since   0.1.0

### `isSystem(...)`

```php
public function isSystem(): bool
```

Whether unattended work, rather than a person, holds this context.

@return  bool  True exactly when `systemActor()` is non-null and the strength is `System`.

@since   0.1.0

### `issueHuman(...)`

```php
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
            throw new InvalidContext('A human context requires a principal from the same authority.')
```

Mint a context for a signed-in human actor.

The principal must carry the same provenance as the context being issued, so a principal authenticated by
one authority cannot be re-wrapped in a context that another authority trusts. When no surface is given
it is derived from the strength alone, never from ambient state: a bearer token enters through the API
surface and every other human strength through the administrator surface.

@param   object                  $provenance              Authority issuing the context.
@param   Principal               $principal               Authenticated actor, seen through the contract.
@param   SiteContext             $site                    Site this unit of work executes in.
@param   AuthenticationStrength  $authenticationStrength  How the actor proved itself; `System` is
         refused here.
@param   string                  $requestId               Identifier of this single unit of work.
@param   ?string                 $correlationId           Trace identifier; defaults to `$requestId`.
@param   ?AuthenticatedSurface   $surface                 Delivery boundary; derived from the strength
         when null.
@param   ?MembershipContext      $membership              Server-resolved organization membership.
@param   ?string                 $sessionId               Rotated browser-session identity.
@param   ?StepUpProof            $stepUpProof             Fresh proof, required for multi-factor strength.

@return  self  A human context bound to the supplied authority.

@throws  InvalidContext  When the principal came from a different authority, when the strength is
         `System`, when the proof and strength disagree or the proof is bound elsewhere, or when an
         identifier is invalid.

@since   0.1.0

### `issueSystem(...)`

```php
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
        )
```

Mint a context for unattended work that has no human behind it.

The strength is fixed at `AuthenticationStrength::System` and the surface at
`AuthenticatedSurface::Background`, which is what tells a host to authorize against the actor's declared
authority rather than against a principal's grants. Which actors exist, and what each may do, is the
host's decision; this package only makes the choice explicit.

@param   object       $provenance     Authority issuing the context.
@param   SystemActor  $actor          Which unattended actor is running.
@param   SiteContext  $site           Site this unit of work executes in.
@param   string       $requestId      Identifier of this single unit of work.
@param   ?string      $correlationId  Trace identifier; defaults to `$requestId`.

@return  self  A system context carrying no human principal.

@throws  InvalidContext  When the actor identifier, the request identifier or the correlation identifier
         is invalid.

@since   0.1.0

### `membership(...)`

```php
public function membership(): ?MembershipContext
```

Reach the versioned membership snapshot used by authorization.

@return  ?MembershipContext  Membership, versions and policy generation, when organization-scoped.

@since   0.1.0

### `organization(...)`

```php
public function organization(): ?OrganizationContext
```

Reach the server-resolved organization, when this unit of work is organization-scoped.

@return  ?OrganizationContext  Organization from the membership snapshot; null means explicitly none.

@since   0.1.0

### `organizationIdentifier(...)`

```php
public function organizationIdentifier(): ?string
```

Return the organization scope of this execution, when one is active.

@return  ?string  Canonical organization identifier, or null outside an organization scope.

@since   0.1.0

### `principal(...)`

```php
public function principal(): ?Principal
```

Reach the human actor this context was issued for.

@return  ?Principal  The principal, or null when a system actor holds this context.

@since   0.1.0

### `requestId(...)`

```php
public function requestId(): string
```

Report the identifier of this single unit of work.

@return  string  Distinct per operation; a context derived with `child()` carries a new one.

@since   0.1.0

### `sessionId(...)`

```php
public function sessionId(): ?string
```

Reach the rotated browser-session identity without exposing a cookie secret.

@return  ?string  Stored session row identity, or null for non-session credentials.

@since   0.1.0

### `site(...)`

```php
public function site(): SiteContext
```

Report which site this unit of work executes in.

@return  SiteContext  Site every resource touched here must belong to, unless the action is global.

@since   0.1.0

### `siteIdentifier(...)`

```php
public function siteIdentifier(): string
```

Return the site this execution is scoped to, as a plain identifier.

@return  string  Canonical site identifier.

@since   0.1.0

### `stepUpProof(...)`

```php
public function stepUpProof(): ?StepUpProof
```

Reach the fresh multi-factor proof attached to this context.

@return  ?StepUpProof  Bound proof, present exactly when the strength is `MultiFactor`.

@since   0.1.0

### `surface(...)`

```php
public function surface(): AuthenticatedSurface
```

Report the authenticated delivery boundary.

@return  AuthenticatedSurface  Surface whose session or credential issued this context.

@since   0.1.0

### `systemActor(...)`

```php
public function systemActor(): ?SystemActor
```

Reach the unattended actor this context was issued for.

@return  ?SystemActor  The actor, or null when a human principal holds this context.

@since   0.1.0

### `toArray(...)`

```php
public function toArray(): array
```

Export the context's non-secret facts for logging, an audit record or transport.

The provenance object, the session identity, the proof nonce and every fingerprint are deliberately
absent: none of them is a fact about the unit of work, and each would let an exported record be replayed
or correlated with live state. A context cannot be rebuilt from this export by design.

@return  array{
             actor: string,
             actor_kind: string,
             site: string,
             organization: ?string,
             workspace: ?string,
             membership: ?array{
                 membership_id: string,
                 organization: string,
                 workspace: ?string,
                 membership_version: int,
                 policy_generation: int
             },
             authentication_strength: string,
             surface: string,
             request_id: string,
             correlation_id: string,
             session_bound: bool,
             step_up: ?array{
                 actor: string,
                 site: string,
                 organization: ?string,
                 workspace: ?string,
                 method: string,
                 verified_at: string,
                 expires_at: string,
                 purpose: string,
                 security_epoch: int
             }
         }  `actor_kind` is `human` or `system`; `session_bound` says whether a session identity exists.

@since   0.1.0

### `workspace(...)`

```php
public function workspace(): ?WorkspaceContext
```

Reach the optional workspace nested inside the active membership.

@return  ?WorkspaceContext  Selected workspace, or null for organization-wide or organization-less work.

@since   0.1.0

### `workspaceIdentifier(...)`

```php
public function workspaceIdentifier(): ?string
```

Return the workspace scope of this execution, when one is active.

@return  ?string  Canonical workspace identifier, or null outside a workspace scope.

@since   0.1.0

## `Kumwe\Context\Value\MembershipContext`

class; stable since 0.1.0. Source: `src/Value/MembershipContext.php`.

Versioned proof that a principal currently belongs to an organization and an optional workspace.

The membership and policy generations are included in authorization fingerprints, cursors and delegations. A
host must re-read them from live state inside a mutation transaction; a stale snapshot therefore fails rather
than retaining authority after a membership or policy change. The snapshot is a credential, not authority: it
says what the host resolved, and the host decides on every use whether that is still true.

@since  0.1.0

### `__construct(...)`

```php
public function __construct(
        private string $membershipId,
        private OrganizationContext $organization,
        private ?WorkspaceContext $workspace,
        private int $membershipVersion,
        private int $policyGeneration,
    ) {
        if (preg_match(self::UUID, $membershipId) !== 1) {
            throw new InvalidContext('A membership context requires a valid UUID.')
```

Validate a server-resolved membership snapshot.

@param   string               $membershipId       Stable lowercase UUID of the membership row.
@param   OrganizationContext  $organization       Organization conferred by the membership.
@param   ?WorkspaceContext    $workspace          Workspace selection, when the operation is narrower.
@param   int                  $membershipVersion  Positive optimistic version of the membership.
@param   int                  $policyGeneration   Positive organization policy generation.

@throws  InvalidContext  When the row identity is not a canonical lowercase UUID or either generation is
         below one.

@since   0.1.0

### `equals(...)`

```php
public function equals(self $other): bool
```

Whether another snapshot records exactly the same membership at the same generations.

@param   self  $other  Snapshot to compare against.

@return  bool  True when every fingerprinted value is the same.

@since   0.1.0

### `fingerprint(...)`

```php
public function fingerprint(): string
```

Digest every membership value that may change an authorization decision.

@return  string  Lowercase hexadecimal SHA-256 over the row identity, organization, workspace and both
         generations, in that order.

@since   0.1.0

### `membershipId(...)`

```php
public function membershipId(): string
```

Return the stable membership row identity.

@return  string  Canonical lowercase UUID.

@since   0.1.0

### `membershipVersion(...)`

```php
public function membershipVersion(): int
```

Return the optimistic version of the membership row the snapshot was read at.

@return  int  Positive membership version.

@since   0.1.0

### `organization(...)`

```php
public function organization(): OrganizationContext
```

Return the organization the membership confers.

@return  OrganizationContext  Selected organization.

@since   0.1.0

### `policyGeneration(...)`

```php
public function policyGeneration(): int
```

Return the organization policy generation the snapshot was read at.

@return  int  Positive policy generation.

@since   0.1.0

### `toArray(...)`

```php
public function toArray(): array
```

Export the snapshot as plain values for logging, storage beside a decision or transport.

@return  array{
             membership_id: string,
             organization: string,
             workspace: ?string,
             membership_version: int,
             policy_generation: int
         }  Every value the fingerprint covers; nothing here is secret.

@since   0.1.0

### `workspace(...)`

```php
public function workspace(): ?WorkspaceContext
```

Return the optional workspace selected inside the organization.

@return  ?WorkspaceContext  Selected workspace, or null for organization-wide work.

@since   0.1.0

## `Kumwe\Context\Value\OrganizationContext`

class; stable since 0.1.0. Source: `src/Value/OrganizationContext.php`.

Server-resolved organization in which an authorized unit of work executes.

Unlike an organization value a caller submits with a command, this object belongs to the authenticated
execution context: only a host adapter holding the issuing provenance places it there, through a
`MembershipContext`, so a submitted identifier can be compared with it but never establish it. An
organization is never a site: `SiteContext` shares the grammar and nothing else, and a context carries both
facts separately so neither can stand in for the other.

@since  0.1.0

### `equals(...)`

```php
public function equals(self $other): bool
```

Whether another organization context names the same organization.

@param   self  $other  Organization context to compare against.

@return  bool  True when both carry the same normalised identifier.

@since   0.1.0

### `fromString(...)`

```php
public static function fromString(string $identifier): self
```

Normalise and validate an organization identifier read from trusted membership storage.

@param   string  $identifier  Raw organization identifier to normalise and validate.

@return  self  Validated organization context.

@throws  InvalidContext  When the normalised value is empty, runs past 191 characters, starts with
         something other than a lowercase letter or digit, or holds a character outside `a-z`, `0-9`,
         `.`, `_`, `:` and `-`.

@since   0.1.0

### `identifier(...)`

```php
public function identifier(): string
```

Expose the normalised organization identifier.

@return  string  Identifier safe for exact comparison and query binding.

@since   0.1.0

## `Kumwe\Context\Value\SiteContext`

class; stable since 0.1.0. Source: `src/Value/SiteContext.php`.

Validated identifier of the site a unit of work, a resource or a stored row belongs to.

Site separation is enforced by comparing this identifier rather than by partitioning storage: a host refuses
an action whose resource is owned by a site other than the one on the `ExecutionContext`, and repositories
bind it into their queries to scope a listing. The constructor is private, so every instance has come through
`fromString()` or `default()` and is already trimmed, lowercased and matched against a narrow identifier
pattern; callers may compare two identifiers byte for byte and bind one into a query without further checking.
A site is never an organization: `OrganizationContext` shares the grammar and nothing else, and neither type
converts to the other.

@since  0.1.0

### `DEFAULT`

`public const DEFAULT = 'default';`

Identifier of the site that always exists, whatever the installation is configured for.

Exposed as a constant as well as through `default()` so that a repository can bind the literal into a
query, and compare a stored site identifier against it, without building a value object.

@var    string
@since  0.1.0

### `default(...)`

```php
public static function default(): self
```

Name the site a single-site installation runs under, and that installation-wide records own.

Identity records and installation-global work are recorded against this site, so ownership lookups
resolve for them even where an installation serves several sites.

@return  self  Context for the `default` site.

@since   0.1.0

### `equals(...)`

```php
public function equals(self $other): bool
```

Whether another site context names the same site.

Only a `SiteContext` can be equal to a `SiteContext`; an organization with the same spelling is a
different fact and does not type-check here.

@param   self  $other  Site context to compare against.

@return  bool  True when both carry the same normalised identifier.

@since   0.1.0

### `fromString(...)`

```php
public static function fromString(string $identifier): self
```

Build a site context from an identifier read out of configuration, a request or a stored row.

Surrounding whitespace is stripped and the value is lowercased before validation, so identifiers differing
only in case or padding resolve to one site instead of silently splitting its data.

@param   string  $identifier  Raw site identifier to normalise and validate.

@return  self  Context carrying the normalised identifier.

@throws  InvalidContext  When the normalised value is empty, runs past 191 characters, starts with
         something other than a lowercase letter or digit, or holds a character outside `a-z`, `0-9`,
         `.`, `_`, `:` and `-`.

@since   0.1.0

### `identifier(...)`

```php
public function identifier(): string
```

Expose the normalised identifier for comparison, query binding and logging.

@return  string  Lowercase identifier, safe to compare byte for byte against a stored value.

@since   0.1.0

## `Kumwe\Context\Value\StepUpProof`

class; stable since 0.1.0. Source: `src/Value/StepUpProof.php`.

Fresh, single-session proof produced by a successful multi-factor challenge.

A proof is not authority by itself. A host's high-impact policy verifies its actor, rotated session, site,
organization, workspace, method, freshness and nonce before consuming the action or approval in one
transaction; `ExecutionContext` refuses a multi-factor context whose proof does not match the actor, session,
scope and epoch it carries. A proof is valid for at most fifteen minutes after verification and never before it.

@since  0.1.0

### `__construct(...)`

```php
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
        if ($workspace !== null && $organization === null) {
            throw new InvalidContext('A step-up workspace requires an organization.')
```

Validate a proof issued by the host's step-up provider.

@param   string                $actorId        Principal subject bound to the proof.
@param   string                $sessionId      Rotated session identifier bound to the proof.
@param   SiteContext           $site           Exact site for which it is valid.
@param   ?OrganizationContext  $organization   Exact organization, when organization-scoped.
@param   string                $method         Provider method such as `totp` or `recovery_code`.
@param   DateTimeImmutable     $verifiedAt     Instant of successful verification.
@param   DateTimeImmutable     $expiresAt      Exclusive freshness boundary, within fifteen minutes.
@param   string                $nonce          Unpredictable proof identity used to prevent replay.
@param   ?WorkspaceContext     $workspace      Exact workspace, when the protected operation is narrower.
@param   string                $purpose        Narrow protected-operation purpose.
@param   int                   $securityEpoch  Actor authorization epoch at verification time.

@throws  InvalidContext  When an actor or session identity is empty, over 191 bytes or carries a control
         character; when the method is not a lowercase token of 2 to 32 characters; when the freshness
         interval is empty, inverted or longer than fifteen minutes; when the nonce is not 32 to 128
         URL-safe characters; when the purpose is not a lowercase dotted identifier of at most 127
         characters; when a workspace has no organization; or when the epoch is below one.

@since   0.1.0

### `actorId(...)`

```php
public function actorId(): string
```

Return the bound actor identity.

@return  string  Principal subject the proof was issued for.

@since   0.1.0

### `expiresAt(...)`

```php
public function expiresAt(): DateTimeImmutable
```

Return the freshness boundary.

@return  DateTimeImmutable  Exclusive instant from which the proof is stale.

@since   0.1.0

### `isValidFor(...)`

```php
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
            && $now < $this->expiresAt
```

Test the actor, session, site, organization and freshness bindings.

Identity comparisons are constant-time. The proof is valid from `verifiedAt` inclusive to `expiresAt`
exclusive; the caller supplies trusted time rather than the proof reading a clock. The host must also
check workspace, purpose, security epoch and nonce consumption before authorizing a protected action.

@param   string                $actorId       Expected actor.
@param   string                $sessionId     Current rotated session.
@param   SiteContext           $site          Current site.
@param   ?OrganizationContext  $organization  Current organization, or null outside one.
@param   DateTimeImmutable     $now           Current trusted time.

@return  bool  True only while the supplied bindings match and the proof is fresh; not an authorization.

@since   0.1.0

### `method(...)`

```php
public function method(): string
```

Return the verified provider method.

@return  string  Lowercase provider token such as `totp`.

@since   0.1.0

### `nonce(...)`

```php
public function nonce(): string
```

Return the replay nonce.

@return  string  Unpredictable proof identity a host consumes exactly once.

@since   0.1.0

### `organization(...)`

```php
public function organization(): ?OrganizationContext
```

Return the bound organization.

@return  ?OrganizationContext  Exact organization, or null for a proof outside an organization scope.

@since   0.1.0

### `purpose(...)`

```php
public function purpose(): string
```

Return the protected-operation purpose.

@return  string  Lowercase dotted purpose the challenge was issued for.

@since   0.1.0

### `securityEpoch(...)`

```php
public function securityEpoch(): int
```

Return the actor authorization epoch at verification time.

@return  int  Positive epoch; a context refuses a proof whose epoch differs from its principal's.

@since   0.1.0

### `sessionId(...)`

```php
public function sessionId(): string
```

Return the rotated session identity the proof is bound to.

@return  string  Session identifier; a proof never outlives the session it was issued in.

@since   0.1.0

### `site(...)`

```php
public function site(): SiteContext
```

Return the bound site.

@return  SiteContext  Exact site the proof is valid for.

@since   0.1.0

### `toArray(...)`

```php
public function toArray(): array
```

Export the proof's non-secret facts for logging or an audit record.

The nonce and the session identity are deliberately absent: the nonce is the replay secret a host consumes,
and the session identity is redacted so an exported proof cannot be correlated with a live session.

@return  array{
             actor: string,
             site: string,
             organization: ?string,
             workspace: ?string,
             method: string,
             verified_at: string,
             expires_at: string,
             purpose: string,
             security_epoch: int
         }  Instants are rendered in UTC as RFC 3339 with a `Z` designator.

@since   0.1.0

### `verifiedAt(...)`

```php
public function verifiedAt(): DateTimeImmutable
```

Return the verification instant.

@return  DateTimeImmutable  Instant the challenge succeeded; the proof is not valid before it.

@since   0.1.0

### `workspace(...)`

```php
public function workspace(): ?WorkspaceContext
```

Return the bound workspace.

@return  ?WorkspaceContext  Exact workspace, or null when the proof covers the whole organization.

@since   0.1.0

## `Kumwe\Context\Value\WorkspaceContext`

class; stable since 0.1.0. Source: `src/Value/WorkspaceContext.php`.

Optional server-resolved workspace nested inside an authenticated organization.

A workspace narrows organization-scoped work; it never widens it and never exists without the organization
that contains it, which is why a `MembershipContext` carries the pair together and a context exposes the
workspace only through that membership.

@since  0.1.0

### `equals(...)`

```php
public function equals(self $other): bool
```

Whether another workspace context names the same workspace.

@param   self  $other  Workspace context to compare against.

@return  bool  True when both carry the same normalised identifier.

@since   0.1.0

### `fromString(...)`

```php
public static function fromString(string $identifier): self
```

Normalise and validate a workspace identifier read from trusted storage.

@param   string  $identifier  Raw workspace identifier to normalise and validate.

@return  self  Validated workspace context.

@throws  InvalidContext  When the normalised value is empty, runs past 191 characters, starts with
         something other than a lowercase letter or digit, or holds a character outside `a-z`, `0-9`,
         `.`, `_`, `:` and `-`.

@since   0.1.0

### `identifier(...)`

```php
public function identifier(): string
```

Expose the normalised workspace identifier.

@return  string  Identifier safe for exact comparison and query binding.

@since   0.1.0
