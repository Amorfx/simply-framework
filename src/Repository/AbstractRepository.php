<?php

namespace Simply\Core\Repository;

use Exception;
use Simply\Core\Contract\ModelInterface;
use Simply\Core\Contract\RepositoryInterface;
use Simply\Core\Model\ModelFactory;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * Return the object managed by the repository
     *
     *
     * @return mixed|ModelInterface
     * @throws Exception
     */
    protected function getReturnObject(object $objectQuery): mixed
    {
        $class = $this->getClassName();
        return new $class($objectQuery);
    }
}
