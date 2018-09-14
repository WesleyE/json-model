<?php

namespace WesleyE\JsonModel\Test;

use PHPUnit\Framework\TestCase;
use WesleyE\JsonModel\Test\TestModels\Country;
use WesleyE\JsonModel\Repository;

final class ModelTest extends BaseTest
{
    public function testCanCreateNewModelInstance(): void
    {
        $instance = Country::new();
        $this->assertInstanceOf(Country::class, $instance);
    }

    public function testCanSetAttribute(): void
    {
        $instance = Country::new();
        $instance->name = 'The Netherlands';
        $this->assertEquals('The Netherlands', $instance->name);

        // Check for raw attributes
        $attributes = $instance->getAttributes();
        $this->assertEquals('The Netherlands', $attributes['name']);
    }

    public function testCanSaveAModel(): void
    {
        $instance = Country::new();
        $instance->name = 'The Netherlands';
        $instance->alpha_2 = 'NL';
        $instance->save();

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
        $instance = Country::new();
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
        $instance = Country::new();
        $instance->test;
    }

    public function testThrowsWhenAccessingUnknownAttributeByFunction(): void
    {
        $this->expectException(\Exception::class);
        $instance = Country::new();
        $instance->getAttribute('test');
    }

    public function testThrowsWhenSettingUnknownAttributeByFunction(): void
    {
        $this->expectException(\Exception::class);
        $instance = Country::new();
        $instance->setAttribute('test', 1);
    }

    public function testThrowsWhenSettingUnknownAttribute(): void
    {
        $this->expectException(\Exception::class);
        $instance = Country::new();
        $instance->test = 1;
    }
}
