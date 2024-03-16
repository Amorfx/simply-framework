<?php

namespace Simply\Core\Cache;

use InvalidArgumentException;
use Simply\Core\Contract\CacheInterface;

class CacheFactory
{
    /**
     * @var array{type: string, host: string, port: int}
     */
    private array $configuration;

    /**
     * @param array{type: string, host: string, port: int} $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function createCacheObject(): CacheInterface
    {
        switch ($this->configuration['type']) {
            case 'redis':
                return new RedisCache($this->configuration['host'], $this->configuration['port']);

            case 'memcached':
                return new MemcachedCache($this->configuration['host'], $this->configuration['port']);
        }

        throw new InvalidArgumentException('Invalid cache type');
    }
}
