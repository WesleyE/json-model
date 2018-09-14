<?php

namespace WesleyE\JsonModel;

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
            throw new \Exception('Relation has no ref.');
        }

        echo "-------\n";
        echo $childAttributes[$this->referenceAttribute]['$ref'];
        echo "-------\n";

        return $this->getReferencedModel($childAttributes[$this->referenceAttribute]['$ref']);
    }

    /**
     * Associate the model.
     *
     * @return void
     */
    public function associate($model)
    {
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
        $repo = Repository::getInstance();
        return $repo->loadModel($ref);
    }
}
