# Core contract

`kumwe/access-context` supplies immutable facts to Core under `Kumwe\Context`. Core authenticates actors,
resolves membership and provenance, and makes every authorization decision. The package has no runtime package
requirements beyond PHP and performs no I/O, persistence, transaction management or authorization.

## Public boundary

Core implements `Contract\Principal` and `Contract\SystemActor` using its established identity model. Principal
objects expose stable subjects, security epochs, provenance and fingerprints. System actors come from Core's
approved closed set of unattended identities. Both implementations must remain immutable for the unit of work.

The eight values are `ExecutionContext`, `SiteContext`, `OrganizationContext`, `WorkspaceContext`,
`MembershipContext`, `AuthenticationStrength`, `AuthenticatedSurface` and `StepUpProof`. Invalid inputs raise
`Exception\InvalidContext`. The [public API](public-api.md) describes signatures, normalization and refusal rules.

Core issues contexts at trusted boundaries and passes them explicitly to operations. No ConfigProvider, service
alias, factory or configuration key is required. Never register a shared current actor, membership or tenant.
Core's `ContainerFactory` owns composition; request attributes and SDK execution-context adapters remain Core-owned.

## Authority and sensitive data

Core validates that principal provenance is the exact trusted issuer object. A context cannot authenticate a
principal, admit a system actor or establish trusted scope from submitted URL, Host or form data. Capability/grant
policy and membership resolution remain outside this package.

For high-impact actions, Core refreshes membership and security state in the mutation transaction. It checks proof
workspace, purpose, epoch, method, freshness and nonce consumption alongside current authority. `isValidFor()` checks
only its documented actor/session/site/organization/time inputs and is insufficient to authorize an action.
Core owns audit, disabled-user checks, delivery and recovery.

Redacted `toArray()` exports omit provenance, session identifiers, proof nonces and fingerprints. They support
logging, not context rehydration. Object serialization and client-supplied fingerprints are not trusted transport.

## Compatibility and validation

Use an exact verified pre-1.0 Composer version and commit the host lockfile. Public API, capabilities and the empty
provider decision are recorded in the three manifests under `resources/`. The [release record](release-record.md)
retains exact source provenance, symbol mappings, consumer paths and test ownership needed for Core integration.
The [consumer inventory](core-consumers.json) is anchored to its recorded source commit, not a live Core file list.
Reconcile it against current Core before replacing imports or removing duplicate implementation tests.

The package owns value behavior, input boundaries, deterministic fingerprints, public API and clean archive tests.
Core retains database, transaction, authorization, step-up, token/session, membership, portal, administrator, CLI,
MCP, worker and recovery integration tests. No complete Core test file is authorized for deletion by this contract.
A host rollback restores its previously tested dependency tuple; this value package requires no schema migration.

[Integration](integration.md), [test ownership](test-ownership.md), [release guidance](releasing.md) and
[security policy](security.md) provide the operational details.
