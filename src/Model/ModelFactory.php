<?php

namespace Simply\Core\Model;

use Simply\Core\Contract\ModelInterface;

class ModelFactory {
    /**
     * @param $currentObject
     *
     * @return ModelInterface|mixed
     * @throws \Exception
     */
    public static function create($currentObject) {
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
     * Get Model register by post type
     * A developer can map post type with a specific Model created by him
     *
     * @param $postType
     *
     * @return mixed|string
     */
    private static function getPostModelByType($postType) {
        $mappingModelByPostType = apply_filters('simply_model_post_type_mapping', []);
        if (empty($mappingModelByPostType) || !array_key_exists($postType, $mappingModelByPostType)) {
            return PostTypeObject::class;
        } else {
            return $mappingModelByPostType[$postType];
        }
    }

    private static function getTermModelByType($taxonomy) {
        $mappingModelByTermType = apply_filters('simply_model_term_mapping', [
            'post_tag' => TagObject::class,
            'category' => CategoryObject::class
        ]);
        if (empty($mappingModelByTermType) || !array_key_exists($taxonomy, $mappingModelByTermType)) {
            throw new \Exception('The taxonomy ' . $taxonomy . ' is not supported');
        } else {
            return $mappingModelByTermType[$taxonomy];
        }
    }
}
