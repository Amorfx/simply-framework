<?php

namespace SimplyFramework\Query;

use SimplyFramework\Contract\ModelInterface;

class SimplyQuery {
    /**
     * @return ModelInterface
     */
    public static function getCurrentObject() {
        $currentObject = get_queried_object();
        // TODO Get model manager and use supports to get the right object to return
    }
}
