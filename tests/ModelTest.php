<?php

namespace WesleyE\JsonModel\Test;

use PHPUnit\Framework\TestCase;
use WesleyE\JsonModel\Test\TestModels\Country;

final class ModelTest extends TestCase
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
}
