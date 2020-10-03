<?php

namespace SimplyFramework\Admin\Menu;

use SimplyFramework\Contract\RenderFormTrait;
use SimplyFramework\Form\FormGenerator;

class AdminMenu {
    use RenderFormTrait;

    private $pageTitle;
    private $pageSlug;
    private $nonce;

    public function __construct($pageTitle, $pageSlug, $optionFields, FormGenerator $formGenerator) {
        $this->pageTitle = $pageTitle;
        $this->pageSlug = $pageSlug;
        $this->fields = $optionFields;
        $this->nonce = 'adminmenu_page_' . $pageSlug;
        $this->fields[] = ['type' => 'submit', 'options' => ['label' => __('Save'), 'attr' => ['class' => 'button button-primary']]];
        $this->fields['admin_menu_nonce'] = ['type' => 'nonce', 'options' => ['data' => wp_create_nonce($this->nonce)]];
        $this->initReferenceFields();
        $this->formGenerator = $formGenerator;
    }

    /**
     * Get all data for the form
     * @return array
     */
    public function getOptionsForForm() {
        $data = [];
        foreach ($this->fields as $slug => $dataField) {
            if ($dataField['type'] !== 'submit') {
                $data[$slug] = get_option($slug);
            }
        }
        return $data;
    }

    public function render() {
        $form = $this->formGenerator->createForm($this->fields);
        $form->handleRequest();
        if ($form->isSubmitted() && $form->isValid()) {
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
        foreach ($this->fields as $slug => $dataField) {
            if ($slug === 'admin_menu_nonce') {
                if (!wp_verify_nonce($data[$slug], $this->nonce)) {
                    wp_die('Tu te crois mâlin ?');
                }
            }

            if ($dataField['type'] !== 'submit' && $dataField['type'] !== 'nonce') {
                if (array_key_exists($slug, $data) && !empty($data[$slug])) {
                    update_option($slug, $data[$slug], false);
                } else {
                    delete_option($slug);
                }
            }
        }
    }
}
