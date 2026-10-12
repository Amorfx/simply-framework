<?php

namespace Simply\Core\Cache;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final class CacheDirectoryManager
{
    private const TRASH_PREFIX = '.trash-';

    /**
     * Seconds after which the cache of another fingerprint is considered unused. Several fingerprints can be live at once
     * (WP-CLI run with --skip-themes, a multisite with one theme per site, a rolling deployment): pruning them right away
     * would make each one rebuild the cache of the other.
     */
    private const STALE_AFTER = 3600;

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
        self::trashEntries(static fn(\SplFileInfo $entry): bool => true);
    }

    /**
     * Remove what was cached for other fingerprints and not modified for a while, including the layout of simply-framework <= 0.2.8
     */
    public static function pruneStaleFingerprints(): void
    {
        $fingerprint = self::$fingerprint;
        if ($fingerprint === null) {
            return;
        }

        $staleBefore = time() - self::STALE_AFTER;
        self::trashEntries(static fn(\SplFileInfo $entry): bool =>
            $entry->getFilename() !== $fingerprint && $entry->getMTime() < $staleBefore);
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
     * @param callable(\SplFileInfo): bool $shouldTrash
     */
    private static function trashEntries(callable $shouldTrash): void
    {
        if (!is_dir(SIMPLY_CACHE_DIRECTORY)) {
            return;
        }

        foreach (new \DirectoryIterator(SIMPLY_CACHE_DIRECTORY) as $entry) {
            // Dot entries are being removed by another request: our trash, or the ".!<random>" name Symfony Filesystem::remove() renames to
            if (str_starts_with($entry->getFilename(), '.') || !$shouldTrash($entry)) {
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

        try {
            (new Filesystem())->remove($trash);
        } catch (IOException|\UnexpectedValueException) {
            // Best effort: the cache is already out of the way, a leftover (e.g. files owned by another user) must not break the request
        }
    }
}
