<?php

namespace SimplyFramework\Manager;

use SimplyFramework\Command\ClearCacheCommand;
use SimplyFramework\Contract\ManagerInterface;
use SimplyFramework\Metabox\TermMetabox;

class TaxonomyManager implements ManagerInterface {
    private $taxonomies;
    private $termMetaboxes;

    public function __construct(array $taxonomies, array $termMetaboxes) {
        $this->taxonomies = $taxonomies;
        $this->termMetaboxes = $termMetaboxes;
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

        foreach ($this->termMetaboxes as $key => $termMetabox) {
            if (is_array($termMetabox['taxonomy'])) {
                foreach ($termMetabox['taxonomy'] as $aTax) {
                    new TermMetabox($key, $termMetabox['name'], $aTax, $termMetabox['fields']);
                }
            } else {
                new TermMetabox($key, $termMetabox['name'], $termMetabox['taxonomy'], $termMetabox['fields']);
            }
        }
    }
}
