<?php

namespace Simply\Tests\Cache;

use Composer\Autoload\ClassLoader;
use Simply\Core\Cache\CacheFingerprint;
use Simply\Tests\SimplyTestCase;

class CacheFingerprintTest extends SimplyTestCase
{
    private const PACKAGES = ['vendor/a' => ['1.0.0', 'aaa'], 'vendor/b' => ['2.0.0', 'bbb']];

    public function testSameInputsGiveTheSameFingerprint()
    {
        $this->assertSame(
            CacheFingerprint::compute(['debug' => false], self::PACKAGES),
            CacheFingerprint::compute(['debug' => false], self::PACKAGES)
        );
    }

    public function testFingerprintIsAShortDirectoryName()
    {
        $this->assertMatchesRegularExpression('/^[0-9a-f]{16}$/', CacheFingerprint::compute([], self::PACKAGES));
    }

    public function testContextChangeGivesAnotherFingerprint()
    {
        $this->assertNotSame(
            CacheFingerprint::compute(['theme' => ['path' => '/themes/a']], self::PACKAGES),
            CacheFingerprint::compute(['theme' => ['path' => '/themes/b']], self::PACKAGES)
        );
    }

    public function testPackageUpdateGivesAnotherFingerprint()
    {
        $updated = self::PACKAGES;
        $updated['vendor/b'] = ['2.1.0', 'ccc'];

        $this->assertNotSame(
            CacheFingerprint::compute([], self::PACKAGES),
            CacheFingerprint::compute([], $updated)
        );
    }

    public function testInstalledPackagesAreReadFromComposerByDefault()
    {
        $this->assertNotSame(
            CacheFingerprint::compute([], []),
            CacheFingerprint::compute([])
        );
    }

    public function testPackagesOfAnotherAutoloaderAreIgnored()
    {
        // WP-CLI runs from a phar that registers its own Composer autoloader and installed packages
        $vendorDir = sys_get_temp_dir() . '/simply-framework-tests/other-vendor';
        @mkdir($vendorDir . '/composer', 0777, true);
        file_put_contents($vendorDir . '/composer/installed.php', '<?php return ' . var_export([
            'root' => ['name' => 'wp-cli/wp-cli-bundle', 'pretty_version' => '2.12.0', 'version' => '2.12.0.0', 'reference' => 'abc', 'type' => 'library', 'install_path' => $vendorDir, 'aliases' => [], 'dev' => false],
            'versions' => ['wp-cli/wp-cli' => ['pretty_version' => '2.12.0', 'version' => '2.12.0.0', 'reference' => 'abc', 'type' => 'library', 'install_path' => $vendorDir, 'aliases' => [], 'dev_requirement' => false]],
        ], true) . ';');
        $before = CacheFingerprint::compute([]);

        $loader = new ClassLoader($vendorDir);
        $loader->register();
        try {
            $this->assertSame($before, CacheFingerprint::compute([]));
        } finally {
            $loader->unregister();
        }
    }

    public function testPhpVersionIsPartOfTheFingerprint()
    {
        // The container dumped by Symfony on PHP 8.4 uses syntax that PHP 8.2 cannot parse
        $this->assertNotSame(
            CacheFingerprint::compute([], self::PACKAGES, '8.2'),
            CacheFingerprint::compute([], self::PACKAGES, '8.4')
        );
    }

    public function testCurrentPhpVersionIsUsedByDefault()
    {
        $this->assertSame(
            CacheFingerprint::compute([], self::PACKAGES, PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION),
            CacheFingerprint::compute([], self::PACKAGES)
        );
    }
}
