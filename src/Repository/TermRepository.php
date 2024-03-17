<?php

namespace Simply\Core\Repository;

use Simply\Core\Model\TermObject;

/**
 * Extend this class to have a Repository for your custom taxonomy easily
 *
 * @package Simply\Core\Repository
 */
abstract class TermRepository extends AbstractRepository
{
    public function find(mixed $id)
    {
        return $this->getReturnObject(get_term($id, $this->getTaxonomy()));
    }

    public function findAll(): array
    {
        $terms = get_terms(['taxonomy' => $this->getTaxonomy(), 'hide_empty' => false]);
        $returnModels = [];
        foreach ($terms as $aTerm) {
            $returnModels[] = $this->getReturnObject($aTerm);
        }
        return $returnModels;
    }

    public function findBy(array $criteria, array|string $orderBy = null, int $limit = null, int $offset = null): array
    {
        $mergeCriteria = ['taxonomy' => $this->getTaxonomy()];
        if (!is_null($orderBy)) {
            $mergeCriteria['orderBy'] = $orderBy;
        }

        if (!is_null($limit)) {
            $mergeCriteria['number'] = $limit;
        }

        if (!is_null($offset)) {
            $mergeCriteria['offset'] = $offset;
        }

        $args = array_merge($criteria, $mergeCriteria);
        $terms = get_terms($args);
        $returnModels = [];
        foreach ($terms as $aTerm) {
            $returnModels[] = $this->getReturnObject($aTerm);
        }
        return $returnModels;
    }

    public function findOneBy(array $criteria): ?object
    {
        $term = $this->findBy($criteria, null, 1);
        if ($term) {
            return $term[0];
        }
        return null;
    }

    public function getClassName(): string
    {
        return TermObject::class;
    }

    /**
     * Return the taxonomy name that term is part of
     * @return mixed
     */
    protected function getTaxonomy(): mixed
    {
        return call_user_func(array($this::getClassName(), 'getType'));
    }
}
