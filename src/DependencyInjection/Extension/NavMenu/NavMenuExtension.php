<?php

namespace Simply\Core\DependencyInjection\Extension\NavMenu;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class NavMenuExtension implements ExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $config = $processor->processConfiguration($configuration, $configs);

        // replace trans(***) with __()
        foreach ($config as $slug => $value) {
            if (strpos($value, 'trans(') !== -1) {
                $anArg = str_replace(' ', '', $value);
                preg_match('/trans\((.*),(.*)\)/', $anArg, $matches);
                if (!empty($matches)) {
                    $value = __($matches[1], $matches[2]);
                }
            }
            $config[$slug] = $value;
        }
        $container->setParameter('nav_menu', $config);
    }

    public function getNamespace(): bool
    {
        return false;
    }

    public function getXsdValidationBasePath(): bool
    {
        return false;
    }

    public function getAlias(): string
    {
        return 'nav_menu';
    }
}
