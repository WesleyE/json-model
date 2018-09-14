<?php

namespace WesleyE\JsonModel\Exceptions;

class ModelNotFoundException extends \Exception
{
    protected $message = 'Could not find the model.';
}
