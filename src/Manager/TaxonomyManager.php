<?php

namespace Simply\Core\Manager;

use Simply\Core\Contract\ManagerInterface;

class TaxonomyManager implements ManagerInterface
{
    /** @var array<string, array<mixed>>  */
    private array $taxonomies;

    /** @param array<string, array<mixed>> $taxonomies  */

    public function __construct(array $taxonomies)
    {
        $this->taxonomies = $taxonomies;
    }

    public function initialize(): void
    {
        add_action('init', array($this, 'registerTaxonomies'));
    }

    public function registerTaxonomies(): void
    {
        foreach ($this->taxonomies as $key => $args) {
            $taxArgs = [];
            if (array_key_exists('args', $args)) {
                $taxArgs = $args['args'];
            }
            register_taxonomy($key, $args['object_type'], $taxArgs);
        }
    }
}
