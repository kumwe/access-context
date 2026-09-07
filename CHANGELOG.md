# Changelog

Delivered package changes, newest first. Records describe the candidate release and its required proof.
Publication follows only after the complete
quality lane passes. The newest `## X.Y.Z` heading is the release record: merging it to `main` is the release, and the
`Release on record` workflow tags and publishes exactly that version.

## 0.1.1

- Unify PR and post-rebase release gates, dynamic release identity, tested publication retries,
  and administrator setup across the package family. Preserve immutable release and dependency evidence requirements.

- NRM-2026-005: require protected main before publication and verify the exact release is published,
  stable and immutable. Preserve existing tag ancestry and changelog checks; refuse mutable release metadata.
- Record a successor to 0.1.0 with refreshed release manifests and handoff. Runtime code, public signatures
  and the package/App test ownership split are unchanged. Maintainers enable protections before merging.

## 0.1.0

- **Complete the unfinished extraction (`NRM-2026-005`).** Fix explicit-null manifest checks; reject a step-up
  workspace without its enclosing organization; clarify that proof freshness checks are not authorization.
  Supply the missing charter, complete member/API documentation, security/release guidance and PR-linked handoff.
  Add the full CI/release lane and exact built archive installed as a dependency in an isolated no-dev consumer.
  Reject raw scope control bytes before trim can erase them; preserve normalization for valid space-padded values.
  Repair the pre-existing style/line-width and strict-analysis failures rather than weakening the existing gates.

- **Extraction of the canonical access context from kumwe/app (`KUMWE-MIG-2026-004`).** The site,
  organization, workspace and membership values, the authenticated-surface and authentication-strength
  vocabularies, the step-up proof and the execution context moved from `Kumwe\App\Application\Authorization\*`
  to `Kumwe\Context\Value\*` with their identifier grammars, refusal messages and fingerprints retained,
  subject to the explicitly documented input and proof hardening above. App keeps authentication, sessions,
  tokens, grants, membership lookup, site selection, the closed
  set of system identities and every authorization decision.
- **The Authorization-Identity cycle is broken by two contracts.** `Kumwe\Context\Contract\Principal` exposes
  the subject, security epoch, provenance check and the two authority fingerprints an execution context and a
  policy read; `Kumwe\Context\Contract\SystemActor` makes an unattended actor explicit through one stable
  identifier. The execution context carries a `Principal` and a `SystemActor` instead of the App's
  authenticated-principal class and system-identity enum, validates the claims it is handed, and implements
  no host interface.
- **Every refusal is a `Kumwe\Context\Exception\InvalidContext`**, an `InvalidArgumentException` subclass, so
  callers that already catch the base type keep working and callers that want the package's own type have one.
- **Additive facts for consumers.** `AuthenticationStrength::isHuman()` and `satisfies()` (the credential-presence
  ordering `BearerToken` < `Password` < `MultiFactor`, with `System` incomparable), `equals()` on every scope
  value and on the membership snapshot, `ExecutionContext::isSystem()`, `systemActor()`, and redacted
  `toArray()` exports on the membership snapshot, the step-up proof and the execution context. Exports carry
  no provenance, session identifier, nonce or fingerprint.
- **Deliberate omissions recorded as decisions.** The PSR-7 request-attribute constant stays with the App's
  delivery layer; the extension-SDK execution-context interface is implemented by an App adapter; the
  membership freshness port, the system principal and the closed system-identity set remain App authority.
- **Package lane and release automation.** `composer check` runs Composer validation, syntax, line width,
  member documentation, the architecture boundary, the reflected public API manifest, manifest agreement,
  the production autoload smoke, PSR-12, PHPStan level max with strict and deprecation rules, and the
  dependency-free behavioural suite. CI repeats the lane on PHP 8.5, proves the no-dev classmap-authoritative
  autoloader, builds the consumer archive, verifies its exact file set and installs it as a clean consumer.
  `Release on record` tags and publishes the version this heading records after a human merge to `main`.
