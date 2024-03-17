<?php

namespace Simply\Tests\Model;

use Mockery;
use Simply\Tests\Fixtures\Model\ExamplePostModelCachedProperties;
use Simply\Tests\SimplyTestCase;
use WP_Post;

class HasCachedPropertiesTest extends SimplyTestCase
{
    public function testGetCachedProperty(): void
    {
        $postMock = Mockery::mock(WP_Post::class);
        $postMock->post_title = 'Example title';

        $model = new ExamplePostModelCachedProperties($postMock);
        $this->assertEquals('Example title', $model->title);

        // Verify its cached
        $postMock->post_title = 'New title';
        $this->assertEquals('Example title', $model->title);
    }

    public function testIsset(): void
    {
        $postMock = Mockery::mock(WP_Post::class);
        $model = new ExamplePostModelCachedProperties($postMock);
        $this->assertTrue(isset($model->title));
        $this->assertFalse(isset($model->otherProperty));
    }

    public function testUnset(): void
    {
        $postMock = Mockery::mock(WP_Post::class);
        $postMock->post_title = 'Example title';
        $model = new ExamplePostModelCachedProperties($postMock);

        $this->assertEquals('Example title', $model->title);

        unset($model->title);
        $postMock->post_title = 'New title';

        $this->assertEquals('New title', $model->title);
    }

    public function testReturnNull(): void
    {
        $postMock = Mockery::mock(WP_Post::class);
        $model = new ExamplePostModelCachedProperties($postMock);
        $this->assertNull($model->aPropertyThatDoesNotExist);
    }
}
