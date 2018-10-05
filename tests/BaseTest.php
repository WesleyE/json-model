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

    protected function clearCacheAndRepository()
    {
        clearRepository();
        $this->repository->clearModelCache();
    }

    protected function createCountry($alpha2, $name)
    {
        $country = Country::new($this->repository);
        $country->alpha_2 = $alpha2;
        $country->name = $name;
        $this->repository->save($country);
        return $country;
    }

    protected function createRegion($name)
    {
        $region = Region::new($this->repository);
        $region->name = $name;
        $this->repository->save($region);
        return $region;
    }

    protected function createNetherlands()
    {
        return $this->createCountry('NL', 'The Netherlands');
    }

    protected function createBelgium()
    {
        return $this->createCountry('BE', 'Belgium');
    }

    protected function createChina()
    {
        return $this->createCountry('CN', 'China');
    }

    protected function createEurope()
    {
        return $this->createRegion('Europe');
    }

    protected function createAsia()
    {
        return $this->createRegion('Asia');
    }
}
