<?php

namespace WesleyE\JsonModel\Relations;

use WesleyE\JsonModel\Relations\Exceptions\NoModelReferenceException;
use WesleyE\JsonModel\Exceptions\ModelNotFoundException;

class BaseRelation
{
    protected $referenceAttribute;

    protected $child;

    public function __construct($child, $referenceAttribute)
    {
        $this->child = $child;
        $this->referenceAttribute = $referenceAttribute;
    }

    /**
     * Load and return the referenced model relative to this file.
     *
     * @param  string $ref
     * @return JsonModel
     */
    protected function getReferencedModel($ref)
    {
        $repo = $this->child->getRepository();
        return $repo->loadModel($ref);
    }
}
