<?php

namespace Simply\Tests\Integration\Debug;

use Simply\Core\Debug\FilterParams;
use Simply\Core\Debug\SearchEngine;
use Simply\Core\Hook\LazyHookListener;
use Simply\Tests\Fixtures\ExampleClassHook;
use Simply\Tests\Fixtures\ExampleLazyHookService;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SearchEngineTest extends \WP_UnitTestCase
{
    public function test_it_filter_hook_name(): void
    {
        new ExampleClassHook();
        $searchEngine = new SearchEngine($GLOBALS['wp_filter'], new FilterParams('my_hook'));
        $hooks = $searchEngine->search();
        self::assertCount(1, $hooks);
        self::assertArrayHasKey('my_hook', $hooks);
        self::assertCount(1, $hooks['my_hook']->callbacks);
    }

    public function test_it_filter_directory_name(): void
    {
        $this->loadAllWordpressFiles();
        new ExampleClassHook();
        $searchEngine = new SearchEngine($GLOBALS['wp_filter'], new FilterParams(null, 'tests/Fixtures'));
        $hooks = $searchEngine->search();
        self::assertCount(3, $hooks);


        $searchEngine = new SearchEngine($GLOBALS['wp_filter'], new FilterParams(null, 'not_exists'));
        $hooks = $searchEngine->search();
        self::assertCount(0, $hooks);
    }

    public function test_it_filter_function_name(): void
    {
        new ExampleClassHook();
        $searchEngine = new SearchEngine($GLOBALS['wp_filter'], new FilterParams(functionName: 'functionInit'));
        $hooks = $searchEngine->search();
        self::assertCount(1, $hooks);
        self::assertArrayHasKey('my_hook', $hooks);
        self::assertCount(1, $hooks['my_hook']->callbacks);

        $searchEngine = new SearchEngine($GLOBALS['wp_filter'], new FilterParams(functionName: 'notExist'));
        $hooks = $searchEngine->search();
        self::assertCount(0, $hooks);
    }

    public function test_it_finds_lazy_listeners_by_function_name_and_directory(): void
    {
        $this->loadAllWordpressFiles();
        add_filter('my_lazy_hook', new LazyHookListener(new ServiceLocator([]), 'app.title', ExampleLazyHookService::class, 'title'));

        $byName = (new SearchEngine($GLOBALS['wp_filter'], new FilterParams(functionName: 'title')))->search();
        self::assertArrayHasKey('my_lazy_hook', $byName);

        $byDirectory = (new SearchEngine($GLOBALS['wp_filter'], new FilterParams(null, 'tests/Fixtures')))->search();
        self::assertArrayHasKey('my_lazy_hook', $byDirectory);
    }

    private function loadAllWordpressFiles(): void
    {
        require_once ABSPATH . 'wp-admin/includes/admin.php';
        require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';
        require_once ABSPATH . 'wp-admin/includes/dashboard.php';
        require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/theme-install.php';
        require_once ABSPATH . 'wp-admin/includes/update-core.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }
}
