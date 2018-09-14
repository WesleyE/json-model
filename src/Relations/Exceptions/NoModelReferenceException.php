<?php

namespace WesleyE\JsonModel\Relations\Exceptions;

class NoModelReferenceException extends \Exception
{
    protected $message = 'Relation has no $ref.';
}
