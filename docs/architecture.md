# Architecture

The dependency direction is host adapter to `Kumwe\Context\Contract`, and immutable values to those contracts.
No production class imports App, SDK, Doctrine, PSR HTTP or a container. The eight value/enumeration types are
ExecutionContext, SiteContext, OrganizationContext, WorkspaceContext, MembershipContext, AuthenticationStrength,
AuthenticatedSurface and StepUpProof. Principal/SystemActor are interfaces; InvalidContext is the sole exception.

The source extraction separates the concrete App authenticated principal and closed system identity enum from
neutral actor facts. Provenance object identity remains an explicit fact checked by the host; package construction
cannot turn a client-supplied identity into authority. Implementations of the two interfaces must be trusted host
objects with stable values throughout the unit of work. Package readonly containers do not freeze mutable host code.

The v2 candidate list included capability/grant vocabulary. Current closure does not need either in a public
signature: Principal exposes stable identity, epoch, provenance and fingerprints. Capability/grant evaluation and
its types remain with App/Access ownership, avoiding an Authorization/Identity cycle and an expanded dependency.
This deliberate narrow scope is recorded in the handoff; it is not evidence that the wider Access migration finished.

All methods are deterministic for their explicit inputs. There are no clocks, environment lookups, persistence,
authorization side effects, global caches or request-local singletons. Membership and security freshness are runtime
host obligations. Scope normalization and authority fingerprints preserve the extracted App contract. The additional
orphan-workspace proof refusal is a defensive consistency correction, not a new authorization policy.

Class/unit tests, public API reflection, architecture checks and built-archive consumer verification run here.
The App adoption must retain database/transaction/authorization/delivery tests and delete only package-owned units.
