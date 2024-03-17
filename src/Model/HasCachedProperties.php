<?php

declare(strict_types=1);

namespace Simply\Core\Model;

trait HasCachedProperties
{
    /**
     * @var array<string, mixed> $cachedProperties
     */
    private array $cachedProperties = [];

    public function __get(string $property): mixed
    {
        if (array_key_exists($property, $this->cachedProperties)) {
            return $this->cachedProperties[$property];
        }

        $methodToCall = $this->getMethodToCall($property);
        if (method_exists($this, $methodToCall)) {
            $this->cachedProperties[$property] = $this->$methodToCall();
            return $this->cachedProperties[$property];
        }

        return null;
    }

    public function __isset(string $property): bool
    {
        return array_key_exists($property, $this->cachedProperties)
            || method_exists($this, $this->getMethodToCall($property));
    }

    public function __unset(string $property): void
    {
        unset($this->cachedProperties[$property]);
    }

    private function getMethodToCall(string $property): string
    {
        return 'get' . ucfirst($property);
    }
}
