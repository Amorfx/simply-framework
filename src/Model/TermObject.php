<?php

namespace Simply\Core\Model;

use Simply\Core\Contract\ModelInterface;
use Simply\Core\Repository\TagRepository;

abstract class TermObject implements ModelInterface {
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

    public function getSlug() {
        return $this->term->slug;
    }

    public function getLink() {
        return get_term_link($this->term);
    }

    public function getMeta($meta, $single) {
        return get_term_meta($this->term->term_id, $meta, $single);
    }

    abstract static function getType();
}
