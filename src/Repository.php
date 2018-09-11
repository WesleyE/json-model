<?php

namespace WesleyE\JsonModel;

/**
 * JSON Model Repository
 *
 * @todo: Move this into a singleton.
 */
final class Repository
{

    /**
     * Path of the repository
     */
    public const REPO_PATH = __DIR__ . '/JsonRepository/';

    /**
     * Class path to the Models Directory
     */
    protected const REPO_CLASS_PATH = '\\App\\JSON\\Models\\';

    /**
     * Singleton Instance
     *
     * @var Repository
     */
    protected static $instance = null;

    /**
     * All loaded models
     *
     * @var array
     */
    protected $loadedModels = [];


    protected function __construct()
    {
    }

    protected function __clone()
    {
        throw new \Exception("No cloning of a singleton.");
    }

    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * Load a single file and return the model.
     *
     * @param  string $file
     * @return JsonModel
     */
    public function loadModel(string $file)
    {
        // @todo: check for path and return if already loaded

        // Load JSON
        $realPath = realpath(self::REPO_PATH . $file);
        $json = json_decode(file_get_contents($realPath), true);
        
        // Grab type and deserialize
        $class = self::REPO_CLASS_PATH.$json['type'];
        $id = $json['id'];

        $model = new $class();
        $model->deserialize($json);

        // Add to the repository
        $this->loadedModels[$json['type']][$id] = $model;

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
}
