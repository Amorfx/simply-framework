<?php

namespace Simply\Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
abstract readonly class Model
{
    public function __construct(
        public string $type,
        public string $repositoryClass,
    ) {
    }
}
