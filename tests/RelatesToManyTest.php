<?php

namespace WesleyE\JsonModel\Test;

use PHPUnit\Framework\TestCase;
use WesleyE\JsonModel\Test\TestModels\Country;
use WesleyE\JsonModel\Test\TestModels\Region;
use WesleyE\JsonModel\Repository;
use WesleyE\JsonModel\Exceptions\ModelNotFoundException;
use WesleyE\JsonModel\Relations\Exceptions\NoModelReferenceException;

final class RelatesToManyTest extends BaseTest
{
    public function testCanAttachMultipleRelations(): void
    {
        $this->clearCacheAndRepository();

        $netherlands = $this->createNetherlands();
        $belgium = $this->createBelgium();
        $europe = $this->createEurope();

        $europe->countries()->attach($netherlands);
        $europe->countries()->attach($belgium);
        
        $this->assertEquals([
            $netherlands,
            $belgium
        ], $europe->countries()->get());
    }

    public function testDetachRelation(): void
    {
        $this->clearCacheAndRepository();

        $netherlands = $this->createNetherlands();
        $belgium = $this->createBelgium();
        $europe = $this->createEurope();

        $europe->countries()->attach($netherlands);
        $europe->countries()->attach($belgium);

        $europe->countries()->detach($netherlands);

        $this->assertEquals([
            $belgium
        ], $europe->countries()->get());
    }

    public function testAttachShouldUpdateReverse(): void
    {
        $this->clearCacheAndRepository();

        $netherlands = $this->createNetherlands();
        $belgium = $this->createBelgium();
        $europe = $this->createEurope();

        $europe->countries()->attach($netherlands);
        $europe->countries()->attach($belgium);

        $this->assertEquals($europe, $belgium->region()->get());
    }

    public function testDetachShouldUpdateReverse(): void
    {
        $this->clearCacheAndRepository();

        $netherlands = $this->createNetherlands();
        $belgium = $this->createBelgium();
        $europe = $this->createEurope();

        $europe->countries()->attach($netherlands);
        $europe->countries()->attach($belgium);

        $europe->countries()->detach($belgium);

        $this->expectException(NoModelReferenceException::class);
        $belgiumsEurope = $belgium->region()->get();
    }
}
