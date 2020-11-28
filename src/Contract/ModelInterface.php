<?php

namespace Simply\Core\Contract;

interface ModelInterface {
    /**
     * @return RepositoryInterface
     */
    static function getRepository();
    static function getType();
}
