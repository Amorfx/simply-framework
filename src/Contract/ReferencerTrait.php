<?php

namespace SimplyFramework\Contract;

/**
 * Get parameter referencor of object in container
 * Trait ReferencorTrait
 *
 * @package SimplyFramework\Contract
 */
trait ReferencerTrait {
    public function getFieldReference($field) {
        $allFields = \Simply::getContainer()->getParameter('fields');
        if (array_key_exists($field, $allFields)) {
            return $allFields[$field];
        }

        return false;
    }
}
