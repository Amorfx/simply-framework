<?php

namespace Simply\Core\Model;

use Simply\Core\Contract\ModelInterface;
use Simply\Core\Repository\PostRepository;
use WP_Post;

class PostTypeObject implements ModelInterface
{
    /**
     * @var WP_Post
     */
    public $post;

    public function __construct(WP_Post $post) {
        $this->post = $post;
    }

    public static function getRepository() {
        return \Simply::get(PostRepository::class);
    }

    /**
     * @return string
     */
    public function getTitle() {
        return get_the_title($this->post);
    }

    public function getContent($more_link_text = null, $strip_teaser = false) {
        return get_the_content($more_link_text, $strip_teaser, $this->getID());
    }

    public function getPermalink() {
        return get_permalink($this->getID());
    }

    /**
     * @return int
     */
    public function getID() {
        return $this->post->ID;
    }

    /**
     * @param $key
     * @param false $single
     *
     * @return mixed
     */
    public function getMeta($key, $single = false) {
        return get_post_meta($this->post->ID, $key, $single);
    }

    public static function getType() {
        return 'post';
    }
}
