<?php

namespace Simply\Core\Attributes;

abstract readonly class Model
{
    public function __construct(
        public string $type,
        public string $repositoryClass,
    ) {
    }
}
