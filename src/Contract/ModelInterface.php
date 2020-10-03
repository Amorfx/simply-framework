<?php

namespace SimplyFramework\Contract;

interface ModelInterface {
    /**
     * @return RepositoryInterface
     */
    function getRepository();
    function supports($type);
}
