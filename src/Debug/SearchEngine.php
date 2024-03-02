<?php

namespace Simply\Core\Debug;

use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use WP_Hook;

final class SearchEngine
{
    /**
     * @param array<string, array<WP_Hook> $hooks
     * @param FilterParams $filters
     */
    public function __construct(
        private readonly array $hooks,
        private readonly FilterParams $filters)
    {
    }

    /**
     * @throws ReflectionException
     */
    public function search(): array
    {
        $filteredArray = $this->hooks;

        if ($this->filters->hookName) {
            $filteredArray = $this->filterByHookName($filteredArray);
        }

        if ($this->filters->directory) {
            $filteredArray = $this->filterByDirectory($filteredArray);
        }

        if ($this->filters->functionName) {
            $filteredArray = $this->filterByFunctionName($filteredArray);
        }

        return $filteredArray;
    }

    /**
     * @param array<WP_Hook> $hooks
     * @return array
     */
    private function filterByHookName(array $hooks): array
    {
        if (array_key_exists($this->filters->hookName, $hooks)) {
            return [$this->filters->hookName => $hooks[$this->filters->hookName]];
        }

        return $hooks;
    }

    /**
     * @param array<WP_Hook> $hooks
     * @return array
     * @throws ReflectionException
     */
    private function filterByDirectory(array $hooks): array
    {
        $filteredArray = [];
        foreach ($hooks as $hookName => $hook) {
            if ($this->isInDirectory($hook)) {
                $filteredArray[$hookName] = $hook;
            }
        }

        return $filteredArray;
    }

    private function filterByFunctionName(array $hooks): array
    {
        $filteredArray = [];
        // Get only the callbacks with function name
        foreach ($hooks as $hookName => $hook) {
            $hasToAddHook = false;
            foreach ($hook->callbacks as $keyParent => $callbacks) {
                foreach ($callbacks as $key => $callback) {
                    if ($this->isInFunctionName($callback)) {
                        $hasToAddHook = true;
                    } else {
                        unset($callbacks[$key]);
                    }
                }
                $hook->callbacks[$keyParent] = $callbacks;
            }
            if ($hasToAddHook) {
                $filteredArray[$hookName] = $hook;
            }
        }

        return $filteredArray;
    }

    private function isInFunctionName(array $callback): bool
    {
        if (is_array($callback['function'])) {
            return $this->filters->functionName === $callback['function'][1];
        }

        return $this->filters->functionName === $callback['function'];
    }

    /**
     * @throws ReflectionException
     */
    private function isInDirectory(WP_Hook $hook): bool
    {
        foreach ($hook->callbacks as $callbacks) {
            foreach ($callbacks as $callback) {
                if ($this->isInSource($callback)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @throws ReflectionException
     */
    private function isInSource(array $callback): bool
    {
        $source = $this->getSource($callback);
        return str_contains($source, '/' . $this->filters->directory . '/');
    }

    /**
     * @throws ReflectionException
     */
    private function getSource(array $callback): string
    {
        if (is_array($callback['function'])) {
            $reflector = new ReflectionMethod($callback['function'][0], $callback['function'][1]);
            return $reflector->getFileName();
        } elseif (is_string($callback['function'])) {
            $reflector = new ReflectionFunction($callback['function']);
            return $reflector->getFileName();
        }

        return '';
    }
}
