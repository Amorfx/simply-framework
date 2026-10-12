<?php

namespace Simply\Core\Cache;

use Symfony\Component\Filesystem\Filesystem;

final class CacheDirectoryManager
{
    private const TRASH_PREFIX = '.trash-';

    private static ?string $fingerprint = null;

    /**
     * Scope the cache paths to a fingerprint of what the cache is built from (see CacheFingerprint)
     */
    public static function setFingerprint(?string $fingerprint): void
    {
        self::$fingerprint = $fingerprint;
    }

    /**
     * Remove the whole cache. Concurrent requests see either a complete fingerprint directory or none, never a half-deleted one.
     */
    public static function deleteCache(): void
    {
        self::trashEntries(static fn(string $name): bool => true);
    }

    /**
     * Remove what was cached for other fingerprints, including the layout of simply-framework <= 0.2.8
     */
    public static function pruneStaleFingerprints(): void
    {
        $fingerprint = self::$fingerprint;
        if ($fingerprint === null) {
            return;
        }

        self::trashEntries(static fn(string $name): bool => $name !== $fingerprint);
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
     * The cache directory itself is kept: it may be a mount point (Docker volume), which cannot be renamed
     *
     * @param callable(string): bool $shouldTrash
     */
    private static function trashEntries(callable $shouldTrash): void
    {
        if (!is_dir(SIMPLY_CACHE_DIRECTORY)) {
            return;
        }

        foreach (new \DirectoryIterator(SIMPLY_CACHE_DIRECTORY) as $entry) {
            $name = $entry->getFilename();
            // Trash entries are being removed by the request that renamed them
            if ($entry->isDot() || str_starts_with($name, self::TRASH_PREFIX) || !$shouldTrash($name)) {
                continue;
            }
            self::trash($entry->getPathname());
        }
    }

    /**
     * Rename then remove: rename is atomic, so readers never see a partially removed entry.
     * The trash stays in the cache directory, so the rename never crosses a filesystem boundary.
     */
    private static function trash(string $path): void
    {
        $trash = SIMPLY_CACHE_DIRECTORY . '/' . self::TRASH_PREFIX . bin2hex(random_bytes(6));
        // A concurrent request may have trashed it first
        if (!@rename($path, $trash)) {
            return;
        }

        (new Filesystem())->remove($trash);
    }
}
