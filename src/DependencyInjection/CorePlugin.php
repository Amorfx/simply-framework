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

/**
 * Main Simply plugin to build container with automatic extensions, config directories (load configuration...) provided by Simply class
 */
class CorePlugin implements PluginInterface {
    private array $extensions;
    private array $configDirectories;
    private array $wpPluginPaths;

    public function __construct(array $extensions, array $configDirectories, array $wpPluginPaths) {
        $this->extensions = $extensions;
        $this->configDirectories = $configDirectories;
        $this->wpPluginPaths = $wpPluginPaths;
    }

    public function build(ContainerBuilder $container): void {
        // Load extensions provider by Simply Class
        foreach ($this->extensions as $anExtension) {
            $container->registerExtension($anExtension);
            $container->loadFromExtension($anExtension->getAlias());
        }

        // Load all configurations
        foreach ($this->configDirectories as $aConfigDirectory) {
            $finder = new Finder();
            $fileLocator = new FileLocator($aConfigDirectory);
            // Load configurations file yaml or php
            $yamlFiles = iterator_to_array($finder->files()->in($aConfigDirectory)->name(array('*.yaml', '*.yml')), false);
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

        // Auto configure all file to service
        // TODO use the loader linked to the directory of the plugin
        if (!empty($this->wpPluginPaths)) {
            foreach ($this->wpPluginPaths as $pluginInfo) {
                $srcDir = $pluginInfo['path'] . '/src';
                if (file_exists($srcDir) && !empty($pluginInfo['namespace'])) {
                    // $loader->registerClasses(new Definition(), $pluginInfo['namespace'] . '\\', $srcDir);
                }
            }
        }

        // Add auto tags
        $container->registerForAutoconfiguration(HookableInterface::class)
            ->addTag('wp.hook');
        $container->addCompilerPass(new HookPass());
    }
}
