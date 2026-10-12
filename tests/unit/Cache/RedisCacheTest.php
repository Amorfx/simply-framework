<?php

namespace Simply\Tests\Cache;

use Simply\Core\Cache\RedisCache;
use Simply\Tests\SimplyTestCase;

class RedisCacheTest extends SimplyTestCase
{
    public function testSetWithoutExpirationKeepsTheValue()
    {
        // phpredis rejects an expiration of 0 and stores nothing: no expiration means no option
        $client = \Mockery::mock('Redis');
        $client->shouldReceive('set')->once()->with('key', 'value', null)->andReturn(true);

        (new RedisCache('localhost', 6379, $client))->set('key', 'value');
    }

    public function testSetWithExpirationPassesItAsAnOption()
    {
        $client = \Mockery::mock('Redis');
        $client->shouldReceive('set')->once()->with('key', 'value', ['EX' => 60])->andReturn(true);

        (new RedisCache('localhost', 6379, $client))->set('key', 'value', 60);
    }
}
