<?php

namespace Simply\Core\Contract;

use Simply\Core\Template\TemplateEngine;

trait RenderedTrait {
    /**
     * @return TemplateEngine|\Symfony\Component\DependencyInjection\Container|null
     * @throws \Exception
     */
    public function getTemplateEngine() {
        return \Simply::getContainer()->get('framework.template_engine');
    }

    abstract function render();
}
