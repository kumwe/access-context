<?php

/**
 * Hold the package to its architectural boundary.
 *
 * The rules are the ones the charter states: the canonical namespace is the only namespace, every file declares
 * strict types, every class is final and every contract is an interface, nothing under src/ imports the App,
 * a framework, a persistence library or the extension SDK, nothing reads a clock, randomness, the environment,
 * a request or the filesystem, and there is no container integration to register. A boundary that is not
 * checked is a boundary that drifts.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$source = $root . '/src';
$prefix = 'Kumwe\\Context\\';
$failures = [];
$files = [];

$forbiddenNamespaces = [
    'Kumwe\\App\\', 'Kumwe\\Extension\\', 'Doctrine\\', 'Psr\\', 'Laminas\\', 'Mezzio\\', 'Symfony\\',
    'Illuminate\\', 'Twig\\', 'Monolog\\', 'Ramsey\\',
];
$forbiddenCalls = [
    'time(', 'date(', 'microtime(', 'hrtime(', 'new DateTime(', 'new DateTimeImmutable(', 'new \\DateTime(',
    'new \\DateTimeImmutable(', 'random_bytes(', 'random_int(', 'mt_rand(', 'rand(', 'getenv(', '$_SERVER',
    '$_GET', '$_POST', '$_COOKIE', '$_ENV', '$_REQUEST', '$_SESSION', '$GLOBALS', 'file_get_contents(', 'fopen(',
    'header(', 'class_exists(', 'class_alias(', 'interface_exists(', 'enum_exists(', 'eval(', 'serialize(',
    'unserialize(', 'session_start(', 'session_id(', 'session_regenerate_id(', 'setcookie(',
];

if (!is_dir($source)) {
    fwrite(STDERR, "Architecture check failed: src/ is missing.\n");
    exit(1);
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
);
foreach ($iterator as $file) {
    if ($file instanceof SplFileInfo && $file->isFile()) {
        $files[] = $file->getPathname();
    }
}
sort($files);

foreach ($files as $path) {
    $relative = substr($path, strlen($root) + 1);
    if (!str_ends_with($relative, '.php')) {
        $failures[] = "{$relative} is not a PHP source file; src/ ships code only.";
        continue;
    }
    $code = file_get_contents($path);
    if ($code === false) {
        $failures[] = "{$relative} cannot be read.";
        continue;
    }

    if (!str_starts_with($code, "<?php\n\ndeclare(strict_types=1);\n\nnamespace ")) {
        $failures[] = "{$relative} must open with the PHP tag, strict types and its namespace.";
    }

    $expectedNamespace = rtrim($prefix . str_replace('/', '\\', dirname(substr($relative, 4))), '\\');
    $expectedName = basename($relative, '.php');
    if (preg_match('/^namespace ([^;]+);$/m', $code, $namespace) !== 1 || $namespace[1] !== $expectedNamespace) {
        $failures[] = "{$relative} must declare namespace {$expectedNamespace}.";
    }

    $declarations = preg_match_all(
        '/^(final readonly class|final class|interface|enum) (\w+)(?:: (?:string|int))?/m',
        $code,
        $declared,
        PREG_SET_ORDER,
    );
    if ($declarations !== 1) {
        $failures[] = "{$relative} must declare exactly one final class, interface or backed enum.";
    } elseif ($declared[0][2] !== $expectedName) {
        $failures[] = "{$relative} must declare {$expectedName}, not {$declared[0][2]}.";
    }
    if (preg_match('/^(abstract class|trait|class|readonly class) /m', $code) === 1) {
        $failures[] = "{$relative} declares a non-final class, an abstract class or a trait.";
    }
    if (preg_match('/^enum \w+\s*$/m', $code) === 1) {
        $failures[] = "{$relative} declares an unbacked enum; every case must carry a stable value.";
    }

    preg_match_all('/^use ([^;]+);$/m', $code, $imports);
    foreach ($imports[1] as $import) {
        $import = trim($import);
        if (str_contains($import, '\\') && !str_starts_with($import, $prefix)) {
            $failures[] = "{$relative} imports a namespace outside the package: {$import}.";
        }
    }
    foreach ($forbiddenNamespaces as $forbiddenNamespace) {
        if (str_contains($code, $forbiddenNamespace)) {
            $failures[] = "{$relative} references the forbidden namespace {$forbiddenNamespace}.";
        }
    }
    foreach ($forbiddenCalls as $call) {
        if (preg_match('/(?<![\w>$\\\\])' . preg_quote($call, '/') . '/', $code) === 1) {
            $failures[] = "{$relative} reaches for ambient state or a hidden side effect: {$call}.";
        }
    }
    if (str_contains($code, '@internal')) {
        $failures[] = "{$relative} is marked @internal; this package exports every symbol it ships.";
    }
    if (str_contains($code, 'ConfigProvider') || str_contains($code, 'ContainerInterface')) {
        $failures[] = "{$relative} mentions container integration; this package has none by decision.";
    }
}

foreach (['ConfigProvider.php', 'Container'] as $forbiddenPath) {
    if (file_exists($source . '/' . $forbiddenPath)) {
        $failures[] = "src/{$forbiddenPath} exists; the package registers no services.";
    }
}

if ($files === []) {
    $failures[] = 'src/ is empty.';
}

if ($failures !== []) {
    fwrite(
        STDERR,
        'Architecture check failed (' . count($failures) . " finding(s)):\n - " . implode("\n - ", $failures) . "\n",
    );
    exit(1);
}

echo 'Architecture check passed: ' . count($files) . " source files inside the Kumwe\\Context boundary.\n";
