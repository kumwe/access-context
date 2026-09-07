# Charter

`kumwe/access-context` owns immutable, explicitly supplied access facts under `Kumwe\Context`.
It provides eight scope/strength/surface/proof/context values, two actor contracts and one refusal exception.
The dependency ceiling is PHP alone. It does not own authentication, grants, capability evaluation, authorization,
system identity admission, membership queries, credentials, sessions, host delivery, persistence or a container.

Each source class has one canonical owner. App adapters implement the principal/system actor ports; the package
never implements an SDK or App interface. Site and organization are distinct; an identifier supplied by a client
cannot establish authority. Explicit exports are redacted diagnostics, never a context rehydration mechanism.

Value behavior, hostile input, API compatibility, documentation, manifests and clean archive usage belong here.
App retains transaction, membership freshness, provenance enforcement, disabled users, policy, database, lifecycle,
delivery, deployment and recovery evidence. No provider is appropriate because operation context is explicit input.
