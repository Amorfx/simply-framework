<?php

namespace Simply\Core\Template;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TemplateEngine
{
    private Environment $engine;

    /**
     * @param array<string> $defaultViewsTheme
     */
    public function __construct(array $defaultViewsTheme = [])
    {
        // the path to your other templates
        $viewsDirectory = apply_filters('simply/config/view_directories', $defaultViewsTheme);

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

        $this->engine = apply_filters('simply/config/template', $twig);
    }

    public function getEngine(): Environment
    {
        return $this->engine;
    }

    public function execFunction(mixed $function_name): mixed
    {
        $args = func_get_args();
        array_shift($args);
        if (is_string($function_name)) {
            $function_name = trim($function_name);
        }
        return call_user_func_array($function_name, ($args));
    }

    /** @phpstan-ignore-next-line  */
    public function render($view, array $context, bool $display = true)
    {
        if ($display) {
            $this->engine->display($view, $context);
            return;
        }
        return $this->engine->render($view, $context);
    }
}
