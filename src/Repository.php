<?php

namespace WesleyE\JsonModel;

/**
 * JSON Model Repository
 *
 */
class Repository
{

    /**
     * Path of the repository
     */
    protected $repoPath = '';

    /**
     * Class path to the Models Directory
     */
    protected $repoClassPath = '';

    /**
     * All loaded models
     *
     * @var array
     */
    protected $loadedModels = [];

    /**
     * Loaded full file paths for easy lookups. Paths map to
     * an array with pathIndex = [type, uuid].
     *
     * @var array
     */
    protected $loadedPaths = [];

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
     * Get the full path to the .json files on disk.
     *
     * @return string
     */
    public function getRepositoryPath()
    {
        return $this->repoPath;
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
     * Load a single file and return the model.
     *
     * @param  string $file
     * @return JsonModel
     */
    public function loadModel(string $file)
    {
        // echo "\n\tLoading: " . $file . "\n";
        // Check for path and return if already loaded
        if (array_key_exists($file, $this->loadedPaths)) {
            // echo "\n\t\tFrom Cache\n";
            // Load the correct file
            return $this->loadedPaths[$file];
        }

        try {
            // Load JSON
            $realPath = realpath($this->repoPath . $file);
            $json = json_decode(file_get_contents($realPath), true);
            
            // Grab type and deserialize
            $class = $this->repoClassPath.$json['type'];
            $id = $json['id'];

            $model = new $class($this);
            $model->deserialize($json);
        } catch (\Exception $e) {
            throw new \Exception('Could not load model.', 100, $e);
        }

        // Add to the repository
        $this->loadedModels[$json['type']][$id] = $model;

        $this->loadedPaths[$file] = $model;

        return $model;
    }

    /**
     * Search for a model by type and ID
     *
     * @param  string $type
     * @param  mixed  $id
     * @return JsonModel
     */
    public function getModelByTypeAndId(string $type, $id)
    {
        // @todo: test if exists
        return $this->loadedModels[$type][$id];
    }

    /**
     * Search for a model by type and attribute value
     *
     * @param  string $type
     * @param  string $attribute
     * @param  mixed  $value
     * @return JsonModel
     */
    public function getModelByTypeAndAttribute(string $type, $attribute, $value)
    {
        // @todo: test if type exists in the array
        foreach ($this->loadedModels[$type] as $model) {
            if ($model[$attribute] === $value) {
                return $model;
            }
        }
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

        if (empty($path)) {
            throw new \Exception('Cannot save the model to an empty path.');
        }

        file_put_contents($path, $json);

        $this->loadedModels[$model->type][$model->id] = $model;
        $this->loadedPaths[$path] = $model;
    }
}
