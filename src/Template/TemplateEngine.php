<?php

namespace SimplyFramework\Template;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\TwigFunction;

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
        $viewsDirectory = [realpath(__DIR__.'/../../views')];
        $viewsDirectory = apply_filters('simply_views_directory', $viewsDirectory);

        $twig = new Environment(new FilesystemLoader($viewsDirectory), [
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
        $twig = $this->addTwigFunctions($twig);

        $this->engine = $twig;
    }

    public function addTwigFunctions($twig) {
        $twig->addFunction(new TwigFunction('function', [$this, 'execFunction']));
        $twig->addFunction(new TwigFunction('fn', [$this, 'execFunction']));
        return $twig;
    }

    public function execFunction($function_name) {
        $args = func_get_args();
        array_shift($args);
        if ( is_string($function_name) ) {
            $function_name = trim($function_name);
        }
        return call_user_func_array($function_name, ($args));
    }

    public function render($view, array $context) {
        $this->engine->display($view, $context);
    }
}
