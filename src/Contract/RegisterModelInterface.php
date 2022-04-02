<?php

namespace Simply\Core\Contract;

use Simply\Core\Model\ModelFactory;

/**
 * Use this interface to add possibility to your class to register model
 */
interface RegisterModelInterface {
    public function registerModel(ModelFactory $factory): void;
}
