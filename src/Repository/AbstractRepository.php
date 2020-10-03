<?php

namespace SimplyFramework\Repository;

use SimplyFramework\Contract\RepositoryInterface;

abstract class AbstractRepository  implements RepositoryInterface {
    /**
     * Return the object managed by the repository
     * @param $objectQuery
     */
    protected function getReturnObject($objectQuery) {
    }
}
