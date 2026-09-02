<?php

declare(strict_types=1);

namespace Kumwe\Context\Exception;

use InvalidArgumentException;

/**
 * Raised when a value, or the combination of values in a context, cannot represent an established fact.
 *
 * Every refusal in this package is one of these, so a consumer can catch the package's own type while a caller
 * that already handles `InvalidArgumentException` keeps working unchanged. The message names the rule that was
 * broken and never echoes the offending input, so a refusal can be logged without leaking what was submitted.
 *
 * @since  0.1.0
 */
final class InvalidContext extends InvalidArgumentException
{
}
