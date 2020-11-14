<?php

namespace SimplyFramework\Repository;

use SimplyFramework\Contract\RepositoryInterface;
use SimplyFramework\Model\ModelFactory;

abstract class AbstractRepository  implements RepositoryInterface {
    /**
     * Return the object managed by the repository
     *
     * @param $objectQuery
     *
     * @return mixed|\SimplyFramework\Contract\ModelInterface
     * @throws \Exception
     */
    protected function getReturnObject($objectQuery) {
        return ModelFactory::create($objectQuery);
    }
}
