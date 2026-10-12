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
        return $this->getReturnObjectOrNull(get_term($id, $this->getTaxonomy()));
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
        $args = array_merge(
            $criteria,
            $this->getOrderArgs($orderBy),
            $this->getPaginationArgs($limit, $offset),
            ['taxonomy' => $this->getTaxonomy()]
        );
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

    /**
     * Return the taxonomy name that term is part of
     */
    protected function getTaxonomy(): string
    {
        return $this->type;
    }
}
