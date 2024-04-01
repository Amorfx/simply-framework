<?php

namespace Simply\Core\Model;

use Exception;
use Simply;
use WP_Post;
use WP_Term;
use WP_User;

class ModelFactory
{
    public function __construct(
        private ModelManager $modelManager
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
                $modelClass = $this->modelManager->getModelByType($currentObject->post_type);
                break;

            case WP_Term::class:
                $modelClass = $this->modelManager->getModelByType($currentObject->taxonomy);
                break;

            case WP_User::class:
                $modelClass = UserObject::class;
                break;

            default:
                throw new Exception('The class ' . $className . ' is not supported');
        }
        return new $modelClass($currentObject);
    }
}
