<?php

namespace SimplyFramework\Admin\Menu;

use SimplyFramework\Contract\ReferencerTrait;
use SimplyFramework\Contract\RenderedTrait;
use SimplyFramework\Form\FormGenerator;

class AdminMenu {
    use RenderedTrait;
    use ReferencerTrait;

    private $pageTitle;
    private $pageSlug;
    private $optionFields;
    private $formGenerator;

    public function __construct($pageTitle, $pageSlug, $optionFields, FormGenerator $formGenerator) {
        $this->pageTitle = $pageTitle;
        $this->pageSlug = $pageSlug;
        $this->optionFields = $optionFields;
        $this->optionFields[] = ['type' => 'submit', 'options' => ['label' => __('Save'), 'attr' => ['class' => 'button button-primary']]];
        $this->formGenerator = $formGenerator;
    }

    /**
     * Get all data for the form
     * @return array
     */
    public function getOptionsForForm() {
        $data = [];
        foreach ($this->optionFields as $slug => $dataField) {
            if ($dataField['type'] !== 'submit') {
                $data[$slug] = get_option($slug);
            }
        }
        return $data;
    }

    public function render() {
        $form = $this->formGenerator->createForm($this->optionFields);
        $form->handleRequest();
        if ($form->isSubmitted()) {
            $this->saveWpOptions($form->getData());
        } else {
            $form->setData($this->getOptionsForForm());
        }
        $this->getTemplateEngine()->render('admin/menu/default.html.twig', [
            'pageTitle' => $this->pageTitle,
            'form' => $form->createView()
        ]);
    }

    public function saveWpOptions($data) {
        foreach ($this->optionFields as $slug => $dataField) {
            if ($dataField['type'] !== 'submit') {
                if (array_key_exists($slug, $data) && !empty($data[$slug])) {
                    update_option($slug, $data[$slug], false);
                } else {
                    delete_option($slug);
                }
            }
        }
    }
}
