<?php

namespace SimplyFramework\Model;

use SimplyFramework\Contract\ModelInterface;
use SimplyFramework\Repository\UserRepository;

class UserObject implements ModelInterface {
    public $user;

    public function __construct($user) {
        $this->user = $user;
    }

    static function getRepository() {
        return \Simply::getContainer()->get(UserRepository::class);
    }

    static function getType() {
        return 'user';
    }
}
