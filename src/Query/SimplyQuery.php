<?php

namespace Simply\Core\Query;

use Simply\Core\Contract\ModelInterface;
use Simply\Core\Model\ModelFactory;
use WP_Post;
use WP_Query;
use WP_Term;
use WP_User;

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

        $queriedObject = get_queried_object();
        // Blog home, 404, search and date archives have no queried object, post type archives return a WP_Post_Type:
        // none of them can have a model
        if (!$queriedObject instanceof WP_Post && !$queriedObject instanceof WP_Term && !$queriedObject instanceof WP_User) {
            return false;
        }

        try {
            $model = ModelFactory::fromObject($queriedObject);
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
