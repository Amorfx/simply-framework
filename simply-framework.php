<?php

use Simply\Core\Cache\CacheDirectoryManager;
use Simply\Core\Contract\HookableInterface;
use Simply\Core\DependencyInjection\Compiler\HookPass;
use Simply\Core\DependencyInjection\Extension\NavMenu\NavMenuExtension;
use Simply\Core\DependencyInjection\Extension\PostType\PostTypeExtension;
use Simply\Core\DependencyInjection\Extension\Taxonomy\TaxonomyExtension;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\ProxyDumper;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\ControllerArgumentValueResolverPass;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;

// if vendor exist the user use the plugin not with the boilerplate or install manually
$vendorPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendorPath)) {
    require_once __DIR__ . '/vendor/autoload.php';
}


define('SIMPLY_CACHE_DIRECTORY', __DIR__ . '/cache');
define('SIMPLY_RESOURCES_DIRECTORY', __DIR__ . '/resources');

class Simply {
    private static $container;
    private static array $pluginsPath = array();
    private static array $themePath = array();

    private static function initContainer() {
        $file = CacheDirectoryManager::getCachePath('container.php');
        $containerConfigCache = new ConfigCache($file, WP_DEBUG);

        // register configuration directories
        // Default path of framework
        $configDirectories = apply_filters('simply_config_directories', array(__DIR__ . '/config'));

        // Register path of plugins and theme
        if (!empty(self::$pluginsPath)) {
            /** @var array{path: string, namespace: string} $pluginInfo */
            foreach (self::$pluginsPath as $pluginInfo) {
                $pluginConfig = $pluginInfo['path'] . '/config';
                if (file_exists($pluginConfig)) {
                    $configDirectories[] = $pluginConfig;
                }
            }
        }

        $extensions = apply_filters('simply_container_extensions', array(
            new PostTypeExtension,
            new TaxonomyExtension,
            new NavMenuExtension,
        ));

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = new ContainerBuilder();
            $containerBuilder->setProxyInstantiator(new RuntimeInstantiator());
            // In symfony component kernel.debug parameter must be added
            $containerBuilder->setParameter('kernel.debug', WP_DEBUG);
            do_action('simply/core/build', $containerBuilder);

            // Add auto tags
            $containerBuilder->registerForAutoconfiguration(HookableInterface::class)
                ->addTag('wp.hook');

            foreach ($extensions as $anExtension) {
                $containerBuilder->registerExtension($anExtension);
                $containerBuilder->loadFromExtension($anExtension->getAlias());
            }

            foreach ($configDirectories as $aConfigDirectory) {
                $finder = new Finder();
                $fileLocator = new FileLocator($aConfigDirectory);
                // Load configurations file yaml or php
                $yamlFiles = iterator_to_array($finder->files()->in($aConfigDirectory)->name(array('*.yaml', '*.yml')), false);
                if (!empty($yamlFiles)) {
                    $loader = new YamlFileLoader($containerBuilder, $fileLocator);
                    foreach ($yamlFiles as $aFile) {
                        $loader->load($aFile->getRelativePathname());
                    }
                }

                $phpFiles = iterator_to_array($finder::create()->files()->in($aConfigDirectory)->name('*.php'), false);
                if (!empty($phpFiles)) {
                    $loader = new PhpFileLoader($containerBuilder, $fileLocator);
                    foreach ($phpFiles as $aFile) {
                        $loader->load($aFile->getRelativePathname());
                    }
                }
            }
            // Auto configure all file to service
            // TODO use the loader linked to the directory of the plugin
            if (!empty(self::$pluginsPath)) {
                foreach (self::$pluginsPath as $pluginInfo) {
                    $srcDir = $pluginInfo['path'] . '/src';
                    if (file_exists($srcDir) && !empty($pluginInfo['namespace'])) {
                        $loader->registerClasses(new Definition(), $pluginInfo['namespace'] . '\\', $srcDir);
                    }
                }
            }
            $containerBuilder->addCompilerPass(new HookPass());

            // force autoconfigure true
            foreach ($containerBuilder->getDefinitions() as $id => $d) {
                $d->setAutoconfigured(true);
            }
            $containerBuilder->compile();

            $dumper = new PhpDumper($containerBuilder);
            $dumper->setProxyDumper(new ProxyDumper('_simply_'));
            $containerConfigCache->write(
                $dumper->dump(['class' => 'CachedContainer']),
                $containerBuilder->getResources()
            );
        }

        require_once $file;
        self::$container = new CachedContainer();
    }

    public static function registerPlugin(string $path, string $namespace = '') {
        self::$pluginsPath[] = array('path' => $path, 'namespace' => $namespace);
    }

    /**
     * @return Container
     */
    public static function getContainer(): Container {
        if (is_null(self::$container)) {
            self::initContainer();
        }
        return self::$container;
    }

    static function bootstrap() {
        self::initContainer();
        self::get('framework.manager')->initialize();
    }

    /**
     * Shortcut function for fn get of the container
     * @param $id
     *
     * @return object|Container|null
     * @throws Exception
     */
    static function get($id) {
        return self::getContainer()->get($id);
    }
}

// Use after_setup_theme and not init because the command manager use cli_init to register command
add_action('after_setup_theme', function() {
    Simply::bootstrap();
    do_action('simply/core/after_bootstrap');
});

add_action('deactivate_plugin', function() {
    CacheDirectoryManager::deleteCache();
});

add_action('activate_plugin', function() {
    CacheDirectoryManager::deleteCache();
});

