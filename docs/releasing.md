# Releasing

Package compatibility follows SemVer. During pre-1.0 migration, consumers exact-pin independently verified releases.
The newest `## X.Y.Z` changelog record selects the next version; an optional Unreleased section is skipped.
Malformed headings fail closed. A record is a release intention, not proof of publication.

Pull requests and main pushes run `composer check` on PHP 8.5. The release workflow serializes main pushes without
cancellation, repeats the complete lane, and creates a lightweight version tag only at that tested merged SHA.
Only confirmed HTTP 404 responses authorize creating absent tags/releases; other API failures stop. An existing
published tag must be an ancestor with the matching release record. An unpublished tag must match the exact tested
SHA. Existing releases are left immutable. Agents do not merge, tag, publish or enable auto-merge.

The maintainer makes the first Packagist submission; Packagist then follows GitHub tags. No registry credential is
needed in CI. The source archive includes charter, handoff, public docs/examples, all three manifests and src.
The consumer gate verifies its exact file set/checksums, installs it as a dependency with Packagist disabled,
no development dependencies and authoritative autoloading, then resolves all public symbols and runs all examples.

A fresh independent verifier observes tag/source/archive/registry identity and clean-consumer results, producing
an external RELEASE-ATTESTATION.yaml. Do not insert that artifact's own digest into the handoff or republish to do so.
The App adoption PR retains that attestation unchanged and compares its extraction baseline before deleting classes.

A bad published version is never replaced. Publish a reviewed successor and record compatibility/security impact;
a host rollback restores its previously tested exact dependency tuple. Security changes follow the reporting policy.
