# Host integration

Implement Principal over the existing authenticated identity; do not create another user or grant store.
`subject()` is a non-empty control-free string of at most 191 bytes; `securityEpoch()` is positive.
Provenance compares exact object identity. Both fingerprint methods return lowercase SHA-256 digests; one binds
credential plus effective authority, the other stays stable across session rotation of unchanged authority.
Implement SystemActor over the host's closed set of approved unattended identities. Identifiers follow the same
non-empty/control-free bound. Neither contract authenticates or authorizes its implementation.
Both implementations must remain immutable throughout a unit of work; readonly context fields do not freeze
the internal state of host-supplied objects.

Mint contexts once at the trusted host boundary and supply them to application operations. Browser request attributes
and any SDK ExecutionContext implementation belong to a host adapter. No package ConfigProvider needs registration.
ContainerFactory remains the only Core composition root.

A high-impact operation checks current authority, proof workspace/purpose/method/epoch, freshness and nonce use,
then performs its state change and audit under the host transaction. `isValidFor()` alone is intentionally incomplete
for authorization. Organization/workspace selection must come from trusted membership, never URL/Host/form input.

The [Core contract](core-contract.md) defines ownership, composition, compatibility and retained integration tests.
Select an independently verified published release, exact-pin it in Composer and regenerate the host lockfile.
The [release record](release-record.md) and [consumer inventory](core-consumers.json) retain source-anchored mappings
for reconciling Core imports, reflection/configuration consumers and test ownership with the selected package.
Host User, AuthenticatedPrincipal, SystemIdentity, membership validators, policies, provenance issuers and
SDK-facing adapters remain host-owned.

Validate database/concurrency/step-up/token/session/membership/portal/admin/CLI/MCP/worker/recovery integrations
against the resulting dependency tuple. The package test suite does not replace those host tests.

Rollback restores the previous dependency and namespace composition as one deployment. No schema migration is
required by this value package; cached fingerprints/proofs still require the host's normal expiration and policy.
