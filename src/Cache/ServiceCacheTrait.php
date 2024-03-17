<?php

namespace Simply\Core\Cache;

use Simply\Core\Contract\CacheInterface;

/**
 * @codeCoverageIgnore
 */
trait ServiceCacheTrait
{
    public function getCacheService(): CacheInterface
    {
        return $this->container->get(CacheInterface::class);
    }
}
