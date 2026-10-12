<?php

namespace Simply\Tests\Repository;

use Brain\Monkey;
use Simply\Core\Model\PostTypeObject;
use Simply\Core\Repository\PostRepository;
use Simply\Tests\SimplyTestCase;

class PostRepositoryTest extends SimplyTestCase
{
    private PostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        \WP_Query::$lastArgs = [];
        Monkey\Functions\when('wp_reset_postdata')->justReturn();
        $this->repository = new PostRepository('post', PostTypeObject::class);
    }

    public function testFindByPassesLimitAndOffset(): void
    {
        $this->repository->findBy([], null, 10, 20);

        $this->assertSame(10, \WP_Query::$lastArgs['posts_per_page']);
        $this->assertSame(20, \WP_Query::$lastArgs['offset']);
    }

    public function testFindByAcceptsStringOrderBy(): void
    {
        $this->repository->findBy([], 'title');

        $this->assertSame('title', \WP_Query::$lastArgs['orderby']);
        $this->assertArrayNotHasKey('order', \WP_Query::$lastArgs);
    }

    public function testFindByAcceptsOrderByWithoutOrder(): void
    {
        $this->repository->findBy([], ['orderby' => 'date']);

        $this->assertSame('date', \WP_Query::$lastArgs['orderby']);
        $this->assertArrayNotHasKey('order', \WP_Query::$lastArgs);
    }

    public function testFindByOrdersByMetaKey(): void
    {
        $this->repository->findBy([], ['meta_key' => 'price', 'order' => 'ASC']);

        $this->assertSame('price', \WP_Query::$lastArgs['meta_key']);
        $this->assertSame('meta_value', \WP_Query::$lastArgs['orderby']);
        $this->assertSame('ASC', \WP_Query::$lastArgs['order']);
    }

    public function testFindByKeepsCriteriaAndForcesPostType(): void
    {
        $this->repository->findBy(['post_type' => 'page', 'author' => 3]);

        $this->assertSame('post', \WP_Query::$lastArgs['post_type']);
        $this->assertSame(3, \WP_Query::$lastArgs['author']);
    }
}
