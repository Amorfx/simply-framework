<?php

namespace SimplyFramework\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

class FormGenerator {
    private $typeMapping = [
        'text' => TextType::class,
        'number' => NumberType::class,
        'choice' => ChoiceType::class
    ];

    /**
     * @param array $fields
     * @param string $fieldPrefix
     *
     * @return FormInterface
     */
    public function createForm(array $fields, string $fieldPrefix= '') {
        $formFactory = Forms::createFormFactory();
        $form = $formFactory->createBuilder();
        foreach ($fields as $idField => $argsField) {
            $builderType = $this->getBuilderTypeByType($argsField['type']);
            $options = [];
            if (array_key_exists('options', $argsField)) {
                $options = $argsField['options'];
            }
            $form->add($fieldPrefix . '_' . $idField, $builderType, $options);
        }
        return $form->getForm();
    }

    public function getBuilderTypeByType($type) {
        if (!array_key_exists($type, $this->typeMapping)) {
            throw new \RuntimeException('The form type ' . $type . ' does not exist. Authorized types are : ' . implode(', ', array_keys($this->typeMapping)));
        }
        return $this->typeMapping[$type];
    }
}
