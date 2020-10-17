<?php

namespace SimplyFramework\Model;

use SimplyFramework\Contract\ModelInterface;
use SimplyFramework\Repository\CategoryRepository;
use SimplyFramework\Repository\TagRepository;

class CategoryObject extends TermObject {

    public function getTitle() {
        return $this->term->name;
    }

    // TODO another get function

    public function getMeta($meta, $single) {
        return get_term_meta($this->term->term_id, $meta, $single);
    }

    static function getRepository() {
        return \Simply::getContainer()->get(CategoryRepository::class);
    }

    static function getType() {
        return 'category';
    }
}
