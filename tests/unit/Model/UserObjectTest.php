<?php

namespace Simply\Tests\Model;

use Simply\Core\Model\UserObject;
use Simply\Tests\SimplyTestCase;

class UserObjectTest extends SimplyTestCase
{
    public function testType()
    {
        $this->assertSame('user', UserObject::getType());
    }
}
