<?php

namespace Simply\Core\Model;

use Simply\Core\Repository\CategoryRepository;
use Simply\Core\Repository\TagRepository;

class CategoryObject extends TermObject {

    public function getTitle() {
        return $this->term->name;
    }

    public function getMeta($meta, $single) {
        return get_term_meta($this->term->term_id, $meta, $single);
    }

    static function getType() {
        return 'category';
    }
}
