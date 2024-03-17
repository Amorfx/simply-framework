<?php

namespace Simply\Core\Cache;

use Memcached;
use Simply\Core\Contract\CacheInterface;

class MemcachedCache implements CacheInterface
{
    /**
     * @var Memcached
     */
    private Memcached $client;

    public function __construct(string $host, int $port = 11211)
    {
        $this->client = new Memcached();
        $this->client->addServer($host, $port);
    }


    public function get(string $key): mixed
    {
        return $this->client->get($key);
    }

    public function set(string $key, mixed $value, ?int $expire = null): void
    {
        if (is_null($expire)) {
            $expire = 0;
        }

        $this->client->set($key, $value, $expire);
    }

    public function delete(string $key): void
    {
        $this->client->delete($key);
    }

    public function getClient(): Memcached
    {
        return $this->client;
    }
}
