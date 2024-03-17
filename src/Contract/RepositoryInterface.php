<?php

namespace Simply\Core\Contract;

interface RepositoryInterface
{
    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     *
     * @return object|null The object.
     */
    public function find(mixed $id);

    /**
     * Finds all objects in the repository.
     *
     * @return object[] The objects.
     */
    public function findAll();

    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param mixed[]       $criteria
     * @param string|string[]|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return object[] The objects.
     *
     */
    public function findBy(array $criteria, array|string $orderBy = null, int $limit = null, int $offset = null);

    /**
     * Finds a single object by a set of criteria.
     *
     * @param mixed[] $criteria The criteria.
     *
     * @return object|null The object.
     */
    public function findOneBy(array $criteria): ?object;

    /**
     * Returns the class name of the object managed by the repository.
     *
     * @return string
     */
    public function getClassName(): string;
}
