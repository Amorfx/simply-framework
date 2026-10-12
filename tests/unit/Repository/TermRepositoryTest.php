<?php

namespace Simply\Tests\Repository;

use Brain\Monkey;
use Simply\Core\Model\CategoryObject;
use Simply\Core\Model\TagObject;
use Simply\Core\Repository\CategoryRepository;
use Simply\Core\Repository\TagRepository;
use Simply\Core\Repository\TermRepository;
use Simply\Tests\SimplyTestCase;

class TermRepositoryTest extends SimplyTestCase
{
    public function testTermFindReturnsNullOnError(): void
    {
        Monkey\Functions\when('get_term')->justReturn(\Mockery::mock(\WP_Error::class));
        $repository = new class('genre', CategoryObject::class) extends TermRepository {
        };

        $this->assertNull($repository->find(999));
    }

    public function testTermFindReturnsNullWhenNotFound(): void
    {
        Monkey\Functions\when('get_term')->justReturn(null);
        $repository = new class('genre', CategoryObject::class) extends TermRepository {
        };

        $this->assertNull($repository->find(999));
    }

    public function testCategoryFindReturnsNullWhenNotFound(): void
    {
        Monkey\Functions\when('get_category')->justReturn(null);

        $this->assertNull((new CategoryRepository('category', CategoryObject::class))->find(999));
    }

    public function testTagFindReturnsNullWhenNotFound(): void
    {
        Monkey\Functions\when('get_tag')->justReturn(null);

        $this->assertNull((new TagRepository('post_tag', TagObject::class))->find(999));
    }

    public function testCategoryFindReturnsModel(): void
    {
        Monkey\Functions\when('get_category')->justReturn(\Mockery::mock(\WP_Term::class));

        $this->assertInstanceOf(CategoryObject::class, (new CategoryRepository('category', CategoryObject::class))->find(1));
    }

    public function testTermFindByUsesWordPressOrderbyKey(): void
    {
        Monkey\Functions\expect('get_terms')
            ->once()
            ->with(['hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC', 'number' => 5, 'taxonomy' => 'genre'])
            ->andReturn([]);
        $repository = new class('genre', CategoryObject::class) extends TermRepository {
        };

        $repository->findBy(['hide_empty' => false], ['orderby' => 'name', 'order' => 'ASC'], 5);
    }

    public function testCategoryFindByDoesNotSendNullArguments(): void
    {
        Monkey\Functions\expect('get_categories')->once()->with(['hide_empty' => false])->andReturn([]);

        (new CategoryRepository('category', CategoryObject::class))->findBy(['hide_empty' => false]);
    }

    public function testTagFindByDoesNotSendNullArguments(): void
    {
        Monkey\Functions\expect('get_tags')->once()->with(['orderby' => 'count', 'offset' => 2])->andReturn([]);

        (new TagRepository('post_tag', TagObject::class))->findBy([], 'count', null, 2);
    }
}
