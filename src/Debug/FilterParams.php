<?php

namespace Simply\Core\Debug;

final class FilterParams
{
    public function __construct(
        public readonly ?string $hookName = null,
        public readonly ?string $directory = null,
        public readonly ?string $functionName = null
    ) {
    }
}
