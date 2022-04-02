<?php

use Simply\Core\Cache\CacheDirectoryManager;
use Simply\Core\Contract\PluginInterface;
use Simply\Core\DependencyInjection\CorePlugin;
use Simply\Core\DependencyInjection\Extension\NavMenu\NavMenuExtension;
use Simply\Core\DependencyInjection\Extension\PostType\PostTypeExtension;
use Simply\Core\DependencyInjection\Extension\Taxonomy\TaxonomyExtension;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\ProxyDumper;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

// if vendor exist the user use the plugin not with the boilerplate or install manually
$vendorPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendorPath)) {
    require_once __DIR__ . '/vendor/autoload.php';
}


define('SIMPLY_CACHE_DIRECTORY', __DIR__ . '/cache');

class Simply {
    private static $container;
    /**
     * @var PluginInterface[]
     */
    private static $simplyPlugins = array();
    private static array $wpPluginsPath = array();

    private static function initContainer() {
        $file = CacheDirectoryManager::getCachePath('container.php');
        $containerConfigCache = new ConfigCache($file, WP_DEBUG);

        // register configuration directories
        // Default path of framework
        $configDirectories = apply_filters('simply/config/directories', array(__DIR__ . '/config'));

        // Register path of plugins and theme
        if (!empty(self::$wpPluginsPath)) {
            /** @var array{path: string, namespace: string} $pluginInfo */
            foreach (self::$wpPluginsPath as $pluginInfo) {
                $pluginConfig = $pluginInfo['path'] . '/config';
                if (file_exists($pluginConfig)) {
                    $configDirectories[] = $pluginConfig;
                }
            }
        }

        if (!$containerConfigCache->isFresh()) {
            $extensions = apply_filters('simply/config/container_extensions', array(
                new PostTypeExtension,
                new TaxonomyExtension,
                new NavMenuExtension,
            ));

            self::registerSimplyPlugin(new CorePlugin($extensions, $configDirectories, self::$wpPluginsPath));

            $containerBuilder = new ContainerBuilder();
            $containerBuilder->setProxyInstantiator(new RuntimeInstantiator());
            // In symfony component kernel.debug parameter must be added
            $containerBuilder->setParameter('kernel.debug', WP_DEBUG);

            // TODO deprecated after beta ? Use Class implement PluginInterface ?
            do_action('simply/core/build', $containerBuilder);

            // Build plugins
            foreach (self::$simplyPlugins as $plugin) {
                $plugin->build($containerBuilder);
            }

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

    public static function registerPlugin(string $path, string $namespace = ''): void {
        self::$wpPluginsPath[] = array('path' => $path, 'namespace' => $namespace);
    }

    public static function registerSimplyPlugin(PluginInterface $plugin): void {
        self::$simplyPlugins[] = $plugin;
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
        do_action('simply/core/after_bootstrap');
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
});

add_action('deactivate_plugin', function() {
    CacheDirectoryManager::deleteCache();
});

add_action('activate_plugin', function() {
    CacheDirectoryManager::deleteCache();
});

