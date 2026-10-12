<?php

namespace Simply\Core\DependencyInjection\Compiler;

use LogicException;
use ReflectionClass;
use Simply\Core\Attributes\Action;
use Simply\Core\Attributes\Filter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

/**
 * Add container.service_subscriber tag if class use ServiceSubscriberTrait
 * Collect the #[Action] and #[Filter] hooks of all services for the HookManager
 *
 * @package SimplyFramework\Container\Compiler
 */
class HookPass implements CompilerPassInterface
{
    public const HOOK_MANAGER = 'framework.manager.hook';

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

        if (!$container->hasDefinition(self::HOOK_MANAGER)) {
            return;
        }

        // #[Action] and #[Filter] methods of any service, by service id: the HookManager gets the services through a
        // locator, so that a service is only instantiated when WordPress runs one of its hooks
        $hooks = [];
        $services = [];
        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();
            if ($definition->isAbstract() || empty($class) || !class_exists($class)) {
                continue;
            }
            foreach ((new ReflectionClass($class))->getMethods() as $method) {
                foreach ([...$method->getAttributes(Action::class), ...$method->getAttributes(Filter::class)] as $attribute) {
                    if (!$method->isPublic()) {
                        throw new LogicException(sprintf(
                            'The method "%s::%s()" has a #[%s] attribute but is not public: WordPress cannot call it.',
                            $class,
                            $method->getName(),
                            (new ReflectionClass($attribute->getName()))->getShortName()
                        ));
                    }
                    foreach ($method->getParameters() as $parameter) {
                        if ($parameter->isPassedByReference()) {
                            throw new LogicException(sprintf(
                                'The method "%s::%s()" takes $%s by reference: hooks declared with #[%s] receive their arguments by value. Register this hook with a HookableInterface service instead.',
                                $class,
                                $method->getName(),
                                $parameter->getName(),
                                (new ReflectionClass($attribute->getName()))->getShortName()
                            ));
                        }
                    }
                    /** @var Action|Filter $hook */
                    $hook = $attribute->newInstance();
                    $hooks[] = [
                        'type' => $hook instanceof Action ? 'action' : 'filter',
                        'hook' => $hook->getHook(),
                        'priority' => $hook->getPriority(),
                        'accepted_args' => $hook->getNumberArguments(),
                        'service' => $id,
                        'class' => $class,
                        'method' => $method->getName(),
                    ];
                    $services[$id] = new Reference($id);
                }
            }
        }

        $container->getDefinition(self::HOOK_MANAGER)
            ->replaceArgument(1, $hooks)
            ->replaceArgument(2, ServiceLocatorTagPass::register($container, $services));
    }
}
