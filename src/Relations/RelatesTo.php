<?php

namespace WesleyE\JsonModel\Relations;

use WesleyE\JsonModel\Relations\Exceptions\NoModelReferenceException;
use WesleyE\JsonModel\Exceptions\ModelNotFoundException;

class RelatesTo
{
    protected $child;
    protected $referenceAttribute;

    public function __construct($child, $referenceAttribute)
    {
        $this->child = $child;
        $this->referenceAttribute = $referenceAttribute;
    }

    /**
     * Grabs the related model and returns it.
     *
     * @return JsonModel
     */
    public function get()
    {
        // Check if the relation has a ref
        $childAttributes = $this->child->getAttributes();

        if (!array_key_exists('$ref', $childAttributes[$this->referenceAttribute])) {
            throw new NoModelReferenceException();
        }

        return $this->getReferencedModel($childAttributes[$this->referenceAttribute]['$ref']);
    }

    /**
     * Associate the model.
     *
     * @return void
     */
    public function associate($model)
    {
        if (!$model->isSaved()) {
            throw new ModelNotFoundException('Cannot associate unsaved models.');
        }
        $this->child->setAttribute($this->referenceAttribute, ['$ref' => $model->getRelativeFilePath()]);
    }

    /**
     * Dissociate the model.
     *
     * @return void
     */
    public function dissociate()
    {
        $this->child->setAttribute($this->referenceAttribute, ['$ref' => null]);
    }

    /**
     * Load and return the referenced model relative to this file.
     *
     * @param  string $ref
     * @return JsonModel
     */
    protected function getReferencedModel($ref)
    {
        // echo "\n\tLoading ReferencedModel: " . $ref . "\n";
        $repo = $this->child->getRepository();
        return $repo->loadModel($ref);
    }
}
