<?php

namespace Simply\Core\Debug;

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

    public function search(): array
    {
        $filteredArray = [];
        if ($this->filters->hookName) {
            $filteredArray = $this->filterByHookName($this->hooks);
        }
        return $filteredArray;
    }

    private function filterByHookName(array $hooks): array
    {
        if (array_key_exists($this->filters->hookName, $hooks)) {
            return [$this->filters->hookName => $hooks[$this->filters->hookName]];
        }

        return $hooks;
    }
}
