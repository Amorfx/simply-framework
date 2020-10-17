<?php

namespace SimplyFramework\Metabox;

use SimplyFramework\Contract\RenderFormTrait;
use SimplyFramework\Form\FormGenerator;
use Symfony\Component\Form\FormInterface;
use WP_Post;
use WP_Screen;

/**
 * Class Metabox
 * Main Metabox class to render post meta with form builder
 *
 * @package SimplyFramework\Metabox
 */
class Metabox {
    use RenderFormTrait;

    private $id;
    private $post;

    /**
     * @var array
     */
    private $supportsPage;

    /**
     * @var FormInterface
     */
    private $form;

    public function __construct(string $id, $screen, $post, array $fields, FormGenerator $formGenerator) {
        $this->id = $id;
        $this->post = $post;
        $this->fields = $fields;
        $this->initReferenceFields();

        /**
         * Init compatible page
         */
        $this->supportsPage = array();
        if (is_array($screen)) {
            foreach ($screen as $aScreen) {
                $screenObject = convert_to_screen($aScreen);
                $this->supportsPage[] = $screenObject->id;
            }
        } else {
            $screenObject = convert_to_screen($screen);
            $this->supportsPage[] = $screenObject->id;
        }

        $this->formGenerator = $formGenerator;
    }

    /**
     * Render the metabox view with a Template engine
     */
    public function render() {
        $this->form = $this->formGenerator->createForm($this->fields, $this->id);
        // Set Data to form
        $dataForm = [];
        foreach ($this->fields as $fieldKey => $args) {
            $keyData = $this->id . '_' . $fieldKey;
            $dataForm[$keyData] = get_post_meta($this->post->ID, $fieldKey, true);
            if (empty($dataForm[$keyData])) {
                unset($dataForm[$keyData]);
            }
        }
        $this->form->setData($dataForm);
        $this->getTemplateEngine()->render('admin/metabox/default.html.twig', [
            'form' => $this->form->createView()
        ]);
    }

    /**
     * @param WP_Post $post
     */
    public function setPost(WP_Post $post) {
        $this->post = $post;
    }

    /**
     * Use to know if metabox has to save metas or not
     * @param WP_Screen $screen
     *
     * @return bool
     */
    public function supports(WP_Screen $screen) {
        return in_array($screen->id, $this->supportsPage);
    }

    /**
     * Save all metas
     *
     * @param $post_id
     */
    public function saveFields($post_id) {
        $this->form = $this->formGenerator->createForm($this->fields, $this->id);
        $dataMetaboxes = $this->form->handleRequest()->getData();
        foreach ($dataMetaboxes as $meta => $value) {
            $metaKey = str_replace($this->id . '_', '', $meta);
            if (is_null($value)) {
                delete_post_meta($post_id, $metaKey);
            } else {
                update_post_meta($post_id, $metaKey, $value);
            }
        }
    }
}
