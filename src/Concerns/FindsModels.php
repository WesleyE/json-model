<?php

namespace WesleyE\JsonModel\Concerns;

use WesleyE\JsonModel\JsonModel;
use WesleyE\JsonModel\Exceptions\ModelNotFoundException;

trait FindsModels
{
    /**
     * Search for a model by type and ID
     *
     * @param  string $type
     * @param  mixed  $id
     * @return JsonModel
     */
    public function getModelByTypeAndId(string $type, $id) : JsonModel
    {
        if (!array_key_exists($type, $this->loadedModels)) {
            throw new ModelNotFoundException('No models loaded with type');
        }
        
        return $this->loadedModels[$type][$id];
    }

    /**
     * Get all models by type
     *
     * @param string $type
     * @return array
     */
    public function getAllModelsByType(string $type) : array
    {
        return $this->loadAllModelsByType($type);
    }

    /**
     * Search for a model by type and attribute value
     *
     * @param  string $type
     * @param  string $attribute
     * @param  mixed  $value
     * @return JsonModel
     */
    public function getModelsByTypeAndAttribute(string $type, $attribute, $value, int $limit = 0) : array
    {
        if (!array_key_exists($type, $this->loadedModels)) {
            throw new ModelNotFoundException('No models loaded with type');
        }

        $foundModels = [];
        foreach ($this->loadedModels[$type] as $model) {
            $attr = $model->getAttribute($attribute);

            // Check if the 'attribute' is a relation
            $foundModel = false;
            if ($model->isRelation($attribute)) {
                $relation = $model->getAttribute($attribute);

                // Check if $value is a JsonModel
                if ($value instanceof JsonModel) {
                    if ($relation['$ref'] === $value->getRelativeFilePath()) {
                        $foundModel = true;
                    }
                } else {
                    if ($relation['$ref'] === $value) {
                        $foundModel = true;
                    }
                }
            }

            // If it is a normal attribute
            if ($model->getAttribute($attribute) === $value) {
                $foundModel = true;
            }

            if ($foundModel) {
                $foundModels[] = $model;
            }

            if ($limit > 0 && count($foundModels) > $limit) {
                break;
            }
        }

        return $foundModels;
    }
}
