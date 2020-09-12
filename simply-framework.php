<?php

use SimplyFramework\Command\ClearCacheCommand;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/vendor/autoload.php';

class Simply {
    private static $container;
    private static function initContainer() {
        $file = __DIR__ .'/cache/container.php';
        $containerConfigCache = new ConfigCache($file, WP_DEBUG);
        $configDir = __DIR__ . '/config';

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = new ContainerBuilder();

            $finder = new Finder();
            $finder->files()->in($configDir);
            $loader = new YamlFileLoader($containerBuilder, new FileLocator($configDir));
            if ($finder->hasResults()) {
                foreach ($finder as $aFile) {
                    $loader->load($aFile->getRelativePathname());
                }
            }
            $containerBuilder->compile();

            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump(['class' => 'CachedContainer']),
                $containerBuilder->getResources()
            );
        }

        require_once $file;
        self::$container = new CachedContainer();
    }

    static function getContainer() {
        if (is_null(self::$container)) {
            self::initContainer();
        }
        return self::$container;
    }
}

add_action('cli_init', function() {
    new ClearCacheCommand();
});

