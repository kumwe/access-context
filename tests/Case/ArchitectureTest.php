<?php

/**
 * Package-owned proof that the source tree stays inside its boundary.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Case;

use Kumwe\Context\Tests\TestCase;
use ReflectionClass;

/**
 * Boundary assertions over every source file and exported symbol.
 *
 * @since  0.1.0
 */
final class ArchitectureTest extends TestCase
{
    /**
     * Read every source file keyed by repository-relative path.
     *
     * @return  array<string, string>  File contents.
     *
     * @since   0.1.0
     */
    private function sources(): array
    {
        $root = dirname(__DIR__, 2);
        $sources = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root . '/src', \FilesystemIterator::SKIP_DOTS),
        );
        foreach ($iterator as $file) {
            if ($file instanceof \SplFileInfo && $file->isFile()) {
                $relative = substr($file->getPathname(), strlen($root) + 1);
                $sources[$relative] = (string) file_get_contents($file->getPathname());
            }
        }
        ksort($sources, SORT_STRING);
        $this->assertTrue($sources !== [], 'The source tree is not empty.');

        return $sources;
    }

    /**
     * No source file names the App, the extension SDK, a framework, persistence, HTTP delivery or ambient state.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testSourceImportsNothingOutsideTheBoundary(): void
    {
        $forbidden = [
            'Kumwe\\App\\', 'Kumwe\\Extension\\', 'Doctrine\\', 'Psr\\', 'Laminas\\', 'Mezzio\\', 'Symfony\\',
            'ServerRequestInterface', 'ContainerInterface', 'ConfigProvider', '$_SERVER', '$_GET', '$_POST',
            'getenv(', 'time()', 'new DateTimeImmutable(', 'random_bytes(', 'class_exists(', 'class_alias(',
        ];
        foreach ($this->sources() as $path => $code) {
            $this->assertTrue(str_starts_with($code, "<?php\n\ndeclare(strict_types=1);\n"), "{$path} is strict.");
            foreach ($forbidden as $needle) {
                $this->assertStringExcludes($needle, $code, "{$path} must not reference {$needle}.");
            }
        }
    }

    /**
     * Every exported symbol is a final class, an interface or a backed enum under the canonical namespace.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testEveryExportedSymbolIsFinalOrAContract(): void
    {
        $manifest = json_decode(
            (string) file_get_contents(dirname(__DIR__, 2) . '/resources/public-api/v1.json'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $symbols = is_array($manifest) && is_array($manifest['symbols'] ?? null)
            ? array_keys($manifest['symbols']) : [];
        $this->assertTrue($symbols !== [], 'Symbols are exported.');
        foreach ($symbols as $fqcn) {
            $this->assertTrue(is_string($fqcn) && str_starts_with($fqcn, 'Kumwe\\Context\\'), 'Canonical root.');
            /** @var class-string $fqcn */
            $reflection = new ReflectionClass($fqcn);
            $this->assertTrue(
                $reflection->isInterface() || $reflection->isEnum() || $reflection->isFinal(),
                "{$fqcn} is final, an interface or an enum.",
            );
            $this->assertFalse($reflection->isTrait(), "{$fqcn} is not a trait.");
            if (enum_exists($fqcn)) {
                $this->assertTrue((new \ReflectionEnum($fqcn))->isBacked(), "{$fqcn} is backed.");
            }
            foreach ($reflection->getInterfaceNames() as $interface) {
                $this->assertTrue(
                    str_starts_with($interface, 'Kumwe\\Context\\')
                        || in_array($interface, ['UnitEnum', 'BackedEnum', 'Stringable', 'Throwable'], true),
                    "{$fqcn} implements only package contracts or engine enum interfaces.",
                );
            }
            $parent = $reflection->getParentClass();
            $this->assertTrue(
                $parent === false || $parent->getName() === 'InvalidArgumentException',
                "{$fqcn} extends nothing but the SPL refusal base.",
            );
        }
    }
}
