<?php

namespace Simply\Core\Contract;

interface CacheInterface
{

    public function get(string $key): mixed;

    public function set(string $key, mixed $value, int $expire = null): void;

    public function delete(string $key): void;
}
