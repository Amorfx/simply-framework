<?php

namespace Simply\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Brain\Monkey;

abstract class SimplyTestCase extends TestCase {
    use MockeryPHPUnitIntegration;

    protected function setUp(): void {
        Monkey\setUp();
        parent::setUp();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }
}
