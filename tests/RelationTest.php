<?php

namespace WesleyE\JsonModel\Test;

use PHPUnit\Framework\TestCase;
use WesleyE\JsonModel\Test\TestModels\Country;
use WesleyE\JsonModel\Test\TestModels\Region;
use WesleyE\JsonModel\Repository;
use WesleyE\JsonModel\Exceptions\ModelNotFoundException;
use WesleyE\JsonModel\Relations\Exceptions\NoModelReferenceException;

final class RelationTest extends BaseTest
{
    public function testCannotAttachUnsavedRelations(): void
    {
        $this->clearCacheAndRepository();

        // Setup the region
        $region = Region::new($this->repository);
        $region->name = 'Europe';

        // Setup the country
        $country = Country::new($this->repository);
        $country->alpha_2 = 'NL';
        $country->name = 'The Netherlands';

        // Attach the region
        $this->expectException(ModelNotFoundException::class);
        $country->region()->associate($region);
    }

    public function testCanCreateCountryWithRegion(): void
    {
        $this->clearCacheAndRepository();

        // Setup the region and save it
        $region = $this->createEurope();
        
        // Setup the country
        $country = $this->createNetherlands();

        // Attach the region
        $country->region()->associate($region);

        // Test if we can get the region
        $associatedRegion = $country->region()->get();
        $this->assertInstanceOf(Region::class, $associatedRegion);

        // Test if we can grab the region
        $this->assertEquals('Europe', $associatedRegion->getAttribute('name'));
    }

    public function testSyncsUnsavedAttributesOnGrab(): void
    {
        /*
         * country 1, country 2, region 1. Update region 1 'from' country 1, see when we grab
         * region from country 2, the attributes are properly set.
         */
        $this->clearCacheAndRepository();

        // Setup the region and save it
        $region = $this->createEurope();
        
        // Setup the country
        $country = $this->createNetherlands();
        $country->region()->associate($region);
        $this->repository->save($country);

        // Setup the country
        $country2 = $this->createBelgium();
        $country2->region()->associate($region);
        $this->repository->save($country2);

        $associatedRegion = $country->region()->get();
        $associatedRegion->name = 'Europe_2';
        $this->repository->save($associatedRegion);

        $associatedRegion = $country2->region()->get();
        
        $this->assertEquals('Europe_2', $associatedRegion->getAttribute('name'));
    }

    public function testSetRawReference(): void
    {
        $this->clearCacheAndRepository();

        // Setup the country
        $country = $this->createBelgium();
        $country->setAttribute('region', ['$ref' => 'blep.json']);
        $this->repository->save($country);

        $this->expectException(ModelNotFoundException::class);
        $associatedRegion = $country->region()->get();
    }

    public function testCanDissociate(): void
    {
        $this->clearCacheAndRepository();

        // Setup the country
        $country = $this->createBelgium();
        $region = $this->createEurope();
        $country->region()->associate($region);

        $associatedRegion = $country->region()->get();
        $this->assertEquals($region, $associatedRegion);
        
        $country->region()->dissociate();

        $this->expectException(NoModelReferenceException::class);
        $associatedRegion = $country->region()->get();
    }
}
