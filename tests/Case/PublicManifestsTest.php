<?php

/**
 * Holds the three package manifests to the source tree, the changelog and each other.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Case;

use Kumwe\Context\Tests\TestCase;

/**
 * Manifest agreement with the canonical surface.
 *
 * @since  0.1.0
 */
final class PublicManifestsTest extends TestCase
{
    /**
     * Decode a repository JSON object.
     *
     * @param   string  $path  Repository-relative path.
     *
     * @return  array<string, mixed>  Decoded object.
     *
     * @since   0.1.0
     */
    private function json(string $path): array
    {
        $bytes = file_get_contents(dirname(__DIR__, 2) . '/' . $path);
        $this->assertTrue(is_string($bytes), "{$path} must be readable.");
        $decoded = json_decode((string) $bytes, true, 512, JSON_THROW_ON_ERROR);
        $this->assertTrue(is_array($decoded) && !array_is_list($decoded), "{$path} must be an object.");
        /** @var array<string, mixed> $decoded */

        return $decoded;
    }

    /**
     * The public API manifest exports exactly the PSR-4 source tree, every symbol loads, and no App name leaks.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testPublicApiExportsExactlyTheSourceTree(): void
    {
        $manifest = $this->json('resources/public-api/v1.json');
        $this->assertSame('kumwe-package-public-api/v1', $manifest['schema'] ?? null, 'Schema.');
        $this->assertSame('kumwe/access-context', $manifest['package'] ?? null, 'Package.');
        $this->assertSame('Kumwe\\Context\\', $manifest['namespace'] ?? null, 'Namespace.');
        $this->assertSame('src', $manifest['digest_of'] ?? null, 'Digest scope.');

        $root = dirname(__DIR__, 2);
        $expected = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root . '/src', \FilesystemIterator::SKIP_DOTS),
        );
        foreach ($iterator as $file) {
            if ($file instanceof \SplFileInfo && $file->getExtension() === 'php') {
                $relative = substr($file->getPathname(), strlen($root) + 5, -4);
                $expected[] = 'Kumwe\\Context\\' . str_replace('/', '\\', $relative);
            }
        }
        sort($expected, SORT_STRING);

        /** @var array<string, array<string, mixed>> $symbols */
        $symbols = is_array($manifest['symbols'] ?? null) ? $manifest['symbols'] : [];
        $this->assertSame($expected, array_keys($symbols), 'Every source type, and only source types, is exported.');
        $this->assertSame(11, count($symbols), 'The first release exports eleven symbols.');
        foreach ($symbols as $fqcn => $entry) {
            $loaded = match ($entry['kind'] ?? null) {
                'class' => class_exists($fqcn),
                'interface' => interface_exists($fqcn),
                'enum' => enum_exists($fqcn),
                default => false,
            };
            $this->assertTrue($loaded, "{$fqcn} must load as its recorded kind.");
            $this->assertSame('stable', $entry['stability'] ?? null, "{$fqcn} is stable.");
            $this->assertTrue(array_key_exists('deprecated', $entry), "{$fqcn} declares deprecation state.");
            $this->assertNull($entry['deprecated'], "{$fqcn} is not deprecated.");
            $this->assertTrue(is_file($root . '/' . ($entry['file'] ?? '')), "{$fqcn} names its file.");
        }
        $this->assertSame(
            ['Kumwe\\Context\\Contract\\Principal', 'Kumwe\\Context\\Contract\\SystemActor'],
            $manifest['extension_points'] ?? null,
            'The two contracts are the extension points.',
        );
        $bytes = (string) file_get_contents($root . '/resources/public-api/v1.json');
        $this->assertStringExcludes('Kumwe\\App\\', $bytes, 'No App namespace.');
        $this->assertStringExcludes('Kumwe\\Extension\\', $bytes, 'No SDK namespace.');
    }

    /**
     * Every capability claims exported symbols only, and every exported symbol is claimed once.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testCapabilitiesClaimEveryExportedSymbol(): void
    {
        $api = $this->json('resources/public-api/v1.json');
        $capabilities = $this->json('resources/capabilities/v1.json');
        $this->assertSame('kumwe-package-capabilities/v1', $capabilities['schema'] ?? null, 'Schema.');
        $this->assertSame($api['release'] ?? null, $capabilities['release'] ?? null, 'Same release.');
        $this->assertTrue(array_key_exists('native_requirements', $capabilities), 'Native requirement is explicit.');
        $this->assertNull($capabilities['native_requirements'], 'No native requirement.');

        $claimed = [];
        foreach (is_array($capabilities['capabilities'] ?? null) ? $capabilities['capabilities'] : [] as $entry) {
            $id = is_array($entry) ? ($entry['id'] ?? null) : null;
            $this->assertTrue(is_string($id) && str_starts_with($id, 'access-context.'), 'Prefixed identifier.');
            foreach (is_array($entry) && is_array($entry['symbols'] ?? null) ? $entry['symbols'] : [] as $symbol) {
                $this->assertFalse(isset($claimed[$symbol]), "{$symbol} is claimed once.");
                $claimed[$symbol] = true;
            }
        }
        $exported = array_keys(is_array($api['symbols'] ?? null) ? $api['symbols'] : []);
        sort($exported, SORT_STRING);
        $claimedNames = array_keys($claimed);
        sort($claimedNames, SORT_STRING);
        $this->assertSame($exported, $claimedNames, 'Claimed symbols are exactly the exported ones.');
    }

    /**
     * The service map promises no provider, gives the reason, and the promise holds in source.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testServiceMapDeclaresNoProviderWithReason(): void
    {
        $map = $this->json('resources/service-map/v1.json');
        $this->assertSame('kumwe-package-service-map/v1', $map['schema'] ?? null, 'Schema.');
        $this->assertTrue(array_key_exists('config_provider', $map), 'Provider absence is explicit.');
        $this->assertNull($map['config_provider'], 'No provider.');
        $reason = $map['provider_absence_reason'] ?? null;
        $this->assertTrue(is_string($reason) && $reason !== '', 'A reason is given.');
        $this->assertSame([], $map['factories'] ?? null, 'No factory.');
        $this->assertSame([], $map['aliases'] ?? null, 'No alias.');
        $this->assertSame([], $map['delegators'] ?? null, 'No delegator.');
        $this->assertSame([], $map['configuration_keys'] ?? null, 'No configuration key.');
        $this->assertFalse(class_exists('Kumwe\\Context\\ConfigProvider'), 'No provider class exists.');
    }

    /**
     * The release every manifest records is the newest changelog heading.
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function testReleaseIsTheNewestChangelogRecord(): void
    {
        $changelog = (string) file_get_contents(dirname(__DIR__, 2) . '/CHANGELOG.md');
        $this->assertTrue(
            preg_match('/^## ([0-9]+\.[0-9]+\.[0-9]+)$/m', $changelog, $match) === 1,
            'The changelog records a release heading.',
        );
        foreach (['public-api', 'capabilities', 'service-map'] as $manifest) {
            $this->assertSame(
                $match[1],
                $this->json("resources/{$manifest}/v1.json")['release'] ?? null,
                "resources/{$manifest}/v1.json records the changelog release.",
            );
        }
    }
}
