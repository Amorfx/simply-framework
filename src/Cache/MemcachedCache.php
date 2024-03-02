<?php

namespace Simply\Core\Cache;

use Memcached;
use Simply\Core\Contract\CacheInterface;

class MemcachedCache implements CacheInterface
{
    /**
     * @var Memcached
     */
    private $client;

    public function __construct($host, $port = 11211)
    {
        $this->client = new Memcached();
        $this->client->addServer($host, $port);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->client->get($key);
    }

    /**
     * @param $key
     * @param $value
     * @param null $expire
     *
     * @return mixed|void
     */
    public function set($key, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = 0;
        }

        $this->client->set($key, $value, $expire);
    }

    /**
     * @param $key
     *
     * @return mixed|void
     */
    public function delete($key)
    {
        $this->client->delete($key);
    }

    public function getClient()
    {
        return $this->client;
    }
}
