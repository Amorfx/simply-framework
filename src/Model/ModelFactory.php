<?php

namespace SimplyFramework\Model;

use SimplyFramework\Contract\ModelInterface;

class ModelFactory {
    /**
     * @param $currentObject
     * TODO finish with WP_Term etc.
     *
     * @return ModelInterface|mixed
     * @throws \Exception
     */
    public static function create($currentObject) {
        $className = get_class($currentObject);
        switch ($className) {
            case \WP_Post::class:
                $modelClass = self::getPostModelByType(get_post_type($currentObject));
                $model = new $modelClass($currentObject);
                break;

            case \WP_Term::class:
                break;

            default:
                throw new \Exception('The class ' . $className . ' is not supported');
        }

        return $model;
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

    private static function getTermModelByType($termName) {

    }
}
