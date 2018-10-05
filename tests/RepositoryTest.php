<?php

namespace WesleyE\JsonModel\Test;

use PHPUnit\Framework\TestCase;
use WesleyE\JsonModel\Test\TestModels\Country;
use WesleyE\JsonModel\Test\TestModels\Region;
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

    public function testLoadsAllFiles(): void
    {
        $this->clearCacheAndRepository();

        $netherlands = $this->createNetherlands();
        $belgium = $this->createBelgium();

        $countryModels = $this->repository->loadAllModelsByDirName('countries');
        $this->assertEquals(2, count($countryModels));

        $this->createEurope();

        $regionModels = $this->repository->loadAllModelsByType(Region::class);
        $this->assertEquals(1, count($regionModels));
    }

    public function testIfFindsModelsByAttribute()
    {
        $this->clearCacheAndRepository();

        $netherlands = $this->createNetherlands();
        $belgium = $this->createBelgium();
        $europe = $this->createEurope();

        $china = $this->createChina();
        $asia = $this->createAsia();

        $europe->countries()->attach($netherlands);
        $europe->countries()->attach($belgium);

        $asia->countries()->attach($china);

        $nlCountries = $this->repository->getModelsByTypeAndAttribute('Country', 'alpha_2', 'NL');

        $this->assertEquals(1, count($nlCountries));

        // By ref
        $europeCountries = $this->repository->getModelsByTypeAndAttribute('Country', 'region', 'regions/Europe.json');
        $this->assertEquals(2, count($europeCountries));

        $asiaCountries = $this->repository->getModelsByTypeAndAttribute('Country', 'region', $asia);
        $this->assertEquals(1, count($asiaCountries));
    }
}
