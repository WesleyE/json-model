<?php

namespace WesleyE\JsonModel\Test;

use PHPUnit\Framework\TestCase;
use WesleyE\JsonModel\Test\TestModels\Country;
use WesleyE\JsonModel\Repository;

final class ModelTest extends BaseTest
{
    public function testCanLoadModelFromDist(): void
    {
        $instance = Country::new($this->repository);
        $instance->name = 'Germany';
        $instance->alpha_2 = 'DE';
        $this->repository->save($instance);

        $this->repository->clearModelCache();

        $germany = $this->repository->loadModel('countries/DE.json');

        $this->assertEquals('Germany', $germany->name);
    }

    public function testCanCommitToDisk(): void
    {
        $this->clearCacheAndRepository();

        $instance = Country::new($this->repository);
        $instance->name = 'Germany';
        $instance->alpha_2 = 'DE';
        $this->repository->save($instance);

        clearRepository();
        $this->repository->commitToDisk();
        $this->assertFileExists($this->repository->getRepositoryPath() . 'countries/DE.json');
    }

    public function testCanCreateNewModelInstance(): void
    {
        $instance = Country::new($this->repository);
        $this->assertInstanceOf(Country::class, $instance);
    }

    public function testCanSetAttribute(): void
    {
        $instance = Country::new($this->repository);
        $instance->name = 'The Netherlands';
        $this->assertEquals('The Netherlands', $instance->name);

        // Check for raw attributes
        $attributes = $instance->getAttributes();
        $this->assertEquals('The Netherlands', $attributes['name']);
    }

    public function testCanSaveAModel(): void
    {
        $instance = Country::new($this->repository);
        $instance->name = 'The Netherlands';
        $instance->alpha_2 = 'NL';
        $this->repository->save($instance);

        // Load the file
        $contents = file_get_contents($instance->getFullFilePath());
        $this->assertEquals('{
    "id": "' . $instance->id . '",
    "type": "Country",
    "name": "The Netherlands",
    "alpha_2": "NL",
    "region": {
        "$ref": null
    }
}', $contents);
    }

    public function testCanGetSetAttributes(): void
    {
        $instance = Country::new($this->repository);
        $instance->name = 'The Netherlands';
        $instance->setAttribute('alpha_2', 'NL');

        $attributes = $instance->getAttributes();

        $defaultAttributes = [
            'id' => $instance->id,
            'type' => 'Country',
            'name' => 'The Netherlands',
            'alpha_2' => 'NL',
            'region' => [
                '$ref' => null,
            ]
        ];

        $this->assertEquals($defaultAttributes, $attributes);
    }

    public function testThrowsWhenAccessingUnknownAttribute(): void
    {
        $this->expectException(\Exception::class);
        $instance = Country::new($this->repository);
        $instance->test;
    }

    public function testThrowsWhenAccessingUnknownAttributeByFunction(): void
    {
        $this->expectException(\Exception::class);
        $instance = Country::new($this->repository);
        $instance->getAttribute('test');
    }

    public function testThrowsWhenSettingUnknownAttributeByFunction(): void
    {
        $this->expectException(\Exception::class);
        $instance = Country::new($this->repository);
        $instance->setAttribute('test', 1);
    }

    public function testThrowsWhenSettingUnknownAttribute(): void
    {
        $this->expectException(\Exception::class);
        $instance = Country::new($this->repository);
        $instance->test = 1;
    }

    public function testCanCheckForDirty(): void
    {
        $this->clearCacheAndRepository();

        $instance = Country::new($this->repository);
        $instance->name = 'Germany';
        $instance->alpha_2 = 'DE';
        $this->repository->save($instance);

        $this->assertFalse($instance->isDirty());

        $instance->alpha_2 = 'DEE';
        $this->assertTrue($instance->isDirty());
    }

    public function testCanCheckForSaved(): void
    {
        $this->clearCacheAndRepository();

        $instance = Country::new($this->repository);
        $instance->name = 'Germany';
        $instance->alpha_2 = 'DE';
        $this->assertFalse($instance->isSaved());

        $this->repository->save($instance);
        $this->assertTrue($instance->isSaved());
    }
}
