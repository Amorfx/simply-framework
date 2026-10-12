<?php

namespace Simply\Core\Debug;

use WP_Hook;

final class SearchEngine
{
    /**
     * @param array<string, WP_Hook> $hooks
     * @param FilterParams $filters
     */
    public function __construct(
        private readonly array $hooks,
        private readonly FilterParams $filters
    ) {
    }

    /**
     * @return array<string, WP_Hook>
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
     * @param array<string, WP_Hook> $hooks
     * @return array<string, WP_Hook>
     */
    private function filterByHookName(array $hooks): array
    {
        if (array_key_exists($this->filters->hookName, $hooks)) {
            return [$this->filters->hookName => $hooks[$this->filters->hookName]];
        }

        return $hooks;
    }

    /**
     * @param array<string, WP_Hook> $hooks
     * @return array<string, WP_Hook>
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

    /**
     * @param array<string, WP_Hook> $hooks
     * @return array<string, WP_Hook>
     */
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

    /** @phpstan-ignore-next-line  */
    private function isInFunctionName(array $callback): bool
    {
        return $this->filters->functionName === CallbackReflector::reflect($callback['function'])?->getName();
    }

    /**
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
     * @phpstan-ignore-next-line
     */
    private function isInSource(array $callback): bool
    {
        $source = $this->getSource($callback);
        return str_contains($source, '/' . $this->filters->directory . '/');
    }

    /** @phpstan-ignore-next-line */
    private function getSource(array $callback): string
    {
        return CallbackReflector::reflect($callback['function'])?->getFileName() ?: '';
    }
}
