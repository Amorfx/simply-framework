<?php

namespace Simply\Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class UserModel extends Model
{
}
