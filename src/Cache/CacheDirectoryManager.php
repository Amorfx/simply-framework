<?php

namespace Simply\Core\Cache;

use Symfony\Component\Filesystem\Filesystem;

final class CacheDirectoryManager
{
    private static ?string $fingerprint = null;

    /**
     * Scope the cache paths to a fingerprint of what the cache is built from (see CacheFingerprint)
     */
    public static function setFingerprint(?string $fingerprint): void
    {
        self::$fingerprint = $fingerprint;
    }

    /**
     * Remove the whole cache. Concurrent requests see either the complete cache or no cache, never a half-deleted one.
     */
    public static function deleteCache(): void
    {
        self::trash(SIMPLY_CACHE_DIRECTORY);
    }

    /**
     * Remove what was cached for other fingerprints, including the layout of simply-framework <= 0.2.8
     */
    public static function pruneStaleFingerprints(): void
    {
        if (self::$fingerprint === null || !is_dir(SIMPLY_CACHE_DIRECTORY)) {
            return;
        }

        foreach (new \DirectoryIterator(SIMPLY_CACHE_DIRECTORY) as $entry) {
            if ($entry->isDot() || $entry->getFilename() === self::$fingerprint) {
                continue;
            }
            self::trash($entry->getPathname());
        }
    }

    public static function getCachePath(string $path): string
    {
        $directory = SIMPLY_CACHE_DIRECTORY;
        if (self::$fingerprint !== null) {
            $directory .= '/' . self::$fingerprint;
        }

        return $directory . '/' . $path;
    }

    /**
     * Rename then remove: rename is atomic, so readers never see a partially removed cache
     */
    private static function trash(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $trash = SIMPLY_CACHE_DIRECTORY . '.trash-' . bin2hex(random_bytes(6));
        // A concurrent request may have trashed it first
        if (!@rename($path, $trash)) {
            return;
        }

        (new Filesystem())->remove($trash);
    }
}
