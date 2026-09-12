# Architecture

The dependency direction is host adapter to `Kumwe\Context\Contract`, and immutable values to those contracts.
No production class imports App, SDK, Doctrine, PSR HTTP or a container. The eight value/enumeration types are
ExecutionContext, SiteContext, OrganizationContext, WorkspaceContext, MembershipContext, AuthenticationStrength,
AuthenticatedSurface and StepUpProof. Principal/SystemActor are interfaces; InvalidContext is the sole exception.

Core's concrete authenticated principal and closed system identity enum implement neutral actor contracts.
Provenance object identity remains an explicit fact checked by the host; package construction cannot turn a
client-supplied identity into authority. Implementations of the two interfaces must be trusted host objects with
stable values throughout the unit of work. Package readonly containers do not freeze mutable host code.

Principal exposes stable identity, epoch, provenance and fingerprints. Capability/grant evaluation and its types
remain with Core and Access Control, avoiding an Authorization/Identity dependency cycle. The
[Core contract](core-contract.md) records responsibility boundaries and composition requirements.

All methods are deterministic for their explicit inputs. There are no clocks, environment lookups, persistence,
authorization side effects, global caches or request-local singletons. Membership and security freshness are runtime
host obligations. Scope normalization and authority fingerprints follow the documented public contract. A step-up
proof with a workspace requires its enclosing organization; proof consistency does not establish authorization.

Class/unit tests, public API reflection, architecture checks and built-archive consumer verification run here.
Core retains database, transaction, authorization and delivery integration tests.

Raw control bytes in site, organization and workspace inputs are rejected before whitespace normalization.
This includes leading/trailing NUL, tabs, newlines, carriage returns and DEL; valid space-padded values still normalize.
