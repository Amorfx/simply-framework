<?php

namespace Simply\Tests\Cache;

use Simply\Core\Cache\CacheDirectoryManager;
use Simply\Tests\SimplyTestCase;
use Symfony\Component\Filesystem\Filesystem;

class CacheDirectoryManagerTest extends SimplyTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        (new Filesystem())->remove(dirname(SIMPLY_CACHE_DIRECTORY));
        mkdir(SIMPLY_CACHE_DIRECTORY, 0777, true);
    }

    protected function tearDown(): void
    {
        CacheDirectoryManager::setFingerprint(null);
        (new Filesystem())->remove(dirname(SIMPLY_CACHE_DIRECTORY));
        parent::tearDown();
    }

    public function testGetCachePathWithoutFingerprint()
    {
        $this->assertSame(SIMPLY_CACHE_DIRECTORY . '/myfile.php', CacheDirectoryManager::getCachePath('myfile.php'));
    }

    public function testGetCachePathIsScopedByFingerprint()
    {
        CacheDirectoryManager::setFingerprint('abc123');

        $this->assertSame(SIMPLY_CACHE_DIRECTORY . '/abc123/container.php', CacheDirectoryManager::getCachePath('container.php'));
    }

    public function testDeleteCacheEmptiesTheDirectoryWithoutLeftovers()
    {
        file_put_contents(SIMPLY_CACHE_DIRECTORY . '/container.php', '<?php');
        mkdir(SIMPLY_CACHE_DIRECTORY . '/abc123/twig', 0777, true);

        CacheDirectoryManager::deleteCache();

        // The directory itself is kept: it may be a mount point (Docker volume), which cannot be renamed
        $this->assertDirectoryExists(SIMPLY_CACHE_DIRECTORY);
        $this->assertSame([], array_values(array_diff(scandir(SIMPLY_CACHE_DIRECTORY), ['.', '..'])));
        $this->assertSame([], glob(SIMPLY_CACHE_DIRECTORY . '.trash-*'));
    }

    public function testEntriesBeingRemovedByAnotherRequestAreLeftAlone()
    {
        mkdir(SIMPLY_CACHE_DIRECTORY . '/.trash-concurrent', 0777, true);
        mkdir(SIMPLY_CACHE_DIRECTORY . '/previous', 0777, true);
        CacheDirectoryManager::setFingerprint('current');

        CacheDirectoryManager::pruneStaleFingerprints();
        CacheDirectoryManager::deleteCache();

        $this->assertSame(['.trash-concurrent'], array_values(array_diff(scandir(SIMPLY_CACHE_DIRECTORY), ['.', '..'])));
    }

    public function testDeleteCacheWithoutCacheDirectoryDoesNothing()
    {
        (new Filesystem())->remove(SIMPLY_CACHE_DIRECTORY);

        CacheDirectoryManager::deleteCache();

        $this->assertDirectoryDoesNotExist(SIMPLY_CACHE_DIRECTORY);
    }

    public function testPruneStaleFingerprintsKeepsOnlyTheCurrentOne()
    {
        mkdir(SIMPLY_CACHE_DIRECTORY . '/current/twig', 0777, true);
        file_put_contents(SIMPLY_CACHE_DIRECTORY . '/current/container.php', '<?php');
        mkdir(SIMPLY_CACHE_DIRECTORY . '/previous', 0777, true);
        // Layout written by simply-framework <= 0.2.8, without fingerprint
        file_put_contents(SIMPLY_CACHE_DIRECTORY . '/container.php', '<?php');
        file_put_contents(SIMPLY_CACHE_DIRECTORY . '/hooks.php', '<?php');
        mkdir(SIMPLY_CACHE_DIRECTORY . '/twig', 0777, true);
        CacheDirectoryManager::setFingerprint('current');

        CacheDirectoryManager::pruneStaleFingerprints();

        $this->assertSame(['current'], array_values(array_diff(scandir(SIMPLY_CACHE_DIRECTORY), ['.', '..'])));
        $this->assertFileExists(SIMPLY_CACHE_DIRECTORY . '/current/container.php');
        $this->assertSame([], glob(SIMPLY_CACHE_DIRECTORY . '.trash-*'));
    }

    public function testPruneStaleFingerprintsWithoutFingerprintDoesNothing()
    {
        file_put_contents(SIMPLY_CACHE_DIRECTORY . '/container.php', '<?php');

        CacheDirectoryManager::pruneStaleFingerprints();

        $this->assertFileExists(SIMPLY_CACHE_DIRECTORY . '/container.php');
    }
}
