<?php

/**
 * Issue a system execution context for unattended work, with the actor made explicit by the host.
 *
 * Run with `php examples/system-context.php` after `composer install`. A host binds its own closed set of
 * unattended identities to the SystemActor contract, here as a backed enum, and the package fixes the strength
 * at `System` and the surface at `Background` so the context can never pass for a signed-in person.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Example;

use Kumwe\Context\Contract\SystemActor;
use Kumwe\Context\Exception\InvalidContext;
use Kumwe\Context\Value\ExecutionContext;
use Kumwe\Context\Value\SiteContext;
use stdClass;

require $argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php';

enum HostSystemActor: string implements SystemActor
{
    case Scheduler = 'system:scheduler';
    case Worker = 'system:worker';

    public function identifier(): string
    {
        return $this->value;
    }
}

$provenance = new stdClass();

$context = ExecutionContext::issueSystem(
    $provenance,
    HostSystemActor::Scheduler,
    SiteContext::default(),
    requestId: 'schedule-run-17',
);

$refusal = null;
try {
    // A hostile actor implementation cannot smuggle an unloggable token into the audit trail.
    ExecutionContext::issueSystem($provenance, new readonly class implements SystemActor {
        public function identifier(): string
        {
            return "system:\x00worker";
        }
    }, SiteContext::default(), 'schedule-run-18');
} catch (InvalidContext $error) {
    $refusal = $error->getMessage();
}

echo json_encode([
    'context' => $context->toArray(),
    'is_system' => $context->isSystem(),
    'actor_is_enum_case' => $context->systemActor() === HostSystemActor::Scheduler,
    'principal' => $context->principal(),
    'hostile_actor_refused_with' => $refusal,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), "\n";
