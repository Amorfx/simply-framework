<?php

namespace SimplyFramework\Metabox;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Metabox
 * Main Metabox class to render post meta with form builder
 * @package SimplyFramework\Metabox
 */
class Metabox {
    private $container;
    private $post;

    public function __construct($container, $post) {
        $this->container = $container;
        $this->post = $post;
    }

    public function render() {
        var_dump('dans metabox');
    }
}
