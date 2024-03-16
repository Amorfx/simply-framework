<?php

namespace Simply\Core\Model;

use Simply\Core\Contract\ModelInterface;
use Simply\Core\Repository\TagRepository;

class TagObject extends TermObject
{
    public static function getType(): string
    {
        return 'post_tag';
    }
}
