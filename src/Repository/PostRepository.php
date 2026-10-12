<?php

namespace Simply\Core\Repository;

use Simply\Core\Contract\ModelInterface;

class PostRepository extends AbstractRepository
{
    private function getPostType(): string
    {
        return $this->type;
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
        $queryArgs = array_merge(
            $criteria,
            $this->getOrderArgs($orderBy),
            $this->getPaginationArgs($limit, $offset, 'posts_per_page'),
            ['post_type' => $this->getPostType()]
        );

        $query = new \WP_Query($queryArgs);
        $returnModels = [];
        foreach ($query->posts as $aPost) {
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
}
