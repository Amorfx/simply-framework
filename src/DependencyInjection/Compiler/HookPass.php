<?php

namespace Simply\Core\DependencyInjection\Compiler;

use ReflectionClass;
use Simply\Core\Attributes\Action;
use Simply\Core\Attributes\Filter;
use Simply\Core\Compiler\HookCompiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

/**
 * Add container.service_subscriber tag if class use ServiceSubscriberTrait
 * Compile all hooks with parsing attributes
 *
 * @package SimplyFramework\Container\Compiler
 */
class HookPass implements CompilerPassInterface
{
    /**
     * @throws \ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        $allHookServices = $container->findTaggedServiceIds('wp.hook');
        if (!empty($allHookServices)) {
            foreach ($allHookServices as $id => $tags) {
                $definition = $container->findDefinition($id);
                if (!$definition->hasTag('container.service_subscriber') && in_array(ServiceSubscriberTrait::class, class_uses($definition->getClass()))) {
                    $definition->addTag('container.service_subscriber');
                    $definition->setAutowired(true);
                }
            }
        }

        // In php 8 we have Action and Filters Attribute that can be used to register action and filters
        // A class without HookableInterface can add action and filters with this attributes
        $classesToParse = array();

        foreach ($container->getDefinitions() as $d) {
            if (!empty($d->getClass()) && class_exists($d->getClass())) {
                $classesToParse[] = $d->getClass();
            }
        }
        // Parse to find hooks
        $hookCompiler = $this->getHookCompiler();
        foreach ($classesToParse as $c) {
            $ref = new ReflectionClass($c);
            foreach ($ref->getMethods() as $method) {
                $actionsAttribute = $method->getAttributes(Action::class);
                $filtersAttribute = $method->getAttributes(Filter::class);
                $attributes = array_merge($actionsAttribute, $filtersAttribute);
                if (empty($attributes)) {
                    continue;
                }
                $container->getDefinition($c)->addTag('simply.attribute_hooks');
                foreach ($attributes as $attr) {
                    /** @var Action|Filter $hooks */
                    $hooks = $attr->newInstance();
                    /** @phpstan-ignore-next-line  */
                    $hooks->setCallable(array($c, $method->getName()));
                    $hookCompiler->add($c, get_class($hooks), $hooks->getHook(), $method->getName(), $hooks->getPriority(), $hooks->getNumberArguments());
                }
            }
            $hookCompiler->compile();
            $container->setParameter('simply.compile_hooks', $hookCompiler->getFromCache());
        }
    }

    protected function getHookCompiler(): HookCompiler
    {
        return new HookCompiler();
    }
}
