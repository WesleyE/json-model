<?php

namespace WesleyE\JsonModel\Relations;

use WesleyE\JsonModel\Relations\Exceptions\NoModelReferenceException;
use WesleyE\JsonModel\Exceptions\ModelNotFoundException;

class RelatesTo extends BaseRelation
{
    /**
     * Grabs the related model and returns it.
     *
     * @return JsonModel
     */
    public function get()
    {
        // Check if the relation has a ref
        $childAttributes = $this->child->getAttributes();

        if (!array_key_exists('$ref', $childAttributes[$this->referenceAttribute])
            || empty($childAttributes[$this->referenceAttribute]['$ref'])) {
            throw new NoModelReferenceException();
        }

        return $this->getReferencedModel($childAttributes[$this->referenceAttribute]['$ref']);
    }

    /**
     * Associate the model.
     *
     * @return void
     */
    public function associate($model, $updateInverse = true)
    {
        if (!$model->isSaved()) {
            throw new ModelNotFoundException('Cannot associate unsaved models.');
        }

        $this->child->setAttribute($this->referenceAttribute, ['$ref' => $model->getRelativeFilePath()]);

        if ($updateInverse) {
            $this->addInverse($model);
        }
    }

    /**
     * Dissociate the model.
     *
     * @return void
     */
    public function dissociate($updateInverse = true)
    {
        // Do this first, since we do not nessesarily need the model.
        if ($updateInverse) {
            $this->removeInverse($this->get());
        }

        $this->child->setAttribute($this->referenceAttribute, ['$ref' => null]);
    }
}
