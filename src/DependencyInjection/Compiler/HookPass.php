<?php

namespace Simply\Core\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

/**
 * Add container.service_subscriber tag if class use ServiceSubscriberTrait
 *
 * @package SimplyFramework\Container\Compiler
 */
class HookPass implements CompilerPassInterface {
    public function process(ContainerBuilder $container) {
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
    }
}
