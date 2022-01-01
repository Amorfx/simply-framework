<?php

namespace Simply\Tests\Cache;

use Simply\Core\Cache\CacheDirectoryManager;
use Simply\Tests\SimplyTestCase;

class CacheDirectoryManagerTest extends SimplyTestCase {
    public function testGetCachePath() {
        define('SIMPLY_CACHE_DIRECTORY', '/var/www/wordpress/wp-content/mu-plugins/simply-framework/cache');
        $this->assertEquals('/var/www/wordpress/wp-content/mu-plugins/simply-framework/cache/myfile.php', CacheDirectoryManager::getCachePath('myfile.php'));
    }
}
