<?php

namespace SimplyFramework\Contract;

use SimplyFramework\Form\FormGenerator;

/**
 * Use this trait when a class can have form to render
 * Trait RenderFormTrait
 *
 * @package SimplyFramework\Contract
 */
trait RenderFormTrait {
    use RenderedTrait;
    use ReferencerTrait;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var FormGenerator
     */
    private $formGenerator;

    /**
     * Set referencer in field
     */
    public function initReferenceFields() {
        if (array_key_exists('reference', $this->fields)) {
            if (!is_array($this->fields['reference'])) {
                throw new \RuntimeException('The reference parameter should be array type.');
            }
            // TODO not put fields references in end of array but in place of reference key ?
            $fieldsKeyReferences = $this->fields['reference'];
            unset($this->fields['reference']);
            foreach ($fieldsKeyReferences as $aField) {
                $referenceField = $this->getFieldReference($aField);
                if ($referenceField) {
                    $this->fields[$aField] = $referenceField;
                }
            }
        }
    }
}
