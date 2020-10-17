<?php

namespace SimplyFramework\Repository;

use SimplyFramework\Contract\ModelInterface;
use SimplyFramework\Model\ModelFactory;
use SimplyFramework\Model\PostTypeObject;

class PostRepository extends AbstractRepository {
    private function getPostType(){
        return call_user_func(array($this::getClassName(), 'getType'));
    }
    /**
     * @param mixed $id
     *
     * @return mixed|ModelInterface|null
     * @throws \Exception
     */
    public function find($id) {
        $post = get_post($id);
        return ModelFactory::create($post);
    }

    /**
     * @return ModelInterface[]
     * @throws \Exception
     */
    public function findAll() {
        $query = new \WP_Query([
            'post_type' => $this->getPostType(),
            'posts_per_page' => -1,
        ]);
        $returnModels = [];
        $allPosts = $query->get_posts();
        foreach ($allPosts as $aPost) {
            $returnModels[] = ModelFactory::create($aPost);
        }
        wp_reset_postdata();
        return $returnModels;
    }

    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null) {
        $postType = call_user_func(array($this::getClassName(), 'getType'));
        $queryArgs = array_merge($criteria, [
            'post_type' => $this->getPostType(),
            'orderby' => $orderBy,
            'posts_per_page' => $limit,
            'offset' => $offset
        ]);
        $query = new \WP_Query($queryArgs);
        $returnModels = [];
        $allPosts = $query->get_posts();
        foreach ($allPosts as $aPost) {
            $returnModels[] = ModelFactory::create($aPost);
        }
        wp_reset_postdata();
        return $returnModels;
    }

    public function findOneBy(array $criteria) {
        $models = $this->findBy($criteria, null, 1);
        if (count($models) > 0) {
            return $models[0];
        }
        return false;
    }

    public function getClassName() {
        return PostTypeObject::class;
    }
}
