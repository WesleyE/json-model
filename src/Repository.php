<?php

namespace WesleyE\JsonModel;

use WesleyE\JsonModel\Concerns\LoadsModels;
use WesleyE\JsonModel\Concerns\FindsModels;

/**
 * JSON Model Repository
 *
 */
class Repository
{
    use LoadsModels, FindsModels;

    /**
     * All loaded models
     *
     * @var array
     */
    protected $loadedModels = [];

    /**
     * Creates a new repository
     *
     * @param string $repoPath Full path to the repository, with trailing slash
     * @param string $repoClassPath Full Class path to the models
     */
    public function __construct($repoPath, $repoClassPath)
    {
        $this->repoPath = $repoPath;
        $this->repoClassPath = $repoClassPath;
    }

    /**
     * Get the base Class Path to find the models.
     *
     * @return string
     */
    public function getRepositoryClassPath()
    {
        return $this->repoClassPath;
    }

    /**
     * Clear all loaded models
     *
     * @return void
     */
    public function clearModelCache()
    {
        $this->loadedModels = [];
    }

    /**
     * Saves the JSON Model
     *
     * @return void
     */
    public function save(JsonModel $model)
    {
        $json = json_encode($model, JSON_PRETTY_PRINT);
        $path = $model->getFullFilePath();

        file_put_contents($path, $json);

        $this->loadedModels[$model->type][$model->id] = $model;
        $this->loadedPaths[$model->getRelativeFilePath()] = $model;

        // Reset dirty
        $model->setDirty(false);
    }

    /**
     * Commit all loaded models to disk.
     *
     * @return void
     */
    public function commitToDisk()
    {
        foreach ($this->loadedModels as $modelTypes) {
            foreach ($modelTypes as $model) {
                $this->save($model);
            }
        }
    }
}
