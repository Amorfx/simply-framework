<?php

namespace Simply\Core\Cache;

use Composer\Autoload\ClassLoader;

/**
 * Identify what the cache is built from, so that a new deployment or configuration gets a fresh cache
 * even when WP_DEBUG is false and no file is checked for changes.
 */
final class CacheFingerprint
{
    /**
     * @param array<mixed> $context what the container is built from besides the installed packages
     * @param array<string, array{0: ?string, 1: ?string}>|null $installedPackages name => [version, reference], read from Composer when null
     */
    public static function compute(array $context, ?array $installedPackages = null): string
    {
        $installedPackages ??= self::getInstalledPackages();

        return substr(hash('xxh128', serialize([$context, $installedPackages])), 0, 16);
    }

    /**
     * Packages of the Composer installations that load Simply. Other autoloaders are ignored: WP-CLI runs from a phar
     * with its own packages, which would give WP-CLI and web requests different fingerprints.
     *
     * @return array<string, array{0: ?string, 1: ?string}>
     */
    private static function getInstalledPackages(): array
    {
        // The first declared ClassLoader wins: it may come from an older Composer (a WP-CLI phar, a plugin's own vendor)
        // @phpstan-ignore function.alreadyNarrowedType
        if (!method_exists(ClassLoader::class, 'getRegisteredLoaders')) {
            return [];
        }

        $packages = [];
        foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
            $installedFile = $vendorDir . '/composer/installed.php';
            if ($loader->findFile(self::class) === false || !is_file($installedFile)) {
                continue;
            }
            $installed = require $installedFile;
            foreach ($installed['versions'] as $name => $package) {
                $packages[$name] = [$package['pretty_version'] ?? null, $package['reference'] ?? null];
            }
        }
        ksort($packages);

        return $packages;
    }
}
