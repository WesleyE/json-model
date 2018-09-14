<?php

namespace WesleyE\JsonModel\Test;

use PHPUnit\Framework\TestCase;
use WesleyE\JsonModel\Test\TestModels\Country;
use WesleyE\JsonModel\Repository;
use WesleyE\JsonModel\Exceptions\ModelNotFoundException;

final class RepositoryTest extends BaseTest
{
    public function testCannotSaveEmptyFilePaths(): void
    {
        $this->expectException(\Exception::class);
        $instance = Country::new($this->repository);
        $instance->name = 'AC';

        // Try to save without a name
        $this->repository->save($instance);
    }

    public function testGetRepositoryClassPath(): void
    {
        $classPath = $this->repository->getRepositoryClassPath();
        $this->assertEquals('\\WesleyE\JsonModel\Test\\TestModels\\', $classPath);
    }

    public function testLoadNotExistingModel(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->loadModel('test.jhson');
    }
}
