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
}
