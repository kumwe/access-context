<?php

/**
 * Proves the shape of the package refusal.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Case;

use InvalidArgumentException;
use Kumwe\Context\Exception\InvalidContext;
use Kumwe\Context\Tests\TestCase;
use ReflectionClass;

/**
 * Refusal type hierarchy and finality.
 *
 * @since  0.1.0
 */
final class InvalidContextTest extends TestCase
{
    /**
     * The refusal is a final InvalidArgumentException carrying the message it was given.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testIsAFinalInvalidArgumentException(): void
    {
        $error = new InvalidContext('A rule was broken.');
        $this->assertTrue($error instanceof InvalidArgumentException, 'Extends InvalidArgumentException.');
        $this->assertSame('A rule was broken.', $error->getMessage(), 'Message preserved.');
        $this->assertTrue((new ReflectionClass(InvalidContext::class))->isFinal(), 'Final.');
    }
}
