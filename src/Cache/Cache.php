<?php

namespace Simply\Core\Cache;

use Simply\Core\Contract\CacheInterface;

class Cache implements CacheInterface {
    /**
     * @var CacheInterface
     */
    private $cacheObjectService;

    public function __construct(array $configuration) {
        $this->cacheObjectService = apply_filters('simply_cache-object_class', $this->cacheObjectService);
        if (!is_null($this->cacheObjectService)) {
            return;
        }

        switch ($configuration['type']) {
            case 'redis':
                $this->cacheObjectService = new RedisCache($configuration['host'], $configuration['port']);
                break;

            case 'memcached':
                $this->cacheObjectService = new MemcachedCache($configuration['host'], $configuration['port']);
                break;
        }
    }

    /**
     * @param $key
     *
     * @return bool|mixed|string
     */
    public function get($key) {
        return $this->cacheObjectService->get($key);
    }

    /**
     * @param $key
     * @param $value
     * @param null $expire
     *
     * @return mixed|void
     */
    public function set($key, $value, $expire = null) {
        $this->cacheObjectService->set($key, $value, $expire);
    }

    /**
     * @param $key
     *
     * @return mixed|void
     */
    public function delete($key) {
        $this->cacheObjectService->delete($key);
    }
}
