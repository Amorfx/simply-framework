<?php

namespace Simply\Core\Model;

use Simply\Core\Contract\ModelInterface;
use Simply\Core\Repository\TagRepository;

class TagObject implements ModelInterface {
    /**
     * @var \WP_Term
     */
    public $term;

    public function __construct(\WP_Term $term) {
        $this->term = $term;
    }

    public function getTitle() {
        return $this->term->name;
    }

    public function getMeta($meta, $single) {
        return get_term_meta($this->term->term_id, $meta, $single);
    }

    static function getRepository() {
        return \Simply::get(TagRepository::class);
    }

    static function getType() {
        return 'post_tag';
    }
}
