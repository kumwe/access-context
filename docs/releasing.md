# Releasing

Package compatibility follows SemVer. During pre-1.0 migration, consumers exact-pin independently verified releases.
The newest `## X.Y.Z` changelog record selects the next version; an optional Unreleased section is skipped.
Malformed headings fail closed. A record is a release intention, not proof of publication.

Pull requests and main pushes run `composer check` on PHP 8.5. The release workflow serializes main pushes without
cancellation, repeats the complete lane, and creates a lightweight version tag only at that tested merged SHA.
Only confirmed HTTP 404 responses authorize creating absent tags/releases; other API failures stop. An existing
published tag must be an ancestor with the matching release record. An unpublished tag must match the exact tested
SHA. Existing releases must report immutable published state or verification refuses them.
Agents do not merge, tag, publish or enable auto-merge.

The maintainer makes the first Packagist submission; Packagist then follows GitHub tags. No registry credential is
needed in CI. The source archive includes charter, handoff, public docs/examples, all three manifests and src.
The consumer gate verifies its exact file set/checksums, installs it as a dependency with Packagist disabled,
no development dependencies and authoritative autoloading, then resolves all public symbols and runs all examples.

A fresh independent verifier observes tag/source/archive/registry identity and clean-consumer results, producing
an external RELEASE-ATTESTATION.yaml. Do not insert that artifact's own digest into the handoff or republish to do so.
The App adoption PR retains that attestation unchanged and compares its extraction baseline before deleting classes.

A bad published version is never replaced. Publish a reviewed successor and record compatibility/security impact;
a host rollback restores its previously tested exact dependency tuple. Security changes follow the reporting policy.

## Maintainer setup before merging the 0.1.1 successor

1. Protect main with an active branch protection rule or ruleset requiring the reviewed pull request and
   package CI. The release job reads GitHub's ref_protected event context and refuses false or missing state
   before any tag or release mutation. This does not configure or alter repository permissions.
2. Enable immutable releases in the repository release settings before merging. GitHub applies that option
   to future releases; it does not retroactively protect 0.1.0. Keep the existing tag and release intact.
3. Review and merge the 0.1.1 changelog record. The existing release lane re-proves every package check,
   verifies tag ancestry and release-record identity, then publishes using its ordinary workflow token.
4. Require the final metadata check to pass: the exact version must be published, stable and immutable.
   The same check runs when an existing release is found. Mutable, draft, missing or contradictory metadata
   fails closed. If immutable releases were not enabled first, a failed post-publication check cannot undo
   publication; leave that version intact and prepare another reviewed successor.
5. Obtain a fresh independent RELEASE-ATTESTATION.yaml for the successor before a dependent package or App
   adopts it. A green package PR or a failed publication workflow is not release verification.

The workflow does not call the administrative immutable-releases settings endpoint or use an admin PAT.
Configuration is a maintainer action; publication verification uses the normal release metadata endpoint.
The development gate uses Bash and jq with isolated response fixtures. These tests exercise refusal logic
without contacting GitHub or claiming that protections are currently configured. Both helper scripts are
under the export-ignored tools directory, so the runtime archive and runtime dependency ceiling are unchanged.

All portable implementation tests remain package-owned. The handoff's exact App integration/security test
retention and later duplicate-test removal instructions are unchanged by this release-only correction.
