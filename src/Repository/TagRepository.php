<?php

namespace Simply\Core\Repository;

use Simply\Core\Model\TagObject;

class TagRepository extends AbstractRepository
{
    public function find(mixed $id)
    {
        $tag = get_tag($id);
        return $this->getReturnObject($tag);
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
        $args = array_merge($criteria, [
            'orderby' => $orderBy,
            'number' => $limit,
            'offset' => $offset
        ]);
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

    public function getClassName(): string
    {
        return TagObject::class;
    }
}
