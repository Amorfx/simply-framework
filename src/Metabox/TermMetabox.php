<?php

namespace SimplyFramework\Metabox;

use SimplyFramework\Contract\RenderedTrait;
use Symfony\Component\Form\FormInterface;

/**
 * TODO create abstract class Metabox
 * Main class for term metabox (register, save, render)
 * Class TermMetabox
 *
 * @package SimplyFramework\Metabox
 */
class TermMetabox {

    use RenderedTrait;

    /**
     * @var string
     */
    private $id;

    /**
     * The metabox name
     * @var string
     */
    private $name;

    /**
     * Taxonomy link to the metabox
     * @var string
     */
    private $taxonomy;

    /**
     * Array description fields for form
     * @var array
     */
    private $fields;

    /**
     * The metabox form
     * @var FormInterface
     */
    private $form;

    public function __construct(string $id, string $name, string $taxonomy, array $fields) {
        $this->id = $id;
        $this->name = $name;
        $this->fields = $fields;
        $this->taxonomy = $taxonomy;

        // Render metabox
        add_action($this->taxonomy . '_add_form_fields', [$this, 'renderAdd']);
        add_action($this->taxonomy . '_edit_form', [$this, 'renderEdit']);

        // Save metabox
        add_action('edit_' . $this->taxonomy, [$this, 'saveTermMeta']);
        add_action('create_' . $this->taxonomy, [$this, 'saveTermMeta']);
    }

    /**
     * @return FormInterface
     */
    public function getForm() {
        if (is_null($this->form)) {
            $this->createForm();
        }
        return $this->form;
    }

    private function createForm() {
        $this->form = \Simply::getContainer()->get('framework.form_generator')->createForm($this->fields, $this->id);
    }

    public function renderAdd() {
        $form = $this->getForm();
        $this->getTemplateEngine()->render('admin/metabox/default.html.twig', [
            'title' => $this->name,
            'form' => $form->createView()
        ]);
    }

    public function renderEdit($term) {
        $form = $this->getForm();
        $dataForm = [];
        foreach ($this->fields as $key => $field) {
            $dataForm[$this->id . '_' . $key] = get_term_meta($term->term_id, $key, true);
        }
        $form->setData($dataForm);
        $this->getTemplateEngine()->render('admin/metabox/default.html.twig', [
            'title' => $this->name,
            'form' => $form->createView()
        ]);
    }

    public function saveTermMeta($term_id) {
        $form = $this->getForm();
        $form->handleRequest();
        $dataForm = $form->getData();
        foreach ($dataForm as $key => $aData) {
            $keyToSave = str_replace($this->id . '_', '', $key);
            update_term_meta($term_id, $keyToSave, sanitize_text_field($aData));
        }
    }
}
