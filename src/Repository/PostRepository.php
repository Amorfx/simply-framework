<?php

namespace Simply\Core\Repository;

use Simply\Core\Contract\ModelInterface;
use Simply\Core\Model\PostTypeObject;

class PostRepository extends AbstractRepository
{
    private function getPostType(): string
    {
        return call_user_func(array($this::getClassName(), 'getType'));
    }
    /**
     * @param mixed $id
     *
     * @return mixed|ModelInterface|null
     * @throws \Exception
     */
    public function find(mixed $id): mixed
    {
        $post = get_post($id);
        return $this->getReturnObject($post);
    }

    /**
     * @return ModelInterface[]
     * @throws \Exception
     */
    public function findAll(): array
    {
        $query = new \WP_Query([
            'post_type' => $this->getPostType(),
            'posts_per_page' => -1,
        ]);
        $returnModels = [];
        $allPosts = $query->posts;
        foreach ($allPosts as $aPost) {
            $returnModels[] = $this->getReturnObject($aPost);
        }
        wp_reset_postdata();
        return $returnModels;
    }

    /**
     * @param array $criteria
     * @param array|string|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array{}|object[]
     * @throws \Exception
     * @phpstan-ignore-next-line
     */
    public function findBy(array $criteria, array|string $orderBy = null, int $limit = null, int $offset  = null): array
    {
        $arrayMergeCriterias = ['post_type' => $this->getPostType()];

        // orderby example :
        // ['meta_key' => yourMetaKey, 'order' => 'ASC|DESC']
        // ['orderby' => id, 'order' => 'ASC|DESC']
        if (!is_null($orderBy)) {
            if (array_key_exists('meta_key', $orderBy)) {
                $arrayMergeCriterias['meta_key'] = $orderBy['meta_key'];
                $arrayMergeCriterias['orderby'] = $orderBy['meta_key'];
            } else {
                $arrayMergeCriterias['orderby'] = $orderBy['orderby'];
            }
            $arrayMergeCriterias['order'] = $orderBy['order'];
        }

        if (!is_null($limit)) {
            $arrayMergeCriterias['posts_per_page'] = $limit;
        }

        if (!is_null($offset)) {
            $arrayMergeCriterias['offset'] = $orderBy;
        }

        $queryArgs = array_merge($criteria, $arrayMergeCriterias);
        $query = new \WP_Query($queryArgs);
        $returnModels = [];
        $allPosts = $query->posts;
        foreach ($allPosts as $aPost) {
            $returnModels[] = $this->getReturnObject($aPost);
        }
        wp_reset_postdata();
        return $returnModels;
    }

    public function findOneBy(array $criteria): ?object
    {
        $models = $this->findBy($criteria, null, 1);
        if (count($models) > 0) {
            return $models[0];
        }
        return null;
    }

    public function getClassName(): string
    {
        return PostTypeObject::class;
    }
}
