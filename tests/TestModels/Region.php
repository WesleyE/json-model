<?php

namespace WesleyE\JsonModel\Test\TestModels;

use WesleyE\JsonModel\JsonModel;
use WesleyE\JsonModel\Relations\RelatesToMany;

class Region extends JsonModel
{
    protected static $modelDirectory = 'regions';
    
    protected $defaultAttributes = [
        'id' => null,
        'type' => 'Region',
        'name' => '',
        'countries' => []
    ];

    public function getFilename()
    {
        return $this->attributes['name'] . '.json';
    }

    public function countries()
    {
        return new RelatesToMany($this, 'countries', 'region');
    }
}
