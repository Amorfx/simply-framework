<?php

namespace Simply\Core\Model;

use Simply\Core\Contract\ModelInterface;
use WP_User;

class UserObject implements ModelInterface
{
    public WP_User $user;

    public function __construct(WP_User $user)
    {
        $this->user = $user;
    }

    public static function getType(): string
    {
        return 'user';
    }
}
