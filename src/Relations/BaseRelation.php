<?php

namespace WesleyE\JsonModel\Relations;

use WesleyE\JsonModel\Relations\Exceptions\NoModelReferenceException;
use WesleyE\JsonModel\Exceptions\ModelNotFoundException;
use WesleyE\JsonModel\JsonModel;

class BaseRelation
{
    protected $referenceAttribute;

    protected $child;

    protected $inverseModelAttribute;

    /**
     * Creates a new relation.
     *
     * @param JsonModel $child
     * @param string $referenceAttribute
     * @param string $inverseModelClass
     * @param string $inverseModelAttribute
     */
    public function __construct($child, $referenceAttribute, $inverseModelAttribute = null)
    {
        $this->child = $child;
        $this->referenceAttribute = $referenceAttribute;

        $this->inverseModelAttribute = $inverseModelAttribute;
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

    /**
     * Resolve and add the model to the inverse
     * of the relation.
     *
     * @return void
     */
    protected function addInverse(JsonModel $model)
    {
        if ($this->inverseModelAttribute === null) {
            return;
        }

        // Grab the model
        $relation = $model->{$this->inverseModelAttribute}();

        if (get_class($relation) === RelatesToMany::class) {
            $relation->attach($this->child, false);
        } elseif (get_class($relation) === RelatesTo::class) {
            $relation->associate($this->child, false);
        } else {
            throw new \Exception('Not a valid relation to add inverse relation to.');
        }
    }

    /**
     * Resolve and add the model to the inverse
     * of the relation.
     *
     * @return void
     */
    protected function removeInverse(JsonModel $model)
    {
        if ($this->inverseModelAttribute === null) {
            return;
        }

        // Grab the model
        $relation = $model->{$this->inverseModelAttribute}();

        if (get_class($relation) === RelatesToMany::class) {
            $relation->detach($this->child, false);
        } elseif (get_class($relation) === RelatesTo::class) {
            $relation->dissociate($this->child, false);
        } else {
            throw new \Exception('Not a valid relation to remove inverse relation from.');
        }
    }
}
