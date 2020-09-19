<?php

namespace SimplyFramework\Template;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

class TemplateEngine {
    private $engine;

    public function __construct() {
        // the Twig file that holds all the default markup for rendering forms
        // this file comes with TwigBridge
        $defaultFormTheme = 'admin/metabox/form_div_layout.html.twig';

        // the path to TwigBridge library so Twig can locate the
        // form_div_layout.html.twig file
        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());
        // the path to your other templates
        $viewsDirectory = realpath(__DIR__.'/../../views');

        $twig = new Environment(new FilesystemLoader([
            $viewsDirectory
        ]), [
            'cache' => SIMPLY_CACHE_DIRECTORY . '/twig'
        ]);
        $formEngine = new TwigRendererEngine([$defaultFormTheme], $twig);
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => function () use ($formEngine) {
                return new FormRenderer($formEngine);
            }
        ]));

        // adds the FormExtension to Twig
        $twig->addExtension(new FormExtension());

        $this->engine = $twig;
    }

    public function render($view, array $context) {
        $this->engine->display($view, $context);
    }
}
