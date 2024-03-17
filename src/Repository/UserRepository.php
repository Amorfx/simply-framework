<?php

namespace Simply\Core\Repository;

use Simply\Core\Model\UserObject;

class UserRepository extends AbstractRepository
{
    public function find(mixed $id)
    {
        $user = get_user_by('id', $id);
        if ($user) {
            return $this->getReturnObject($user);
        }
        return null;
    }

    public function findAll(): array
    {
        $users = get_users();
        $returnModels = [];
        foreach ($users as $aUser) {
            $returnModels[] = $this->getReturnObject($aUser);
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
        $users = get_users($args);
        $returnModels = [];
        foreach ($users as $aUser) {
            $returnModels[] = $this->getReturnObject($aUser);
        }
        return $returnModels;
    }

    public function findOneBy(array $criteria): ?object
    {
        $user = $this->findBy($criteria, null, 1);
        if ($user) {
            return $user[0];
        }
        return null;
    }

    public function getClassName(): string
    {
        return UserObject::class;
    }
}
