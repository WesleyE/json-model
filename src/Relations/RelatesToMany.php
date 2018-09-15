<?php

namespace WesleyE\JsonModel\Relations;

use WesleyE\JsonModel\JsonModel;

class RelatesToMany extends BaseRelation
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
    public function attach($model, $updateInverse = true)
    {
        $childAttributes = $this->child->getAttributes();
        $references = $childAttributes[$this->referenceAttribute];

        $references[] = $model->getRelativeFilePath();
        $this->child->setAttribute($this->referenceAttribute, $references);

        if ($updateInverse) {
            $this->addInverse($model);
        }
    }

    /**
     * Dissociate the model.
     *
     * @return void
     */
    public function detach(JsonModel $model, $updateInverse = true)
    {
        $childAttributes = $this->child->getAttributes();
        $references = $childAttributes[$this->referenceAttribute];

        // Rebuild all the references
        $relatingModels = [];
        foreach ($references as $modelReference) {
            if ($modelReference !== $model->getRelativeFilePath()) {
                $relatingModels[] = $modelReference;
            }
        }

        $this->child->setAttribute($this->referenceAttribute, $relatingModels);

        if ($updateInverse) {
            $this->removeInverse($model);
        }
    }
}
