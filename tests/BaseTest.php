<?php

namespace WesleyE\JsonModel\Test;

use PHPUnit\Framework\TestCase;
use WesleyE\JsonModel\Test\TestModels\Country;
use WesleyE\JsonModel\Test\TestModels\Region;
use WesleyE\JsonModel\Repository;

abstract class BaseTest extends TestCase
{
    protected $repository;

    public function setUp()
    {
        clearRepository();
        $this->repository = Repository::getInstance();
        $this->repository->setRepositoryPath(__DIR__ . '/Json/');
        $this->repository->setRepositoryClassPath('\\WesleyE\JsonModel\Test\\TestModels\\');
    }

    public function clearCacheAndRepository()
    {
        clearRepository();
        $this->repository->clearModelCache();
    }
}
