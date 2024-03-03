<?php

use Simply\Core\Cache\CacheDirectoryManager;
use Simply\Core\Contract\PluginInterface;
use Simply\Core\Contract\RegisterModelInterface;
use Simply\Core\DependencyInjection\CorePlugin;
use Simply\Core\DependencyInjection\Extension\NavMenu\NavMenuExtension;
use Simply\Core\DependencyInjection\Extension\PostType\PostTypeExtension;
use Simply\Core\DependencyInjection\Extension\Taxonomy\TaxonomyExtension;
use Simply\Core\Model\ModelFactory;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\ProxyDumper;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\LazyProxy\Instantiator\LazyServiceInstantiator;
use Symfony\Component\DependencyInjection\LazyProxy\PhpDumper\LazyServiceDumper;

// if vendor exist the user use the plugin not with the boilerplate or install manually
$vendorPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendorPath)) {
    require_once __DIR__ . '/vendor/autoload.php';
}


define('SIMPLY_CACHE_DIRECTORY', __DIR__ . '/cache');

final class Simply
{
    private static ?Container $container = null;
    /**
     * @var PluginInterface[]|RegisterModelInterface[]
     */
    private static array $simplyPlugins = [];
    private static array $wpPluginsPath = [];
    private static array $wpThemePath = [];

    private static function initContainer(): void
    {
        $file = CacheDirectoryManager::getCachePath('container.php');
        $containerConfigCache = new ConfigCache($file, WP_DEBUG);

        // register configuration directories
        // Default path of framework
        $configDirectories = apply_filters('simply/config/directories', [__DIR__ . '/config']);


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
        // For theme, we have only one theme register not many
        if (!empty(self::$wpThemePath)) {
            $themeConfig = self::$wpThemePath['path'] . '/config';
            if (file_exists($themeConfig)) {
                $configDirectories[] = $themeConfig;
            }
        }

        if (!$containerConfigCache->isFresh()) {
            $extensions = apply_filters('simply/config/container_extensions', [
                new PostTypeExtension(),
                new TaxonomyExtension(),
                new NavMenuExtension(),
            ]);

            self::registerSimplyPlugin(new CorePlugin($extensions, $configDirectories, self::$wpPluginsPath, self::$wpThemePath));

            $containerBuilder = new ContainerBuilder();
            // In symfony component kernel.debug parameter must be added
            $containerBuilder->setParameter('kernel.debug', WP_DEBUG);

            // Build plugins
            $modelFactory = new ModelFactory();
            foreach (self::$simplyPlugins as $plugin) {
                $plugin->build($containerBuilder);
                $interfaces = class_implements($plugin);
                if (is_array($interfaces) && in_array(RegisterModelInterface::class, $interfaces)) {
                    $plugin->registerModel($modelFactory);
                }
            }

            // force autoconfigure true
            foreach ($containerBuilder->getDefinitions() as $id => $d) {
                $d->setAutoconfigured(true);
            }
            $containerBuilder->compile();

            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump(['class' => 'CachedContainer']),
                $containerBuilder->getResources()
            );
        }

        require_once $file;
        self::$container = new CachedContainer(); // @phpstan-ignore-line
    }

    public static function registerPlugin(string $path, string $namespace = ''): void
    {
        self::$wpPluginsPath[] = ['path' => $path, 'namespace' => $namespace];
    }

    public static function registerSimplyPlugin(PluginInterface $plugin): void
    {
        self::$simplyPlugins[] = $plugin;
    }

    public static function registerTheme(string $path, string $namespace = ''): void
    {
        self::$wpThemePath = ['path' => $path, 'namespace' => $namespace];
    }

    /**
     * @return Container
     */
    public static function getContainer(): Container
    {
        if (is_null(self::$container)) {
            self::initContainer();
        }
        return self::$container;
    }

    public static function bootstrap()
    {
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
    public static function get($id)
    {
        return self::getContainer()->get($id);
    }
}

// Use after_setup_theme and not init because the command manager use cli_init to register command
add_action('after_setup_theme', function () {
    Simply::bootstrap();
});

add_action('deactivate_plugin', function () {
    CacheDirectoryManager::deleteCache();
});

add_action('activate_plugin', function () {
    CacheDirectoryManager::deleteCache();
});
