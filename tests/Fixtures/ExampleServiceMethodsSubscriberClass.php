<?php

namespace Simply\Tests\Fixtures;

use Symfony\Contracts\Service\ServiceMethodsSubscriberTrait;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class ExampleServiceMethodsSubscriberClass implements ServiceSubscriberInterface
{
    use ServiceMethodsSubscriberTrait;
}
