<?php

namespace Simply\Core\Model;

use Simply\Core\Repository\CategoryRepository;
use Simply\Core\Repository\TagRepository;

class CategoryObject extends TermObject
{
    public function getTitle(): string
    {
        return $this->term->name;
    }

    public function getMeta(string $meta, bool $single): mixed
    {
        return get_term_meta($this->term->term_id, $meta, $single);
    }

    public static function getType(): string
    {
        return 'category';
    }
}
