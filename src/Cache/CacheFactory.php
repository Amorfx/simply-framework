<?php

namespace Simply\Core\Cache;

use Simply\Core\Contract\CacheInterface;

class CacheFactory {
    /**
     * @var array
     */
    private $configuration;

    public function __construct(array $configuration) {
        $this->configuration = $configuration;
    }

    public function createCacheObject(): CacheInterface {
        switch ($this->configuration['type']) {
            case 'redis':
                return new RedisCache($this->configuration['host'], $this->configuration['port']);

            case 'memcached':
                return new MemcachedCache($this->configuration['host'], $this->configuration['port']);
        }
    }
}
