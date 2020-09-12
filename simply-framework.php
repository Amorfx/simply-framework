<?php


require_once __DIR__ . '/vendor/autoload.php';


// based on something in your project

$file = __DIR__ .'/cache/container.php';
$containerConfigCache = new ConfigCache($file, $isDebug);

if (!$containerConfigCache->isFresh()) {
    $containerBuilder = new ContainerBuilder();
    // ...
    $containerBuilder->compile();

    $dumper = new PhpDumper($containerBuilder);
    $containerConfigCache->write(
        $dumper->dump(['class' => 'MyCachedContainer']),
        $containerBuilder->getResources()
    );
}

require_once $file;
$container = new MyCachedContainer();
