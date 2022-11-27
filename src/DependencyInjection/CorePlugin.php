<?php

namespace Simply\Core\DependencyInjection;

use Simply\Core\Contract\HookableInterface;
use Simply\Core\Contract\PluginInterface;
use Simply\Core\DependencyInjection\Compiler\HookPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Main Simply plugin to build container with automatic extensions, config directories (load configuration...) provided by Simply class
 */
class CorePlugin implements PluginInterface {
    private array $extensions;
    private array $configDirectories;
    private array $wpPluginPaths;
    private array $wpThemePath;

    public function __construct(array $extensions, array $configDirectories, array $wpPluginPaths, array $wpThemePath) {
        $this->extensions = $extensions;
        $this->configDirectories = $configDirectories;
        $this->wpPluginPaths = $wpPluginPaths;
        $this->wpThemePath = $wpThemePath;
    }

    public function build(ContainerBuilder $container): void {
        // Load extensions provider by Simply Class
        foreach ($this->extensions as $anExtension) {
            $container->registerExtension($anExtension);
            $container->loadFromExtension($anExtension->getAlias());
        }

        $hasThemePath = !empty($this->wpThemePath);

        // Auto configure all file to service
        // TODO use the loader linked to the directory of the plugin
        if (!empty($this->wpPluginPaths)) {
            foreach ($this->wpPluginPaths as $pluginInfo) {
                $srcDir = $pluginInfo['path'] . '/src';
                $this->registerClasses($container, $pluginInfo['namespace'], $srcDir);
            }
            // Same for files in Theme
            if ($hasThemePath) {
                $srcDir = $this->wpThemePath['path'] . '/src';
                $this->registerClasses($container, $this->wpThemePath['namespace'], $srcDir);
            }
        }

        // Load all configurations
        foreach ($this->configDirectories as $aConfigDirectory) {
            $finder = new Finder();
            $fileLocator = new FileLocator($aConfigDirectory);
            // Load configurations file yaml or php
            $yamlFiles = iterator_to_array($finder->files()->in($aConfigDirectory)->name(array(
                '*.yaml',
                '*.yml',
            )), false);
            if (!empty($yamlFiles)) {
                $loader = new YamlFileLoader($container, $fileLocator);
                foreach ($yamlFiles as $aFile) {
                    $loader->load($aFile->getRelativePathname());
                }
            }

            $phpFiles = iterator_to_array($finder::create()->files()->in($aConfigDirectory)->name('*.php'), false);
            if (!empty($phpFiles)) {
                $loader = new PhpFileLoader($container, $fileLocator);
                foreach ($phpFiles as $aFile) {
                    $loader->load($aFile->getRelativePathname());
                }
            }
        }

        // Put the views path if exist of theme into defaultViewsDirectory for TemplateEngine Class
        if ($hasThemePath) {
            $viewsDirectory = $this->wpThemePath['path'] . '/views';
            if (file_exists($viewsDirectory)) {
                $container->setParameter('default_views_directories', array($viewsDirectory));
            } else {
                $container->setParameter('default_views_directories', array());
            }
        } else {
            $container->setParameter('default_views_directories', array());
        }

        // Add auto tags
        $container->registerForAutoconfiguration(ServiceSubscriberInterface::class)
            ->addTag('container.service_subscriber');
        $container->registerForAutoconfiguration(HookableInterface::class)->addTag('wp.hook');
        $container->addCompilerPass(new HookPass());
        $container->setParameter('container.behavior_describing_tags', [
            'annotations.cached_reader',
            'container.do_not_inline',
            'container.service_locator',
            'container.service_subscriber',
            'kernel.event_subscriber',
            'kernel.event_listener',
            'kernel.locale_aware',
            'kernel.reset',
        ]);
    }

    private function registerClasses(ContainerBuilder $container, $namespace, $srcDir) {
        if (file_exists($srcDir) && !empty($namespace)) {
            $loader = new PhpFileLoader($container, new FileLocator($srcDir));
            $def = new Definition();
            $def->setAutoconfigured(true)
                ->setAutowired(true);
            $loader->registerClasses($def, $namespace . '\\', $srcDir);
        }
    }
}
