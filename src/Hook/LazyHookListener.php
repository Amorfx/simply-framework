<?php

namespace Simply\Core\Hook;

use Psr\Container\ContainerInterface;

/**
 * WordPress callback of an #[Action] or #[Filter] method: the service is only instantiated when WordPress runs the hook
 */
final class LazyHookListener
{
    /**
     * @param class-string $class
     */
    public function __construct(
        private readonly ContainerInterface $services,
        public readonly string $serviceId,
        public readonly string $class,
        public readonly string $method,
    ) {
    }

    public function __invoke(mixed ...$arguments): mixed
    {
        return $this->services->get($this->serviceId)->{$this->method}(...$arguments);
    }
}
