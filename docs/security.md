# Security and supported use

The maintained release line targets PHP 8.5. Report sensitive defects privately through the repository's GitHub
Security Advisory reporting when available; otherwise contact the maintainer privately before sharing exploit
details.
Never include live credentials, session identifiers, provenance objects, proof nonces or real user data in reports.

Values describe authenticated facts; they do not establish authentication or authorize a caller. Hosts must enforce
provenance, current security/membership generations, scope, purpose, permission and one-time proof consumption.
Do not accept PHP unserialization or an exported JSON record as an authenticated context. Use `toArray()` for
redacted
logs and keep access to explicit session/nonce getters restricted to the relevant trusted host adapter.

Constructor refusals name violated rules without submitted input. The library performs no logging or secret I/O.
Dependency auditing, hostile-input behavior tests and the exact no-dev archive consumer are required release gates.
