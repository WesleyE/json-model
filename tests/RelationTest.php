<?php

namespace WesleyE\JsonModel\Test;

use PHPUnit\Framework\TestCase;
use WesleyE\JsonModel\Test\TestModels\Country;
use WesleyE\JsonModel\Test\TestModels\Region;
use WesleyE\JsonModel\Repository;

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
        $this->expectException(\Exception::class);
        $country->region()->associate($region);
    }

    public function testCanCreateCountryWithRegion(): void
    {
        $this->clearCacheAndRepository();

        // Setup the region and save it
        $region = Region::new($this->repository);
        $region->name = 'Europe';
        $this->repository->save($region);
        
        // Setup the country
        $country = Country::new($this->repository);
        $country->alpha_2 = 'NL';
        $country->name = 'The Netherlands';
        $this->repository->save($country);

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
        // country 1, country 2, region 1. Update region 1 'from' country 1, see when we grab
        // region from country 2, the attributes are properly set.
        $this->clearCacheAndRepository();

        // Setup the region and save it
        $region = Region::new($this->repository);
        $region->name = 'Europe';
        $this->repository->save($region);
        
        // Setup the country
        $country = Country::new($this->repository);
        $country->alpha_2 = 'NL';
        $country->name = 'The Netherlands';
        $country->region()->associate($region);
        $this->repository->save($country);

        // Setup the country
        $country2 = Country::new($this->repository);
        $country2->alpha_2 = 'BE';
        $country2->name = 'Belgium';
        $country2->region()->associate($region);
        $this->repository->save($country2);

        $associatedRegion = $country->region()->get();
        $associatedRegion->name = 'Europe_2';
        $this->repository->save($associatedRegion);

        $associatedRegion = $country2->region()->get();
        
        $this->assertEquals('Europe_2', $associatedRegion->getAttribute('name'));
    }
}
