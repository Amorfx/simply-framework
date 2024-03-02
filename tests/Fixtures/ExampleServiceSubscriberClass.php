<?php

namespace Simply\Tests\Fixtures;

use Simply\Core\Attributes\Action;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

class ExampleServiceSubscriberClass implements ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;
}
