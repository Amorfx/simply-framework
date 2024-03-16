<?php

namespace Simply\Core\Model;

use Exception;
use Simply\Core\Contract\ModelInterface;
use WP_Post;

class PostTypeObject implements ModelInterface
{
    public WP_Post $post;

    public function __construct(WP_Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return get_the_title($this->post);
    }

    public function getContent(string $more_link_text = null, bool $strip_teaser = false): string
    {
        return get_the_content($more_link_text, $strip_teaser, $this->getID());
    }

    public function getExcerpt(): string
    {
        return get_the_excerpt($this->post);
    }

    public function getPermalink(): bool|string
    {
        return get_permalink($this->getID());
    }

    public function getDate(string $format = ''): bool|int|string
    {
        return get_the_date($format, $this->post);
    }

    /**
     * @param string|int[] $size
     * @return bool|string
     */
    public function getThumbnailUrl(string|array $size = 'post-thumbnail'): bool|string
    {
        return get_the_post_thumbnail_url($this->getID(), $size);
    }

    /**
     * @return array<object>
     * @throws Exception
     */
    public function getCategories(): array
    {
        $allCategories = get_the_category($this->getID());
        if (is_array($allCategories) && sizeof($allCategories) > 0) {
            foreach ($allCategories as $k => $c) {
                $allCategories[$k] = ModelFactory::create($c);
            }
        } else {
            $allCategories = [];
        }
        return $allCategories;
    }

    /**
     * @return array<object>
     * @throws Exception
     */
    public function getTags(): array
    {
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
    public function getID(): int
    {
        return $this->post->ID;
    }

    public function getMeta(string $key, bool $single = false): mixed
    {
        return get_post_meta($this->post->ID, $key, $single);
    }

    public static function getType(): string
    {
        return 'post';
    }
}
