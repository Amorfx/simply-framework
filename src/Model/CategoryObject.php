<?php

namespace Simply\Core\Model;

use Simply\Core\Attributes\TermModel;
use Simply\Core\Repository\CategoryRepository;

#[TermModel(type: 'category', repositoryClass: CategoryRepository::class)]
class CategoryObject extends TermObject
{
}
