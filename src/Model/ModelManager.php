<?php

declare(strict_types=1);

namespace Simply\Core\Model;

use Exception;
use Simply;
use WP_Post;
use WP_Term;
use WP_User;

final class ModelManager
{
    /**
     * @param array<string> $postModelList
     * @param array<string> $termModelList
     * @param array<string, string> $modelRepositoryMapping
     * @param array<string, string> $modelTypeMapping
     */
    public function __construct(
        private array $postModelList = [],
        private array $termModelList = [],
        private array $modelRepositoryMapping = [],
        private array $modelTypeMapping = []
    )
    {
    }

    /**
     * @throws Exception
     */
    public function getRepositoryClassByModel(string $modelClass): string
    {
        if (!array_key_exists($modelClass, $this->modelRepositoryMapping)) {
            throw new Exception('The model ' . $modelClass . ' has not repository class defined');
        }

        return $this->modelRepositoryMapping[$modelClass];
    }

    /**
     * @throws Exception
     */
    public function getTypeByModel(string $modelClass): string
    {
        if (!array_key_exists($modelClass, $this->modelTypeMapping)) {
            throw new Exception('The model ' . $modelClass . ' has not type defined');
        }

        return $this->modelTypeMapping[$modelClass];
    }

    public function getModelByType(string $type): string
    {
        $search = array_search($type, $this->modelTypeMapping);
        if ($search === false) {
            throw new Exception('The type ' . $type . ' is not supported, the supported types are ' . implode(', ', $this->modelTypeMapping));
        }

        return $search;
    }
}
