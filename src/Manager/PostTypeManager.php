<?php

namespace Simply\Core\Manager;

use Simply\Core\Contract\ManagerInterface;

class PostTypeManager implements ManagerInterface {
    private $postTypes;

    public function __construct(array $postTypes) {
        $this->postTypes = $postTypes;
    }

    public function initialize() {
        add_action('init', function() {
            foreach ($this->postTypes as $key => $args) {
                register_post_type($key, $args);
            }
        });
    }
}
