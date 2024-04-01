<?php

namespace Simply\Core\DependencyInjection\Compiler;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Simply\Core\Attributes\Model;
use Simply\Core\Model\CategoryObject;
use Simply\Core\Model\PostTypeObject;
use Simply\Core\Model\TagObject;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This compiler pass permit to preconfigure the mapping for ModelFactory
 * - create 3 parameters :
 * -- model.mapping.model_repository
 * -- model.mapping.type_model
 * -- model.list.post_model
 * -- model.list.term_model
 */
final class ModelPass implements CompilerPassInterface
{
    /**
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container)
    {
        $modelRepository = [];
        $listTypeModelIndexedByClass = [];

        foreach ($container->getDefinitions() as $definition) {
            $class = $definition->getClass();
            if ($class === null) {
                continue;
            }

            try {
                $ref = new ReflectionClass($class);
            } catch (ReflectionException) { // @phpstan-ignore-line
                continue;
            }

            $attributes = $ref->getAttributes(Model::class, ReflectionAttribute::IS_INSTANCEOF);

            if (empty($attributes)) {
                continue;
            }

            $modelAttribute = $attributes[0]->newInstance();
            $listTypeModelIndexedByClass[$class] = $modelAttribute->type;
            $modelRepository[$class] = $modelAttribute->repositoryClass;
        }

        // Remove default post model by the framework if there is more than one with plugins and theme
        $allPostModelTypePost = array_filter($listTypeModelIndexedByClass, fn($type) => $type === 'post');
        if (count($allPostModelTypePost) > 1) {
            unset($listTypeModelIndexedByClass[PostTypeObject::class]);
            unset($modelRepository[PostTypeObject::class]);
        }

        $allCategoryModelTypeCategory = array_filter($listTypeModelIndexedByClass, fn($type) => $type === 'category');
        if (count($allCategoryModelTypeCategory) > 1) {
            unset($listTypeModelIndexedByClass[CategoryObject::class]);
            unset($modelRepository[CategoryObject::class]);
        }

        $allTagModelTypeTag = array_filter($listTypeModelIndexedByClass, fn($type) => $type === 'post_tag');
        if (count($allTagModelTypeTag) > 1) {
            unset($listTypeModelIndexedByClass[TagObject::class]);
            unset($modelRepository[TagObject::class]);
        }


        $container->setParameter('simply.model.mapping.model_repository', $modelRepository);
        $container->setParameter('simply.model.mapping.type_model', $listTypeModelIndexedByClass);

        foreach ($listTypeModelIndexedByClass as $class => $type) {
            $this->setupDefinitionForRepository($container, $type, $class, $modelRepository[$class]);
        }
    }

    private function setupDefinitionForRepository(
        ContainerBuilder $container,
        string $type,
        string $modelClass,
        string $repositoryClass
    ): void
    {
        $definition = $container->getDefinition($repositoryClass);
        $definition->setArgument('$type', $type);
        $definition->setArgument('$modelClass', $modelClass);
    }

}
