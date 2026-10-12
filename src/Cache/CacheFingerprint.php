<?php

namespace Simply\Core\Cache;

use Composer\InstalledVersions;

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
     * @return array<string, array{0: ?string, 1: ?string}>
     */
    private static function getInstalledPackages(): array
    {
        if (!class_exists(InstalledVersions::class)) {
            return [];
        }

        $packages = [];
        foreach (InstalledVersions::getAllRawData() as $installed) {
            foreach ($installed['versions'] as $name => $package) {
                $packages[$name] = [$package['pretty_version'] ?? null, $package['reference'] ?? null];
            }
        }
        ksort($packages);

        return $packages;
    }
}
