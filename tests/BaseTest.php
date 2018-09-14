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
        $this->repository = new Repository(__DIR__ . '/Json/', '\\WesleyE\JsonModel\Test\\TestModels\\');
    }

    public function clearCacheAndRepository()
    {
        clearRepository();
        $this->repository->clearModelCache();
    }
}
