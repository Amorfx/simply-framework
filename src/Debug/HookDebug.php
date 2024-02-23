<?php

namespace Simply\Core\Debug;

final class HookDebug
{
    public function __construct(
        public readonly string $name,
        public readonly string $source,
        public readonly int $sourceLine,
        public readonly string $functionName,
        public readonly int $priority,
    )
    {}
}
