<?php

namespace Simply\Core\Model;

use Simply\Core\Attributes\UserModel;
use Simply\Core\Contract\ModelInterface;
use Simply\Core\Repository\UserRepository;
use WP_User;

#[UserModel(type: 'user', repositoryClass: UserRepository::class)]
class UserObject
{
    public WP_User $user;

    public function __construct(WP_User $user)
    {
        $this->user = $user;
    }
}
