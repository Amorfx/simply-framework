<?php

namespace Simply\Core\Repository;

use Simply\Core\Model\CategoryObject;

class CategoryRepository extends AbstractRepository {
    public function find($id) {
        $cat = get_category($id);
        return $this->getReturnObject($cat);
    }

    public function findAll() {
        $tags = get_categories(['hide_empty' => false]);
        $returnModels = [];
        foreach ($tags as $aCat) {
            $returnModels[] = $this->getReturnObject($aCat);
        }
        return $returnModels;
    }

    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null) {
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

    public function findOneBy(array $criteria) {
        $cat = $this->findBy($criteria, null, 1);
        if ($cat) {
            return $cat[0];
        }
        return false;
    }

    public function getClassName() {
        return CategoryObject::class;
    }
}
