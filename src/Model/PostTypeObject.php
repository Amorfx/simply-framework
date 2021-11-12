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

    /**
     * @return string
     */
    public function getTitle() {
        return get_the_title($this->post);
    }

    public function getContent($more_link_text = null, $strip_teaser = false) {
        return get_the_content($more_link_text, $strip_teaser, $this->getID());
    }

    public function getExcerpt() {
        return get_the_excerpt($this->post);
    }

    public function getPermalink() {
        return get_permalink($this->getID());
    }

    public function getDate($format = '') {
        return get_the_date($format, $this->post);
    }

    public function getThumbnailUrl($size = 'post-thumbnail') {
        return get_the_post_thumbnail_url($this->getID(), $size);
    }

    public function getCategories(): array {
        $allCategories = get_the_category($this->getID());
        if (sizeof($allCategories) > 0 && (!$allCategories instanceof \WP_Error) && $allCategories !== false) {
            foreach ($allCategories as $k => $c) {
                $allCategories[$k] = ModelFactory::create($c);
            }
        } else {
            $allCategories = [];
        }
        return $allCategories;
    }

    public function getTags(): array {
        $allTags = get_the_tags($this->getID());
        if (is_array($allTags) && sizeof($allTags) > 0) {
            foreach ($allTags as $k => $t) {
                $allTags[$k] = ModelFactory::create($t);
            }
        } else {
            $allTags = [];
        }
        return $allTags;
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
