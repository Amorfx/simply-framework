<?php

namespace Simply\Core\DependencyInjection\Compiler;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Simply\Core\Attributes\Model;
use Simply\Core\Attributes\PostTypeModel;
use Simply\Core\Attributes\TermModel;
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

        $container->setParameter('model.list.post_model', $postModelList);
        $container->setParameter('model.list.term_model', $termModelList);
        $container->setParameter('model.mapping.model_repository', $modelRepository);
        $container->setParameter('model.mapping.type_model', $listTypeModelIndexedByClass);
    }
}
