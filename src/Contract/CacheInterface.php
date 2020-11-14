<?php

namespace SimplyFramework\Contract;

interface CacheInterface {
    /**
     * Get cache value from key
     * @param $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Set value to cache
     *
     * @param $key
     * @param $value
     * @param $expire
     *
     * @return mixed
     */
    public function set($key, $value, $expire = null);

    /**
     * Delete a key in cache
     * @param $key
     *
     * @return mixed
     */
    public function delete($key);
}
