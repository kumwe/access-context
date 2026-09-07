# Releasing Access Context

Follow the [Package release standard](package-release-standard.md) for the shared
quality gate, changelog parsing, publication and retry behavior. Complete the
[repository release setup](repository-release-setup.md) with an administrator
session before merging a release record:

```bash
bash tools/configure-release-repositories.sh --check kumwe/access-context
bash tools/configure-release-repositories.sh --apply kumwe/access-context
```

The required CI check is **Package gate**. Maintainers rebase reviewed PRs into the
repository's current default branch; the release workflow reruns the same quality
gate on the resulting commit and derives its release identity from that run.
A release intention in CHANGELOG.md is not evidence that publication occurred.
Keep work that is not ready for publication under `## Unreleased`.

Package compatibility follows SemVer. During pre-1.0 migration, consumers exact-pin
independently verified releases.

## Artifact and consumer verification

The source archive includes the charter, handoff, public docs and examples, all
three manifests and production sources. The consumer gate verifies its exact file
set and checksums, installs it as a dependency with Packagist disabled, no development
dependencies and authoritative autoloading, then resolves all public symbols and
runs every shipped example. Package checkout tests do not replace this proof.

Keep the independent attestation outside the artifact it hashes. Do not insert that
attestation's own digest into the handoff or republish to do so. The App adoption PR
retains the attestation unchanged and compares its extraction baseline before
removing classes. Portable implementation tests remain package-owned; the handoff's
App integration/security test retention and later duplicate-test removal rules remain.

A host rollback restores its previously tested exact dependency tuple. Security
changes follow the reporting policy and require a corrected successor release.

## Publication evidence and recovery

The maintainer performs the initial Packagist submission. Its GitHub integration
then follows tags without a registry credential in CI. Before dependent publication
or App adoption, a fresh independent verifier must bind the exact published
source/tag, archive digest, manifests, registry coordinate, license/security and
clean-consumer results in an external RELEASE-ATTESTATION.yaml. The artifact and
handoff must not invent their own final commit, checksum or publication evidence.

Use the current release workflow on the default branch to retry after correcting
repository settings. Historical mutable releases remain unchanged: enabling
immutability affects future publications, so a mutable version requires an unused
successor. Never move or delete a published tag or replace a released artifact.
An unpublished tag can be completed only on the exact commit tested by the retry.
A green PR does not replace the default-branch release result or independent
verification. Administrator credentials do not belong in Actions.
