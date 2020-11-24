<?php

namespace SimplyFramework\Manager;

use SimplyFramework\Contract\ManagerInterface;

class TaxonomyManager implements ManagerInterface {
    private $taxonomies;

    public function __construct(array $taxonomies) {
        $this->taxonomies = $taxonomies;
    }

    public function initialize() {
        add_action('init', function() {
            foreach ($this->taxonomies as $key => $args) {
                $taxArgs = [];
                if (array_key_exists('args', $args)) {
                    $taxArgs = $args['args'];
                }
                register_taxonomy($key, $args['object_type'], $taxArgs);
            }
        });
    }
}
