<?php

namespace Simply\Core\Debug;

final class FilterParams
{
    public function __construct(
        public readonly ?string $hookName,
        public readonly ?string $directory,
        public readonly ?string $functionName
    )
    {}
}
