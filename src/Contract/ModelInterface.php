<?php

namespace SimplyFramework\Contract;

interface ModelInterface {
    /**
     * @return RepositoryInterface
     */
    static function getRepository();
    static function getType();
}
