<?php

namespace SimplyFramework\Repository;

use SimplyFramework\Model\PostTypeObject;

class PostRepository extends AbstractRepository {
    public function find($id) {
        // TODO: Implement find() method.
    }

    public function findAll() {
        // TODO: Implement findAll() method.
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null) {
        // TODO: Implement findBy() method.
    }

    public function findOneBy(array $criteria) {
        // TODO: Implement findOneBy() method.
    }

    public function getClassName() {
        return PostTypeObject::class;
    }
}
