<?php

namespace Simply\Core\Template;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TemplateEngine {
    private Environment $engine;

    public function __construct() {
        // the path to your other templates
        $viewsDirectory = apply_filters('simply/config/view_directories', []);

        // environnement configuration
        $envConfig = [
            'cache' => SIMPLY_CACHE_DIRECTORY . '/twig'
        ];

        if (WP_DEBUG === true) {
            $envConfig['cache'] = false;
            $envConfig['debug'] = true;
        }

        $twig = new Environment(new FilesystemLoader($viewsDirectory), $envConfig);

        if (WP_DEBUG === true) {
            $twig->addExtension(new DebugExtension());
        }

        $twig->addFunction(new TwigFunction('function', [$this, 'execFunction']));
        $twig->addFunction(new TwigFunction('fn', [$this, 'execFunction']));

        $this->engine = apply_filters('simply/config/template', $twig);;
    }

    public function getEngine() {
        return $this->engine;
    }

    public function execFunction($function_name) {
        $args = func_get_args();
        array_shift($args);
        if ( is_string($function_name) ) {
            $function_name = trim($function_name);
        }
        return call_user_func_array($function_name, ($args));
    }

    public function render($view, array $context, bool $display = true) {
        if ($display) {
            return $this->engine->display($view, $context);
        }
        return $this->engine->render($view, $context);
    }
}
