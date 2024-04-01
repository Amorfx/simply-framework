<?php

namespace Simply\Core\Query;

use Simply\Core\Contract\ModelInterface;
use Simply\Core\Model\ModelFactory;
use WP_Query;

class SimplyQuery
{
    private static object|bool|null $currentObject = null;
    public WP_Query $query;

    public function __construct(WP_Query $query)
    {
        $this->query = $query;
    }

    /**
     * Get the model associated with the WordPress queried object
     * @throws \Exception
     */
    public static function getCurrentObject(): object|bool|null
    {
        if (!is_null(self::$currentObject)) {
            return self::$currentObject;
        }

        $currentObject = get_queried_object();
        try {
            $model = ModelFactory::fromObject($currentObject);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
        self::$currentObject = $model;
        return $model;
    }

    /**
     * @return SimplyQuery
     */
    public static function getCurrentQuery(): SimplyQuery
    {
        global $wp_query;
        return new self($wp_query);
    }

    /**
     * @return array{}|array<object>|false
     * @throws \Exception
     */
    public function getQueriedPosts(): bool|array
    {
        $allPosts = $this->query->posts;
        if (sizeof($allPosts) > 0) {
            $returnPosts = [];
            foreach ($allPosts as $aPost) {
                $returnPosts[] = ModelFactory::fromObject($aPost);
            }
            return $returnPosts;
        }
        return false;
    }
}
