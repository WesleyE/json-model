<?php

namespace WesleyE\JsonModel;

class RelatesToMany
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
        $references = $childAttributes[$this->referenceAttribute];

        $relatingModels = [];
        foreach ($references as $modelReference) {
            $relatingModels[] = $this->getReferencedModel($modelReference);
        }

        return $relatingModels;
    }

    /**
     * Associate the model.
     *
     * @return void
     */
    public function attach($model)
    {
        $childAttributes = $this->child->getAttributes();
        $references = $childAttributes[$this->referenceAttribute];

        $references[] = $model->getRelativeFilePath();
        $this->child->setAttribute($this->referenceAttribute, $references);
    }

    /**
     * Dissociate the model.
     *
     * @return void
     */
    public function detach()
    {
        $childAttributes = $this->child->getAttributes();
        $references = $childAttributes[$this->referenceAttribute];

        $modelPath = $this->child->getRelativeFilePath();
        $relatingModels = [];
        foreach ($references as $modelReference) {
            if ($modelReference !== $modelPath) {
                $relatingModels[] = $this->getReferencedModel($modelReference);
            } else {
                echo 1111;
            }
        }

        $this->child->setAttribute($this->referenceAttribute, $relatingModels);
    }

    /**
     * Load and return the referenced model relative to this file.
     *
     * @param  string $ref
     * @return JsonModel
     */
    protected function getReferencedModel($ref)
    {
        $repo = Repository::getInstance();
        return $repo->loadModel($ref);
    }
}
