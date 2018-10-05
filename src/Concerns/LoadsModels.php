<?php

namespace WesleyE\JsonModel\Concerns;

use WesleyE\JsonModel\Exceptions\ModelNotFoundException;
use WesleyE\JsonModel\JsonModel;

trait LoadsModels
{
    
    /**
     * Loaded full file paths for easy lookups. Paths map to
     * an array with pathIndex = [type, uuid].
     *
     * @var array
     */
    protected $loadedPaths = [];

    /**
     * Path of the repository
     */
    protected $repoPath = '';

    /**
     * Class path to the Models Directory
     */
    protected $repoClassPath = '';

    /**
     * Get the full path to the .json files on disk.
     *
     * @return string
     */
    public function getRepositoryPath() : string
    {
        return $this->repoPath;
    }

    /**
     * Load a single file and return the model.
     *
     * @param  string $file
     * @return JsonModel
     */
    public function loadModel(string $file) : JsonModel
    {
        // Check for path and return if already loaded
        if (array_key_exists($file, $this->loadedPaths)) {
            // Load the correct file
            return $this->loadedPaths[$file];
        }

        try {
            // Check if it is a 'realpath'
            $realPath = $file;
            if (!$this->isAbsolutePath($file)) {
                $realPath = realpath($this->repoPath . $file);
            }
            
            $json = json_decode(file_get_contents($realPath), true);
            
            // Grab type and deserialize
            $class = $this->repoClassPath.$json['type'];
            $id = $json['id'];

            $model = new $class($this);
            $model->deserialize($json);
        } catch (\Exception $e) {
            throw new ModelNotFoundException('Could not load model.', 100, $e);
        }

        // Add to the repository
        $this->loadedModels[$json['type']][$id] = $model;

        $this->loadedPaths[$file] = $model;

        return $model;
    }

    /**
     * Loads all models found in a subdirectory matching the 'type'
     *
     * @param string $type
     * @return Model[]
     */
    public function loadAllModelsByType(string $type) : array
    {
        $files = array_merge(glob($this->repoPath . $type . '/*.json'));

        $models = [];
        foreach ($files as $file) {
            if (is_file($file)) {
                $models[] = $this->loadModel($file);
            }
        }

        return $models;
    }

    /**
     * Returns true if the path is absolute
     *
     * @param string $path
     * @return boolean
     */
    protected function isAbsolutePath(string $path) : bool
    {
        if ($path === null || $path === '') {
            throw new Exception("Empty path");
        }
        return $path[0] === DIRECTORY_SEPARATOR || preg_match('~\A[A-Z]:(?![^/\\\\])~i', $path) > 0;
    }
}
