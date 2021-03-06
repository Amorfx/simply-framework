<?php

namespace Simply\Core\Repository;

use Simply\Core\Model\TagObject;

class TagRepository extends AbstractRepository {
    public function find($id) {
        $tag = get_tag($id);
        return $this->getReturnObject($tag);
    }

    public function findAll() {
        $tags = get_tags(['hide_empty' => false]);
        $returnModels = [];
        foreach ($tags as $aTag) {
            $returnModels[] = $this->getReturnObject($aTag);
        }
        return $returnModels;
    }

    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null) {
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

    public function findOneBy(array $criteria) {
        $tag = $this->findBy($criteria, null, 1);
        if ($tag) {
            return $tag[0];
        }
        return false;
    }

    public function getClassName() {
        return TagObject::class;
    }
}
