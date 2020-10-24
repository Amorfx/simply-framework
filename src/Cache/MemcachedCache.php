<?php

namespace SimplyFramework\Cache;

use Memcached;
use SimplyFramework\Contract\CacheInterface;

class MemcachedCache implements CacheInterface {
    /**
     * @var Memcached
     */
    private $client;

    public function __construct($host, $port = 11211) {
        $this->client = new Memcached();
        $this->client->addServer($host, $port);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key) {
        return $this->client->get($key);
    }

    /**
     * @param $key
     * @param $value
     * @param null $expire
     *
     * @return mixed|void
     */
    public function set($key, $value, $expire = null) {
        if (is_null($expire)) {
            $expire = 0;
        }

        $this->client->set($key, $value, $expire);
    }
}
