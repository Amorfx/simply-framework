<?php

namespace Simply\Core\DependencyInjection\Compiler;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Simply\Core\Attributes\Model;
use Simply\Core\Attributes\PostTypeModel;
use Simply\Core\Attributes\TermModel;
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
        $postModelList = [];
        $termModelList = [];
        $modelRepository = [];
        $listTypeModelIndexedByClass = [];

        foreach ($container->getDefinitions() as $definition) {
            $class = $definition->getClass();
            if ($class === null) {
                continue;
            }

            try {
                $ref = new ReflectionClass($class);
            } catch (ReflectionException) {
                continue;
            }

            $attributes = $ref->getAttributes(Model::class, ReflectionAttribute::IS_INSTANCEOF);

            if (empty($attributes)) {
                continue;
            }

            $modelAttribute = $attributes[0]->newInstance();
            if ($modelAttribute instanceof PostTypeModel) {
                $postModelList[] = $class;
            } elseif ($modelAttribute instanceof TermModel) {
                $termModelList[] = $class;
            }

            $listTypeModelIndexedByClass[$class] = $modelAttribute->type;
            $modelRepository[$class] = $modelAttribute->repositoryClass;
        }

        // Remove default post model by the framework if there is more than one with plugins and theme
        $allPostModelTypePost = array_filter($listTypeModelIndexedByClass, fn($type) => $type === 'post');
        if (count($allPostModelTypePost) > 1) {
            unset($listTypeModelIndexedByClass[PostTypeObject::class]);
            unset($modelRepository[PostTypeObject::class]);
            unset($postModelList[array_search(PostTypeObject::class, $postModelList)]);
        }

        $allCategoryModelTypeCategory = array_filter($listTypeModelIndexedByClass, fn($type) => $type === 'category');
        if (count($allCategoryModelTypeCategory) > 1) {
            unset($listTypeModelIndexedByClass[CategoryObject::class]);
            unset($modelRepository[CategoryObject::class]);
            unset($termModelList[array_search(CategoryObject::class, $termModelList)]);
        }

        $allTagModelTypeTag = array_filter($listTypeModelIndexedByClass, fn($type) => $type === 'post_tag');
        if (count($allTagModelTypeTag) > 1) {
            unset($listTypeModelIndexedByClass[TagObject::class]);
            unset($modelRepository[TagObject::class]);
            unset($termModelList[array_search(TagObject::class, $termModelList)]);
        }


        $container->setParameter('simply.model.list.post_model', $postModelList);
        $container->setParameter('simply.model.list.term_model', $termModelList);
        $container->setParameter('simply.model.mapping.model_repository', $modelRepository);
        $container->setParameter('simply.model.mapping.type_model', $listTypeModelIndexedByClass);
    }

}
