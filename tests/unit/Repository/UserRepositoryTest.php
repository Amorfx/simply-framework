<?php

namespace Simply\Tests\Repository;

use Brain\Monkey;
use Simply\Core\Model\UserObject;
use Simply\Core\Repository\UserRepository;
use Simply\Tests\SimplyTestCase;

class UserRepositoryTest extends SimplyTestCase
{
    public function testFindByDoesNotSendNullArguments(): void
    {
        Monkey\Functions\expect('get_users')->once()->with(['role' => 'editor', 'number' => 3])->andReturn([]);

        (new UserRepository('user', UserObject::class))->findBy(['role' => 'editor'], null, 3);
    }
}
