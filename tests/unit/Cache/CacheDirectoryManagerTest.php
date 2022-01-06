<?php

namespace Simply\Tests\Cache;

use Simply\Core\Cache\CacheDirectoryManager;
use Simply\Tests\SimplyTestCase;

class CacheDirectoryManagerTest extends SimplyTestCase {
    public function testGetCachePath() {
        $this->assertEquals('/tmp/myfile.php', CacheDirectoryManager::getCachePath('myfile.php'));
    }
}
