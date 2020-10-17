<?php

namespace SimplyFramework\Model;

use SimplyFramework\Contract\ModelInterface;
use SimplyFramework\Repository\TagRepository;

class TermObject implements ModelInterface {
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

    // TODO another get function

    public function getMeta($meta, $single) {
        return get_term_meta($this->term->term_id, $meta, $single);
    }

    static function getRepository() {
        return \Simply::getContainer()->get(TagRepository::class);
    }

    static function getType() {
        return 'post_tag';
    }
}
