<?php

namespace Simply\Tests\Model;

use Simply\Core\Model\CategoryObject;
use Simply\Core\Model\ModelFactory;
use Simply\Core\Model\ModelManager;
use Simply\Core\Model\PostTypeObject;
use Simply\Core\Model\TagObject;
use Simply\Core\Model\UserObject;
use Simply\Tests\Fixtures\Model\ExamplePostModel;
use Simply\Tests\Fixtures\Model\ExampleTermModel;
use Simply\Tests\SimplyTestCase;
use Brain\Monkey;

class ModelFactoryTest extends SimplyTestCase
{
    public function testCreatePostTypeModel()
    {
        $modelManager = new ModelManager(modelTypeMapping: [ExamplePostModel::class => 'example_type']);
        $modelFactory = new ModelFactory($modelManager);

        $postMock = \Mockery::mock(\WP_Post::class);
        $postMock->post_type = 'example_type';
        // Model factory use get class and want to have exactly a classname but a mock is not WP_Post but Mock_xx_WP_Post so mock get_class
        Monkey\Functions\when('get_class')->justReturn(\WP_Post::class);
        $this->assertInstanceOf(ExamplePostModel::class, $modelFactory->create($postMock));
    }

    public function testCreatePostTypeModelNotFoundDefault()
    {
        $modelManager = new ModelManager();
        $modelFactory = new ModelFactory($modelManager);
        $postMock = \Mockery::mock(\WP_Post::class);
        $postMock->post_type = 'does_not_exist';
        // Model factory use get class and want to have exactly a classname but a mock is not WP_Post but Mock_xx_WP_Post so mock get_class
        Monkey\Functions\when('get_class')->justReturn(\WP_Post::class);
        $this->expectExceptionMessage('The type does_not_exist is not supported, the supported types are ');
        $modelFactory->create($postMock);
    }

    public function testClassNotSupported()
    {
        $modelManager = new ModelManager();
        $modelFactory = new ModelFactory($modelManager);
        $this->expectException(\Exception::class);
        $modelFactory->create(new \stdClass());
    }

    public function testCreateTermModel()
    {
        $modelManager = new ModelManager(modelTypeMapping: [ExampleTermModel::class => 'example_type']);
        $modelFactory = new ModelFactory($modelManager);
        Monkey\Functions\when('get_class')->justReturn(\WP_Term::class);

        $termMock = \Mockery::mock(\WP_Term::class);
        $termMock->taxonomy = 'example_type';
        $this->assertInstanceOf(ExampleTermModel::class, $modelFactory->create($termMock));
    }

    public function testCreateUserModel()
    {
        $modelManager = new ModelManager(modelTypeMapping: [UserObject::class => 'user']);
        $modelFactory = new ModelFactory($modelManager);
        Monkey\Functions\when('get_class')->justReturn(\WP_User::class);
        $userMock = \Mockery::mock(\WP_User::class);
        $this->assertInstanceOf(UserObject::class, $modelFactory->create($userMock));
    }
}
