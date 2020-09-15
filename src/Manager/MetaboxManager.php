<?php

namespace SimplyFramework\Manager;

use SimplyFramework\Contract\ManagerInterface;

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

    public function render(string $id) {
        var_dump($this->get($id));
        echo $id;
    }

    public function initMetaboxes() {
        foreach ($this->metaboxes as $id => $args) {
            add_meta_box($id, $args['title'], function() use ($id) {
                $this->render($id);
            }, $args['screen']);
        }
    }
}
