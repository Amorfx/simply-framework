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
     * Same as getReturnObject but returns null when WordPress found nothing (null, false or WP_Error)
     */
    protected function getReturnObjectOrNull(mixed $objectQuery): mixed
    {
        if (!is_object($objectQuery) || $objectQuery instanceof \WP_Error) {
            return null;
        }

        return $this->getReturnObject($objectQuery);
    }

    /**
     * Translate the repository orderBy argument into WordPress query arguments.
     * Accepted: a field name ('title'), ['orderby' => field, 'order' => 'ASC|DESC'],
     * ['meta_key' => key, 'orderby' => 'meta_value|meta_value_num', 'order' => 'ASC|DESC']
     * or a single field => direction pair (['title' => 'DESC']).
     *
     * @param array<string, string>|string|null $orderBy
     * @return array<string, string>
     * @throws \UnexpectedValueException When ordering by several fields
     */
    protected function getOrderArgs(array|string|null $orderBy): array
    {
        if (null === $orderBy) {
            return [];
        }

        if (is_string($orderBy)) {
            return ['orderby' => $orderBy];
        }

        if (!isset($orderBy['meta_key']) && !isset($orderBy['orderby']) && !isset($orderBy['order'])) {
            if (count($orderBy) !== 1) {
                throw new \UnexpectedValueException('Only one field => direction pair is supported in orderBy, pass "orderby" in the criteria to order by several fields.');
            }

            return ['orderby' => (string) array_key_first($orderBy), 'order' => (string) reset($orderBy)];
        }

        $args = [];
        if (isset($orderBy['meta_key'])) {
            $args['meta_key'] = $orderBy['meta_key'];
            $args['orderby'] = $orderBy['orderby'] ?? 'meta_value';
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
