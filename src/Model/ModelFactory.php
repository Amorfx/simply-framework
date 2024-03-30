<?php

namespace Simply\Core\Model;

use Exception;
use Simply;
use WP_Post;
use WP_Term;
use WP_User;

class ModelFactory
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

    public static function fromObject(object $object): object
    {
        return Simply::get(self::class)->create($object);
    }


    /**
     * @throws Exception
     */
    public function create(?object $currentObject): mixed
    {
        if (is_null($currentObject)) {
            return false;
        }

        $className = get_class($currentObject);
        switch ($className) {
            case WP_Post::class:
                $modelClass = $this->getModelByType($currentObject->post_type);
                break;

            case WP_Term::class:
                $modelClass = $this->getModelByType($currentObject->taxonomy);
                break;

            case WP_User::class:
                $modelClass = UserObject::class;
                break;

            default:
                throw new Exception('The class ' . $className . ' is not supported');
        }
        return new $modelClass($currentObject);
    }

    /**
     * @throws Exception
     */
    private function getRepositoryClassByModel(string $modelClass): string
    {
        if (!array_key_exists($modelClass, $this->modelRepositoryMapping)) {
            throw new Exception('The model ' . $modelClass . ' has not repository class defined');
        }

        return $this->modelRepositoryMapping[$modelClass];
    }

    /**
     * @throws Exception
     */
    private function getTypeByModel(string $modelClass): string
    {
        if (!array_key_exists($modelClass, $this->modelTypeMapping)) {
            throw new Exception('The model ' . $modelClass . ' has not type defined');
        }

        return $this->modelTypeMapping[$modelClass];
    }

    private function getModelByType(string $type): string
    {
        $search = array_search($type, $this->modelTypeMapping);
        if ($search === false) {
            throw new Exception('The type ' . $type . ' is not supported, the supported types are ' . implode(', ', $this->modelTypeMapping));
        }

        return $search;
    }
}
