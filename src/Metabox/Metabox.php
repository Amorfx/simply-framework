<?php

namespace SimplyFramework\Metabox;

use http\Client\Request;
use SimplyFramework\Form\FormGenerator;
use SimplyFramework\Template\TemplateEngine;
use Symfony\Component\Form\FormInterface;

/**
 * Class Metabox
 * Main Metabox class to render post meta with form builder
 *
 * @package SimplyFramework\Metabox
 */
class Metabox {
    private $id;
    private $post;
    private $fields;
    private $formGenerator;
    /**
     * @var FormInterface
     */
    private $form;
    /**
     * @var TemplateEngine
     */
    private $engine;

    public function __construct(string $id, $post, array $fields, FormGenerator $formGenerator) {
        $this->id = $id;
        $this->post = $post;
        $this->fields = $fields;
        $this->formGenerator = $formGenerator;
        $this->engine = \Simply::getContainer()->get('framework.template_engine');
        $this->form = $this->formGenerator->createForm($this->fields, $this->id);
    }

    public function render() {
        $this->engine->render('admin/metabox/default.html.twig', [
            'title' => 'test title',
            'form' => $this->form->createView()
        ]);
    }

    public function setPost(\WP_Post $post) {
        $this->post = $post;
    }

    public function saveFields() {
        var_dump($this->form->handleRequest()->getData()); die;
    }
}
