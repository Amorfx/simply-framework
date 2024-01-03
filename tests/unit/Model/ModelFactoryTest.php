<?php

namespace Simply\Tests\Model;

use Simply\Core\Model\CategoryObject;
use Simply\Core\Model\ModelFactory;
use Simply\Core\Model\PostTypeObject;
use Simply\Core\Model\TagObject;
use Simply\Core\Model\UserObject;
use Simply\Tests\Fixtures\Model\ExamplePostModel;
use Simply\Tests\Fixtures\Model\ExampleTermModel;
use Simply\Tests\SimplyTestCase;
use Brain\Monkey;

class ModelFactoryTest extends SimplyTestCase {
    public function testCreatePostTypeModel() {
        $mapping = array(ExamplePostModel::class, PostTypeObject::class);

        $postMock = \Mockery::mock(\WP_Post::class);
        $postMock->post_type = 'example_type';
        // Model factory use get class and want to have exactly a classname but a mock is not WP_Post but Mock_xx_WP_Post so mock get_class
        Monkey\Functions\when('get_class')->justReturn(\WP_Post::class);
        Monkey\Functions\when('apply_filters')->justReturn($mapping);
        $this->assertInstanceOf(ExamplePostModel::class, ModelFactory::create($postMock));
    }

    public function testCreatePostTypeModelNotFoundDefault() {
        $mapping = array(ExamplePostModel::class, PostTypeObject::class);
        $postMock = \Mockery::mock(\WP_Post::class);
        $postMock->post_type = 'does_not_exist';
        // Model factory use get class and want to have exactly a classname but a mock is not WP_Post but Mock_xx_WP_Post so mock get_class
        Monkey\Functions\when('get_class')->justReturn(\WP_Post::class);
        Monkey\Functions\when('apply_filters')->justReturn($mapping);
        $this->assertInstanceOf(PostTypeObject::class, ModelFactory::create($postMock));
    }

    public function testClassNotSupported() {
        $this->expectException(\Exception::class);
        ModelFactory::create(new \stdClass());
    }

    public function testCreateTermModel() {
        $mapping = [ExampleTermModel::class, TagObject::class, CategoryObject::class];
        Monkey\Functions\when('get_class')->justReturn(\WP_Term::class);
        Monkey\Functions\when('apply_filters')->justReturn($mapping);

        $termMock = \Mockery::mock(\WP_Term::class);
        $termMock->taxonomy = 'example_type';
        $this->assertInstanceOf(ExampleTermModel::class, ModelFactory::create($termMock));
    }

    public function testTaxonomyNotSupported() {
        Monkey\Functions\when('get_class')->justReturn(\WP_Term::class);
        $termMock = \Mockery::mock(\WP_Term::class);
        $termMock->taxonomy = 'does_not_exist';
        $this->expectExceptionMessage('The taxonomy does_not_exist is not supported');
        ModelFactory::create($termMock);
    }

    public function testCreateUserModel() {
        Monkey\Functions\when('get_class')->justReturn(\WP_User::class);
        $userMock = \Mockery::mock(\WP_User::class);
        $this->assertInstanceOf(UserObject::class, ModelFactory::create($userMock));
    }

    public function testNotCreateModel() {
        $this->assertFalse(ModelFactory::create(null));
    }

    public function testRegisterModels() {
        $factory = new ModelFactory();
        $class = new \stdClass();
        $class->a = 'ok';
        $expected = array($class);
        $factory->registerPostModel(array($class));
        $factory->registerTermModel(array($class));
        $factoryReflection = new \ReflectionClass($factory);
        $this->assertSame($expected, $factoryReflection->getStaticPropertyValue('postTypeModelClasses'));
        $this->assertSame($expected, $factoryReflection->getStaticPropertyValue('termTypeModelClasses'));
    }
    
    public function testRegisterMultipleModelsWithMultipleCall() {
        $factory = new ModelFactory();
        $classA = 'ClassA';
        $classB = 'ClassB';
        $expected = array($classA, $classB);
        $factory->registerPostModel(array($classA));
        $factory->registerTermModel(array($classA));
        $factory->addPostModel(array($classB));
        $factory->addTermModel(array($classB));
        $factoryReflection = new \ReflectionClass($factory);
        $this->assertSame($expected, $factoryReflection->getStaticPropertyValue('postTypeModelClasses'));
        $this->assertSame($expected, $factoryReflection->getStaticPropertyValue('termTypeModelClasses'));
    }
}
