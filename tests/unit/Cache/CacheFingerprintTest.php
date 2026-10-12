<?php

namespace Simply\Tests\Cache;

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
}
