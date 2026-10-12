<?php

use Simply\Core\Cache\CacheDirectoryManager;
use Simply\Core\Cache\CacheFingerprint;
use Simply\Core\Contract\PluginInterface;
use Simply\Core\DependencyInjection\CorePlugin;
use Simply\Core\DependencyInjection\Extension\NavMenu\NavMenuExtension;
use Simply\Core\DependencyInjection\Extension\PostType\PostTypeExtension;
use Simply\Core\DependencyInjection\Extension\Taxonomy\TaxonomyExtension;
use Simply\Core\Model\ModelFactory;
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

final class Simply
{
    private static ?Container $container = null;
    /**
     * @var PluginInterface[]
     */
    private static array $simplyPlugins = [];
    /**
     * @var array{}|array{path: string, namespace: string}[]
     */
    private static array $wpPluginsPath = [];

    /**
     * @var array{}|array{path: string, namespace: string}
     */
    private static array $wpThemePath = [];

    private static function initContainer(): void
    {
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

        // Scope the cache to what it is built from: a package update, another theme or configuration directory
        // gets a fresh container even when WP_DEBUG is false and no file is checked for changes.
        // CorePlugin is left out because it is registered while building, it would change the fingerprint on the next call.
        $fingerprint = CacheFingerprint::compute([
            'debug' => WP_DEBUG,
            'config_directories' => $configDirectories,
            'plugins' => self::$wpPluginsPath,
            'theme' => self::$wpThemePath,
            'simply_plugins' => array_values(array_filter(
                array_map('get_class', self::$simplyPlugins),
                static fn(string $class): bool => $class !== CorePlugin::class
            )),
        ]);
        CacheDirectoryManager::setFingerprint($fingerprint);
        // One class per fingerprint: a container requested early (e.g. on plugins_loaded) and the one built at bootstrap
        // may differ, and both are loaded in the same request
        $containerClass = 'CachedContainer_' . $fingerprint;
        $file = CacheDirectoryManager::getCachePath('container.php');
        $containerConfigCache = new ConfigCache($file, WP_DEBUG);

        // Rebuild when a concurrent cache clear removed the file between the check and the include,
        // or when the file was dumped by another version of this code under the same fingerprint (source or path installs)
        if (!$containerConfigCache->isFresh() || (@include_once $file) === false || !class_exists($containerClass, false)) {
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
            foreach (self::$simplyPlugins as $plugin) {
                $plugin->build($containerBuilder);
                $interfaces = class_implements($plugin);
            }

            // force autoconfigure true
            foreach ($containerBuilder->getDefinitions() as $id => $d) {
                $d->setAutoconfigured(true);
            }
            $containerBuilder->compile();

            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump(['class' => $containerClass]),
                $containerBuilder->getResources()
            );
            CacheDirectoryManager::pruneStaleFingerprints();
            // Not require_once: a stale file of the same path may already have been included above
            if (!class_exists($containerClass, false)) {
                require $file;
            }
        }

        /** @var Container $container */
        $container = new $containerClass();
        self::$container = $container;
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

    public static function bootstrap(): void
    {
        self::initContainer();
        self::get('framework.manager')->initialize();
        do_action('simply/core/after_bootstrap');
    }

    /**
     * Shortcut function for fn get of the container
     * @param string $id
     *
     * @return object|Container|null
     * @throws Exception
     */
    public static function get(string $id): mixed
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
