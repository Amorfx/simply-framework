<?php

namespace Simply\Core\Model;

use Simply\Core\Attributes\TermModel;
use Simply\Core\Repository\TagRepository;

#[TermModel(type: 'post_tag', repositoryClass: TagRepository::class)]
class TagObject extends TermObject
{
    public static function getType(): string
    {
        return 'post_tag';
    }
}
