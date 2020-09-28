<?php

namespace SimplyFramework\Container\Extension\Taxonomy\Metabox;


use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class TaxonomyMetaboxExtension implements ExtensionInterface {
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);

        // TODO use class to translation
        // replace trans(***) with __()
        foreach ($config as $slug => $args) {
            foreach ($args as $key => $anArg) {
                if (!is_array($anArg)) {
                    if (strpos($anArg, 'trans(') !== -1) {
                        $anArg = str_replace(' ', '', $anArg);
                        preg_match('/trans\((.*),(.*)\)/', $anArg, $matches);
                        if (!empty($matches)) {
                            $args[$key] = __($matches[1], $matches[2]);
                        }
                    }
                }
            }
            $config[$slug] = $args;
        }
        $container->setParameter('term_metabox', $config);
    }

    public function getNamespace() {
        return false;
    }

    public function getXsdValidationBasePath() {}

    public function getAlias() {
        return 'term_metabox';
    }
}
