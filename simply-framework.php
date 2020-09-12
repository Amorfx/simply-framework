<?php

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/vendor/autoload.php';


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
$container = new CachedContainer();
