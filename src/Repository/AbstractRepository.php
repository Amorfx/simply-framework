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

    /**
     * Translate the repository orderBy argument into WordPress query arguments.
     * Accepted: a field name ('title'), ['orderby' => field, 'order' => 'ASC|DESC']
     * or ['meta_key' => key, 'order' => 'ASC|DESC'].
     *
     * @param array<string, string>|string|null $orderBy
     * @return array<string, string>
     */
    protected function getOrderArgs(array|string|null $orderBy): array
    {
        if (null === $orderBy) {
            return [];
        }

        if (is_string($orderBy)) {
            return ['orderby' => $orderBy];
        }

        $args = [];
        if (isset($orderBy['meta_key'])) {
            $args['meta_key'] = $orderBy['meta_key'];
            $args['orderby'] = 'meta_value';
        } elseif (isset($orderBy['orderby'])) {
            $args['orderby'] = $orderBy['orderby'];
        }

        if (isset($orderBy['order'])) {
            $args['order'] = $orderBy['order'];
        }

        return $args;
    }

    /**
     * @return array<string, int>
     */
    protected function getPaginationArgs(?int $limit, ?int $offset, string $limitKey = 'number'): array
    {
        $args = [];
        if (null !== $limit) {
            $args[$limitKey] = $limit;
        }

        if (null !== $offset) {
            $args['offset'] = $offset;
        }

        return $args;
    }
}
