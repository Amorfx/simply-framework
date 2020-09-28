<?php

namespace SimplyFramework\Contract;

use SimplyFramework\Template\TemplateEngine;

trait RenderedTrait {
    /**
     * @return TemplateEngine|\Symfony\Component\DependencyInjection\Container|null
     * @throws \Exception
     */
    public function getTemplateEngine() {
        return \Simply::getContainer()->get('framework.template_engine');
    }
}
