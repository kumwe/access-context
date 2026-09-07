# Host integration and migration

Implement Principal over the existing authenticated identity; do not create another user or grant store.
`subject()` is a non-empty control-free string of at most 191 bytes; `securityEpoch()` is positive.
Provenance compares exact object identity. Both fingerprint methods return lowercase SHA-256 digests; one binds
credential plus effective authority, the other stays stable across session rotation of unchanged authority.
Implement SystemActor over the host's closed set of approved unattended identities. Identifiers follow the same
non-empty/control-free bound. Neither contract authenticates or authorizes its implementation.
Both implementations must remain immutable throughout a unit of work; readonly context fields do not freeze
the internal state of host-supplied objects.

Mint contexts once at the trusted host boundary and supply them to application operations. Browser request attributes
and any SDK ExecutionContext implementation belong to an App adapter; do not register historical class aliases.
No package ConfigProvider needs registration. ContainerFactory remains the only App composition root.

A high-impact operation checks current authority, proof workspace/purpose/method/epoch, freshness and nonce use,
then performs its state change and audit under the host transaction. `isValidFor()` alone is intentionally incomplete
for authorization. Organization/workspace selection must come from trusted membership, never URL/Host/form input.

The migration handoff maps eight App value types, two new ports and the package exception. Compare those source paths
and their consumers/tests with App baseline `960ce8ec00cf724a7cae03e5ba09c4852c9ab54e` before adoption. Newer portable
changes require a successor package release. Existing App User, AuthenticatedPrincipal, SystemIdentity, membership
validator, policies, provenance issuers and SDK-facing adapters remain App-owned.

Adopt only after human merge, immutable publication and a fresh independent release attestation. Exact-pin the
verified pre-1.0 release, regenerate composer.lock, replace imports/config/reflection consumers and compose neutral
ports through App adapters. Remove the eight obsolete App values and their implementation tests together, after
proving all retained integrations. Update the capability index, paired MIG/CS004 records, train and NRM evidence.
Retain database/concurrency/step-up/token/session/membership/portal/admin/CLI/MCP/worker/recovery tests.

Rollback restores the previous App dependency/namespace composition as one deployment. No schema migration is
required by this value package; cached fingerprints/proofs still require the host's normal expiration and policy.
