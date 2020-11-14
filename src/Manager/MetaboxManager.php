<?php

namespace SimplyFramework\Manager;

use SimplyFramework\Contract\ManagerInterface;
use SimplyFramework\Form\FormGenerator;
use SimplyFramework\Metabox\Metabox;

/**
 * Class MetaboxManager
 * Manage view metabox default
 *
 * @package SimplyFramework\Manager
 */
class MetaboxManager implements ManagerInterface {
    private $metaboxes;
    /**
     * @var Metabox[]
     */
    private $metaboxInstance;
    private $formGenerator;

    public function __construct(array $metaboxes, FormGenerator $formGenerator) {
        $this->metaboxes = $metaboxes;
        $this->metaboxInstance = [];
        $this->formGenerator = $formGenerator;
    }

    public function initialize() {
        // Use current screen to instanciate metabox
        add_action('current_screen', [$this, 'createMetaboxInstances']);
        add_action('add_meta_boxes', [$this, 'addMetaboxes']);
        add_action('save_post', [$this, 'saveDataMetaboxes'], 10, 2);
    }

    public function createMetaboxInstances() {
        foreach ($this->metaboxes as $id => $args) {
            if (!array_key_exists('callable', $args)) {
                $fields = [];
                if (array_key_exists('fields', $args)) {
                    $fields = $args['fields'];
                }

                $screen = null;
                if (array_key_exists('screen', $args)) {
                    $screen = $args['screen'];
                }
                $this->metaboxInstance[$id] = new Metabox($id, $screen, null, $fields, $this->formGenerator);
            }
        }
    }

    public function addMetaboxes() {
        foreach ($this->metaboxes as $id => $args) {
            add_meta_box($id, $args['title'], function($post) use ($id, $args) {
                $this->renderMetabox($id, $args, $post);
            }, $args['screen']);
        }
    }

    /**
     * Render default view metabox or the callable function
     * @param string $id
     * @param array $metaboxArgs
     * @param $post
     */
    public function renderMetabox(string $id, array $metaboxArgs, $post) {
        if (array_key_exists($id, $this->metaboxInstance)) {
            $instance=  $this->get($id);
            $instance->setPost($post);
            $instance->render();
        } else {
            $callable = explode('@', $metaboxArgs['callable']);
            $metaboxInstance = new $callable[0]();
            call_user_func(array($metaboxInstance, $callable[1]), $post);
        }
    }

    public function get($id) {
        if (array_key_exists($id, $this->metaboxInstance)) {
            return $this->metaboxInstance[$id];
        }
    }

    public function saveDataMetaboxes($post_id) {
        $currentScreen = get_current_screen();
        foreach ($this->metaboxInstance as $aMetabox) {
            if ($aMetabox->supports($currentScreen)) {
                $aMetabox->saveFields($post_id);
            }
        }
    }
}
