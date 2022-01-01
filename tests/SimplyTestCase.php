<?php

namespace Simply\Tests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;

abstract class SimplyTestCase extends TestCase {
    protected function setUp(): void {
        Monkey\setUp();
        parent::setUp();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }
}
