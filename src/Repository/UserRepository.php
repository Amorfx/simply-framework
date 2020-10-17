<?php

namespace SimplyFramework\Repository;

use SimplyFramework\Model\CategoryObject;
use SimplyFramework\Model\ModelFactory;
use SimplyFramework\Model\UserObject;

class UserRepository extends AbstractRepository {
    public function find($id) {
        $user = get_user_by('id', $id);
        if ($user) {
            return $this->getReturnObject($user);
        }
        return false;
    }

    public function findAll() {
        $users = get_users();
        $returnModels = [];
        foreach ($users as $aUser) {
            $returnModels[] = $this->getReturnObject($aUser);
        }
        return $returnModels;
    }

    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null) {
        $args = array_merge($criteria, [
            'orderby' => $orderBy,
            'number' => $limit,
            'offset' => $offset
        ]);
        $users = get_users($args);
        $returnModels = [];
        foreach ($users as $aUser) {
            $returnModels[] = $this->getReturnObject($aUser);
        }
        return $returnModels;
    }

    public function findOneBy(array $criteria) {
        $user = $this->findBy($criteria, null, 1);
        if ($user) {
            return $user[0];
        }
        return false;
    }

    public function getClassName() {
        return UserObject::class;
    }
}
