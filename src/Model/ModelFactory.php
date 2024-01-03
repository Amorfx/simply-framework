<?php

namespace Simply\Core\Model;

use Simply\Core\Contract\ModelInterface;

class ModelFactory {
    private static $postTypeModelClasses = [PostTypeObject::class];
    private static $termTypeModelClasses = [TagObject::class, CategoryObject::class];

    private static $postTypeMapping = null;
    private static $taxTypeMapping = null;

    /**
     * @param $currentObject
     *
     * @return ModelInterface|mixed
     * @throws \Exception
     */
    public static function create($currentObject) {
        if (is_null($currentObject)) {
            return false;
        }

        $className = get_class($currentObject);
        switch ($className) {
            case \WP_Post::class:
                $modelClass = self::getPostModelByType($currentObject->post_type);
                break;

            case \WP_Term::class:
                $modelClass = self::getTermModelByType($currentObject->taxonomy);
                break;

            case \WP_User::class:
                $modelClass = UserObject::class;
                break;

            default:
                throw new \Exception('The class ' . $className . ' is not supported');
        }
        return new $modelClass($currentObject);
    }

    /**
     * Use in Simply Plugin to register your post type models
     *
     * @param array $postModels
     */
    public function registerPostModel(array $postModels): void {
        self::$postTypeModelClasses = $postModels;
    }

    public function addPostModel(array $postModels): void {
        self::$postTypeModelClasses = array_merge(self::$postTypeModelClasses, $postModels);
    }

    /**
     * Use in Simply Plugin to register your term type models
     *
     * @param array $termModels
     */
    public function registerTermModel(array $termModels): void {
        self::$termTypeModelClasses = $termModels;
    }

    public function addTermModel(array $termModels): void {
        self::$termTypeModelClasses = array_merge(self::$termTypeModelClasses, $termModels);
    }

    /**
     * Set key to have a clean mapping
     *
     * @param string[] $models
     *
     * @return array
     */
    private static function setMappingArray(array $models): array {
        foreach ($models as $key => $model) {
            $models[call_user_func([$model, 'getType'])] = $model;
            unset($models[$key]);
        }
        return $models;
    }

    /**
     * Get Model register by post type
     * A developer can map post type with a specific Model created by him
     *
     * @param $postType
     *
     * @return mixed|string
     */
    private static function getPostModelByType($postType) {
        if (is_null(self::$postTypeMapping)) {
            self::$postTypeMapping = self::setMappingArray(apply_filters('simply/model/post_type_mapping', self::$postTypeModelClasses));
        }

        if (empty(self::$postTypeMapping) || !array_key_exists($postType, self::$postTypeMapping)) {
            return PostTypeObject::class;
        } else {
            return self::$postTypeMapping[$postType];
        }
    }

    private static function getTermModelByType($taxonomy) {
        if (is_null(self::$taxTypeMapping)) {
            self::$taxTypeMapping = self::setMappingArray(apply_filters('simply/model/term_mapping', self::$termTypeModelClasses));
        }

        if (empty(self::$taxTypeMapping) || !array_key_exists($taxonomy, self::$taxTypeMapping)) {
            throw new \Exception('The taxonomy ' . $taxonomy . ' is not supported');
        } else {
            return self::$taxTypeMapping[$taxonomy];
        }
    }
}
