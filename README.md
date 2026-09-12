# Kumwe Access Context

[![Packagist version][version-badge]][package]
[![Context CI][ci-badge]][ci]
[![PHP requirement][php-badge]][package]
[![License][license-badge]](LICENSE)

[version-badge]: https://img.shields.io/packagist/v/kumwe/access-context
[package]: https://packagist.org/packages/kumwe/access-context
[ci-badge]: https://github.com/kumwe/access-context/actions/workflows/ci.yml/badge.svg?branch=main
[ci]: https://github.com/kumwe/access-context/actions/workflows/ci.yml
[php-badge]: https://img.shields.io/packagist/dependency-v/kumwe/access-context/php
[license-badge]: https://img.shields.io/packagist/l/kumwe/access-context

Immutable access-context values and actor contracts, supplied explicitly for each unit of work.
Requires PHP 8.5. The canonical namespace is `Kumwe\Context`; there are no runtime package dependencies.

The package carries established actor, site, organization, workspace, membership, authentication and trace facts.
The host authenticates, resolves membership, validates provenance and makes every authorization decision.
It does not provide grants, capability policies, credentials, sessions, persistence or ambient context lookup.

## Installation and standalone use

Install the published Composer package. Pin the selected pre-1.0 version for a reproducible host deployment:

```sh
composer require kumwe/access-context:0.1.2
```

For the source checkout, run `composer install`, then this complete example:

```php
<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use Kumwe\Context\Contract\SystemActor;
use Kumwe\Context\Value\ExecutionContext;
use Kumwe\Context\Value\SiteContext;

enum BackgroundActor: string implements SystemActor
{
    case Worker = 'system:worker';

    public function identifier(): string
    {
        return $this->value;
    }
}

$authority = new stdClass();
$context = ExecutionContext::issueSystem(
    $authority,
    BackgroundActor::Worker,
    SiteContext::fromString('namibia'),
    'operation-001',
);

assert($context->hasProvenance($authority));
echo json_encode($context->toArray(), JSON_THROW_ON_ERROR), PHP_EOL;
```

Run `composer examples` for human, background and step-up examples. Their host identities are illustrative;
production authentication and the set of permitted system actors belong to the consuming application.

## Construction and lifetime

There is no ConfigProvider, factory registration, service alias or configuration key. These are values, enums,
contracts and one exception; register host adapters in the composition root and pass contexts as operation
arguments.
Never register a shared current actor, membership, request or tenant service. Implement `Contract\Principal` and
`Contract\SystemActor` over the host's established identity model. Principal implementations must remain immutable
for the unit of work and compare provenance by object identity.

## Public surface

- `ExecutionContext`: explicit human/system issuance, scope facts, child contexts, fingerprints and redacted
exports.
- `SiteContext`, `OrganizationContext`, `WorkspaceContext`, `MembershipContext`: distinct bounded scope facts.
- `AuthenticationStrength`, `AuthenticatedSurface`, `StepUpProof`: authentication facts and freshness bindings.
- `Principal`, `SystemActor`: host implementation ports.
- `InvalidContext`: consistent argument refusal; messages identify the rule without echoing submitted input.

[Complete public API](docs/public-api.md), [architecture](docs/architecture.md),
[host integration](docs/integration.md), [Core contract](docs/core-contract.md),
[release record](docs/release-record.md).
The three manifests under `resources/` record every exported symbol, semantic capability and provider decision.

## Guarantees and limits

Scopes normalize by trimming/lowercasing before their 191-character grammar check; site and organization remain
different types. Contexts hold exactly one human or explicit system actor. Background contexts cannot impersonate
human strength or another surface. A workspace proof requires an organization. Multi-factor contexts require a
proof
bound to actor, session, site, organization, workspace and security epoch.

`StepUpProof::isValidFor()` checks its supplied actor/session/site/organization and trusted time interval only.
The host must additionally enforce workspace, purpose, epoch, method, nonce consumption and policy at action time.
Expiry is exclusive; verification is inclusive; intervals cannot exceed fifteen minutes. No method reads a clock.
Fingerprints are deterministic and credential-sensitive or approval-stable as documented; they are not credentials.
`toArray()` omits provenance, session identifiers, proof nonces and fingerprints. PHP object serialization is not a
trusted transport or storage format; only explicit redacted exports are supported for logging.

The package performs no I/O, transaction, authorization, retries or membership refresh. App must refresh security
and membership state inside the actual mutation transaction and preserve the three-database/integration test lane.

## Development and release

```sh
composer install
composer check
```

The full gate includes PHPStan max/strict rules, coding/member documentation checks, behavior/API/architecture
tests,
security audit, release-record parser and an exact archive installed as a dependency in an isolated no-dev
consumer.
The tooling additionally needs mbstring, tokenizer, XMLWriter and ZIP; these are development requirements, not
runtime dependencies of the value package. `composer clean-consumer` runs the artifact check separately.

Published versions and source CI status are linked above. Core integration is verified in the consuming
repository against its selected package version and retained integration suites.
[Releasing and compatibility](docs/releasing.md) describes immutable release-on-record and exact pre-1.0 pins.
[Security policy](docs/security.md) describes sensitive data and reporting. Apache-2.0; see [LICENSE](LICENSE).

Raw control bytes in site, organization and workspace inputs are rejected before whitespace normalization.
This includes leading/trailing NUL, tabs, newlines, carriage returns and DEL; valid space-padded values still
normalize.
