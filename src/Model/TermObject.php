<?php

namespace Simply\Core\Model;

use Simply\Core\Contract\ModelInterface;
use Simply\Core\Repository\TagRepository;

abstract class TermObject implements ModelInterface
{
    /**
     * @var \WP_Term
     */
    public $term;

    public function __construct(\WP_Term $term)
    {
        $this->term = $term;
    }

    public function getTitle(): string
    {
        return $this->term->name;
    }

    public function getSlug(): string
    {
        return $this->term->slug;
    }

    /** @phpstan-ignore-next-line  */
    public function getLink(): \WP_Term|\WP_Error|bool|int|array|string|null
    {
        return get_term_link($this->term);
    }

    public function getMeta(string $meta, bool $single): mixed
    {
        return get_term_meta($this->term->term_id, $meta, $single);
    }

    abstract public static function getType(): string;
}
