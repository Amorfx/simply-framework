<?php

namespace Simply\Core\Repository;

use Exception;
use Simply\Core\Contract\ModelInterface;
use Simply\Core\Contract\RepositoryInterface;
use Simply\Core\Model\ModelFactory;
use Simply\Core\Model\ModelManager;

abstract class AbstractRepository implements RepositoryInterface
{
    public function __construct(
        protected readonly string $type,
        protected readonly string $modelClass,
    )
    {
    }

    /**
     * Return the object managed by the repository
     *
     *
     * @return mixed|ModelInterface
     * @throws Exception
     */
    protected function getReturnObject(object $objectQuery): mixed
    {
        return new ($this->modelClass)($objectQuery);
    }
}
