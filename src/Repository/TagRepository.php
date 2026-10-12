<?php

namespace Simply\Core\Repository;

class TagRepository extends AbstractRepository
{
    public function find(mixed $id)
    {
        return $this->getReturnObjectOrNull(get_tag($id));
    }

    public function findAll(): array
    {
        $tags = get_tags(['hide_empty' => false]);
        $returnModels = [];
        foreach ($tags as $aTag) {
            $returnModels[] = $this->getReturnObject($aTag);
        }
        return $returnModels;
    }

    public function findBy(array $criteria, array|string $orderBy = null, int $limit = null, int $offset = null): array
    {
        $args = array_merge($criteria, $this->getOrderArgs($orderBy), $this->getPaginationArgs($limit, $offset));
        $tags = get_tags($args);
        $returnModels = [];
        foreach ($tags as $aTag) {
            $returnModels[] = $this->getReturnObject($aTag);
        }
        return $returnModels;
    }

    public function findOneBy(array $criteria): ?object
    {
        $tag = $this->findBy($criteria, null, 1);
        if ($tag) {
            return $tag[0];
        }
        return null;
    }
}
