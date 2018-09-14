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

    protected function createNetherlands()
    {
        // Ok. Not the real one, but the model.

        $country = Country::new($this->repository);
        $country->alpha_2 = 'NL';
        $country->name = 'The Netherlands';
        $this->repository->save($country);
        return $country;
    }

    protected function createBelgium()
    {
        // We've tried.

        $country = Country::new($this->repository);
        $country->alpha_2 = 'BE';
        $country->name = 'Belgium';
        $this->repository->save($country);
        return $country;
    }

    protected function createEurope()
    {
        // I'm out of jokes.
        $region = Region::new($this->repository);
        $region->name = 'Europe';
        $this->repository->save($region);
        return $region;
    }
}
