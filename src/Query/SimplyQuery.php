<?php

namespace SimplyFramework\Query;

use SimplyFramework\Contract\ModelInterface;
use SimplyFramework\Model\ModelFactory;

class SimplyQuery {
    /**
     * @var ModelInterface
     */
    private static $currentObject = null;

    /**
     * Get the model associated with the WordPress queried object
     * @return ModelInterface|false
     * @throws \Exception
     */
    public static function getCurrentObject() {
        if (!is_null(self::$currentObject)) {
            return self::$currentObject;
        }

        $currentObject = get_queried_object();
        try {
            $model = ModelFactory::create($currentObject);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
        self::$currentObject = $model;
        return $model;
    }
}
