<?php

namespace Simply\Tests\Template;

use Simply\Core\Template\TemplateEngine;
use Simply\Tests\SimplyTestCase;
use Brain\Monkey;
use Twig\Environment;

class TemplateEngineTest extends SimplyTestCase {
    /**
     * @runInSeparateProcess
     */
    public function testConstructClassDebugFalse() {
        define('WP_DEBUG', false);
        Monkey\Filters\expectApplied('simply_views_directory')->with(array());
        Monkey\Filters\expectApplied('simply_template_configuration');
        $engine = new TemplateEngine();
        $this->assertFalse($engine->getEngine()->isDebug());
    }

    /**
     * @runInSeparateProcess
     */
    public function testConstructClassDebugTrue() {
        define('WP_DEBUG', true);
        $engine = new TemplateEngine();
        $this->assertTrue($engine->getEngine()->isDebug());
    }

    /**
     * @runInSeparateProcess
     */
    public function testExecFunction() {
        define('WP_DEBUG', false);
        $engine = new TemplateEngine();
        Monkey\Functions\when('is_single')->justReturn(true);
        $this->assertTrue($engine->execFunction('is_single'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testRenderDisplay() {
        define('WP_DEBUG', false);
        $engine = new TemplateEngine();
        $twig = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->onlyMethods(array('display', 'render'))
            ->getMock();
        $twig->expects($this->once())->method('display')->with('view.html.twig', array('context' => 'ok'));
        $reflection = new \ReflectionClass($engine);
        $twigProperty = $reflection->getProperty('engine');
        $twigProperty->setAccessible(true);
        $twigProperty->setValue($engine, $twig);
        $engine->render('view.html.twig', array('context' => 'ok'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testRender() {
        define('WP_DEBUG', false);
        $engine = new TemplateEngine();
        $twig = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->onlyMethods(array('display', 'render'))
            ->getMock();
        $twig->expects($this->once())->method('render')->with('view.html.twig', array('context' => 'ok'));
        $reflection = new \ReflectionClass($engine);
        $twigProperty = $reflection->getProperty('engine');
        $twigProperty->setAccessible(true);
        $twigProperty->setValue($engine, $twig);
        $engine->render('view.html.twig', array('context' => 'ok'), false);
    }
}
