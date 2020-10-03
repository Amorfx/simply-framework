<?php

namespace SimplyFramework\Form;

use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

class FormGenerator {
    private $typeMapping = [
        'text' => TextType::class,
        'textarea' => TextareaType::class,
        'integer' => IntegerType::class,
        'number' => NumberType::class,
        'choice' => ChoiceType::class,
        'submit' => SubmitType::class
    ];

    /**
     * Create the form class
     *
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
            $options = $this->configureOption($options);
            $idFieldWithPrefix = !empty($fieldPrefix) ? $fieldPrefix . '_' . $idField : $idField;
            $form->add($idFieldWithPrefix, $builderType, $options);
        }
        return $form->getForm();
    }

    /**
     * Configure specific option like callable choices
     *
     * @param $options
     *
     * @return mixed
     */
    private function configureOption($options) {
        if (array_key_exists('choices', $options)) {
            $options['choices'] = $this->configureChoices($options['choices']);
        }

        return $options;
    }

    /**
     * Choices can be callable string
     * So get the choices by
     *
     * @param $choices
     *
     * @return array|mixed
     * @throws ReflectionException
     */
    private function configureChoices($choices) {
        if (!is_array($choices)) {
            if (function_exists($choices)) {
                $choices = call_user_func($choices);
            } else if (strpos($choices, '@')) {
                // verify if the callable has type of Class@Action
                $arrayCallable = explode('@', $choices);
                $reflectionClass = new ReflectionClass($arrayCallable[0]);
                $classInstance = $reflectionClass->newInstance();
                return $classInstance->{$arrayCallable[1]}();
            } else {
                throw new RuntimeException('Choices in choice type field should be callable function or Class@Action string.');
            }
        }
        return $choices;
    }

    private function getBuilderTypeByType($type) {
        if (!array_key_exists($type, $this->typeMapping)) {
            throw new RuntimeException('The form type ' . $type . ' does not exist. Authorized types are : ' . implode(', ', array_keys($this->typeMapping)));
        }
        return $this->typeMapping[$type];
    }
}
