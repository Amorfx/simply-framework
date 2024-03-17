<?php

namespace Simply\Core\Repository;

use Simply\Core\Model\CategoryObject;

class CategoryRepository extends AbstractRepository
{
    public function find(mixed $id)
    {
        $cat = get_category($id);
        return $this->getReturnObject($cat);
    }

    /**
     * @return array{}|object[]
     * @throws \Exception
     */
    public function findAll(): array
    {
        $tags = get_categories(['hide_empty' => false]);
        $returnModels = [];
        foreach ($tags as $aCat) {
            $returnModels[] = $this->getReturnObject($aCat);
        }
        return $returnModels;
    }

    /**
     * @param array $criteria
     * @param array|string|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array{}|object[]
     * @throws \Exception
     * @phpstan-ignore-next-line
     */
    public function findBy(array $criteria, array|string $orderBy = null, int $limit = null, int $offset = null): array
    {
        $args = array_merge($criteria, [
            'orderby' => $orderBy,
            'number' => $limit,
            'offset' => $offset
        ]);
        $cats = get_categories($args);
        $returnModels = [];
        if (!empty($cats)) {
            foreach ($cats as $aCat) {
                $returnModels[] = $this->getReturnObject($aCat);
            }
        }

        return $returnModels;
    }

    public function findOneBy(array $criteria): ?object
    {
        $cat = $this->findBy($criteria, null, 1);
        if ($cat) {
            return $cat[0];
        }
        return null;
    }

    public function getClassName(): string
    {
        return CategoryObject::class;
    }
}
