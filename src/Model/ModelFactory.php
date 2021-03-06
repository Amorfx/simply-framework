<?php

namespace Simply\Core\Model;

use Simply\Core\Contract\ModelInterface;

class ModelFactory {
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
                $modelClass = self::getPostModelByType(get_post_type($currentObject));
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
            self::$postTypeMapping = self::setMappingArray(apply_filters('simply_model_post_type_mapping', [PostTypeObject::class]));
        }

        if (empty(self::$postTypeMapping) || !array_key_exists($postType, self::$postTypeMapping)) {
            return PostTypeObject::class;
        } else {
            return self::$postTypeMapping[$postType];
        }
    }

    private static function getTermModelByType($taxonomy) {
        if (is_null(self::$taxTypeMapping)) {
            self::$taxTypeMapping = self::setMappingArray(apply_filters('simply_model_term_mapping', [
                TagObject::class,
                CategoryObject::class,
            ]));
        }

        if (empty(self::$taxTypeMapping) || !array_key_exists($taxonomy, self::$taxTypeMapping)) {
            throw new \Exception('The taxonomy ' . $taxonomy . ' is not supported');
        } else {
            return self::$taxTypeMapping[$taxonomy];
        }
    }
}
