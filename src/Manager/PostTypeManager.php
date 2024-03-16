<?php

namespace Simply\Core\Manager;

use Simply\Core\Contract\ManagerInterface;

class PostTypeManager implements ManagerInterface
{
    /**
     * @var array<string, array<mixed>>
     */
    private array $postTypes;

    /**
     * @param array<string, array<mixed>> $postTypes
     */
    public function __construct(array $postTypes)
    {
        $this->postTypes = $postTypes;
    }

    public function initialize(): void
    {
        add_action('init', array($this, 'registerPostTypes'));
    }

    public function registerPostTypes(): void
    {
        foreach ($this->postTypes as $key => $args) {
            register_post_type($key, $args);
        }
    }
}
