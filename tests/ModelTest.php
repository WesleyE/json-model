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
}
