<?php

namespace WesleyE\JsonModel\Test\TestModels;

use WesleyE\JsonModel\JsonModel;
use WesleyE\JsonModel\Relations\RelatesTo;

class Country extends JsonModel
{
    protected $modelDirectory = 'countries';
    
    protected $defaultAttributes = [
        'id' => null,
        'type' => 'Country',
        'name' => '',
        'alpha_2' => '',
        'region' => [
            '$ref' => null,
        ]
    ];

    public function getFilename()
    {
        return $this->attributes['alpha_2'] . '.json';
    }

    public function region()
    {
        return new RelatesTo($this, 'region', 'countries');
    }
}
