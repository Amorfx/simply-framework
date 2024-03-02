<?php

namespace Simply\Core\Cache;

use Symfony\Component\Filesystem\Filesystem;

final class CacheDirectoryManager
{
    /**
     * @codeCoverageIgnore
     */
    public static function deleteCache(): void
    {
        $fs = new Filesystem();
        $fs->remove(SIMPLY_CACHE_DIRECTORY);
        $fs->mkdir(SIMPLY_CACHE_DIRECTORY);
    }

    public static function getCachePath(string $path): string
    {
        return SIMPLY_CACHE_DIRECTORY . '/' . $path;
    }
}
