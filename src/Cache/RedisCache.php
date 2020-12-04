<?php

namespace Simply\Core\Cache;

use Redis;
use Simply\Core\Contract\CacheInterface;

class RedisCache implements CacheInterface {
    /**
     * @var Redis
     */
    private $client;

    public function __construct($host, $port) {
        $this->client = new Redis();
        $this->client->connect($host, $port);
        $this->client->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
    }

    /**
     * @param $key
     *
     * @return bool|mixed|string
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
        $this->client->set($key, $value, $expire);
    }

    /**
     * @param $key
     * @param mixed ...$otherKeys
     *
     * @return mixed|void
     */
    public function delete($key) {
        $this->client->del($key);
    }

    public function geoAdd($key, $longitude, $latitude, $member) {
        $this->client->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
        return $this->client->geoadd($key, $longitude, $latitude, $member);
    }
}
