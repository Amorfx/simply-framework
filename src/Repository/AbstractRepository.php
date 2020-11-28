<?php

namespace Simply\Core\Repository;

use Simply\Core\Contract\RepositoryInterface;
use Simply\Core\Model\ModelFactory;

abstract class AbstractRepository  implements RepositoryInterface {
    /**
     * Return the object managed by the repository
     *
     * @param $objectQuery
     *
     * @return mixed|Simply\Core\Contract\ModelInterface
     * @throws \Exception
     */
    protected function getReturnObject($objectQuery) {
        return ModelFactory::create($objectQuery);
    }
}
