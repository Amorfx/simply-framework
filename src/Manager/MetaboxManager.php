<?php

namespace SimplyFramework\Manager;

use Simply;
use SimplyFramework\Contract\ManagerInterface;
use SimplyFramework\Metabox\Metabox;

/**
 * Class MetaboxManager
 * Manage view metabox default
 *
 * @package SimplyFramework\Manager
 */
class MetaboxManager implements ManagerInterface {
    private $metaboxes;

    public function __construct(array $metaboxes) {
        $this->metaboxes = $metaboxes;
    }

    public function initialize() {
        add_action('add_meta_boxes', [$this, 'initMetaboxes']);
    }

    public function get($id) {
        if (array_key_exists($id, $this->metaboxes)) {
            return $this->metaboxes[$id];
        }
    }

    public function render(array $metaboxArgs, $post) {
        if (!array_key_exists('callable', $metaboxArgs)) {
            $metabox = new Metabox(Simply::getContainer(), $post);
            $metabox->render();
        } else {
            $callable = explode('@', $metaboxArgs['callable']);
            $metaboxInstance = new $callable[0]();
            call_user_func(array($metaboxInstance, $callable[1]), $post);
        }
    }

    public function initMetaboxes() {
        foreach ($this->metaboxes as $id => $args) {
            add_meta_box($id, $args['title'], function($post) use ($id, $args) {
                $this->render($args, $post);
            }, $args['screen']);
        }
    }
}
