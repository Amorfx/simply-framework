<?php

namespace Simply\Core\Model;

use Simply\Core\Contract\ModelInterface;
use Simply\Core\Repository\TagRepository;

class TagObject extends TermObject {
    static function getRepository() {
        return \Simply::get(TagRepository::class);
    }

    static function getType() {
        return 'post_tag';
    }
}
