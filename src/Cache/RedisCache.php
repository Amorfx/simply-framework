<?php

namespace Simply\Core\Cache;

use Redis;
use RedisException;
use Simply\Core\Contract\CacheInterface;

class RedisCache implements CacheInterface
{
    /**
     * @var Redis
     */
    private Redis $client;

    /**
     * @throws RedisException
     */
    public function __construct(string $host, int $port)
    {
        $this->client = new Redis();
        $this->client->connect($host, $port);
        $this->client->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
    }

    public function get(string $key): mixed
    {
        return $this->client->get($key);
    }

    public function set(string $key, mixed $value, ?int $expire = 0): void
    {
        $this->client->set($key, $value, $expire);
    }

    public function delete(string $key): void
    {
        $this->client->del($key);
    }

    public function geoAdd(string $key, float $longitude, float $latitude, string $member): bool|int|Redis
    {
        $this->client->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
        return $this->client->geoadd($key, $longitude, $latitude, $member);
    }
}
