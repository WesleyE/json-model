<?php

namespace WesleyE\JsonModel;

use Ramsey\Uuid\Uuid;

class JsonModel implements \JsonSerializable
{

    /**
     * Default attributes and their content.
     *
     * @var array
     */
    protected $defaultAttributes = [];

    /**
     * Actual attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Path to store the model .json files in. Start from the
     * repository path. Should not include start and end slashes.
     *
     * @var string
     */
    protected $modelDirectory;

    /**
     * The repository this file is loaded from.
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Methods in this class
     *
     * @var array
     */
    protected $methods;

    /**
     * Tracks if the model has been modified after load.
     *
     * @var bool
     */
    protected $dirty;

    /**
     * Create a new model
     *
     * @param string $newId
     */
    public function __construct(Repository $repository, $newId = null)
    {
        $this->dirty = false;

        // Generate a new model
        if ($newId !== null) {
            // @todo, move the id and type default attributes here.
            // @todo, check if the model exists
            $this->attributes = $this->defaultAttributes;
            $this->setAttribute('id', $newId);
        }

        $this->repository = $repository;

        $this->methods = get_class_methods(get_class($this));
    }

    /**
     * Get the original file path for this model
     *
     * @return string
     */
    public function getModelDirectory()
    {
        return $this->modelDirectory;
    }

    /**
     * Get's the filename
     *
     * @return void
     */
    public function getFilename()
    {
        throw new \Exception("Not implemented.");
    }

    /**
     * Get's the full file path
     *
     * @return string
     */
    public function getFullFilePath()
    {
        $filename = $this->getFilename();
        if (empty($filename) || $filename === ".json") {
            throw new \Exception('Cannot save a model when the filename resolves to an empty string');
        }
        return $this->repository->getRepositoryPath() . $this->getModelDirectory() . '/' . $this->getFilename();
    }

    /**
     * Get the relative path to the repository.
     *
     * @return string
     */
    public function getRelativeFilePath()
    {
        $filename = $this->getFilename();
        if (empty($filename) || $filename === ".json") {
            throw new \Exception('Cannot save a model when the filename resolves to an empty string');
        }
        return  $this->getModelDirectory() . '/' . $this->getFilename();
    }

    /**
     * Grab the associated Repository
     *
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Serialize this into an array
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->attributes;
    }

    /**
     * Deserialize the model
     *
     * @param  array $attributes
     * @return void
     */
    public function deserialize(array $attributes)
    {
        $this->attributes = $attributes;
        $this->dirty = false;
    }

    public function __get(string $name)
    {
        if (!in_array($name, array_keys($this->defaultAttributes))) {
            throw new \Exception(self::class . ' has no attribute ' . $name);
        }

        // We now need to resolve the reference
        if (in_array($name, $this->methods)) {
            $relation = $this->$name();
            return $relation->get();
        }

        // todo: check if there is a method
        return $this->attributes[$name];
    }

    public function __set(string $name, $value)
    {
        $this->setAttribute($name, $value);
        $this->attributes[$name] = $value;
    }

    /**
     * Get all attributes
     *
     * @return void
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets an attribuet
     *
     * @param  string $name
     * @param  $value
     * @return void
     */
    public function setAttribute(string $name, $value)
    {
        // todo: check for mutators?
        if (!array_key_exists($name, $this->defaultAttributes)) {
            throw new \Exception('Attribute '.$name.' does not exist');
        }

        $this->dirty = false;
        $this->attributes[$name] = $value;
    }

    /**
     * Get's an attribute
     *
     * @param string $name
     */
    public function getAttribute(string $name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new \Exception('Attribute does not exist');
        }
        
        return $this->attributes[$name];
    }

    public static function new(Repository $repository)
    {
        $class = get_called_class();
        return new $class($repository, Uuid::uuid4()->toString());
    }

    /**
     * Load and return the referenced model relative to this file.
     *
     * @param  string $ref
     * @return JsonModel
     */
    protected function getReferencedModel($ref)
    {
        // Get path relative to this 'file'
        $fullPath = $this->getModelDirectory() . '/' . $ref;

        return $this->repository->loadModel($fullPath);
    }

    /**
     * Returns true if the model has been modified.
     *
     * @return boolean
     */
    public function isDirty()
    {
        return $this->dirty;
    }

    /**
     * Set to true if the model has been modified.
     *
     * @return boolean
     */
    public function setDirty($dirty)
    {
        return $this->dirty = $dirty;
    }

    /**
     * Returns true if the model has been saved (at least once).
     *
     * @return boolean
     */
    public function isSaved()
    {
        return is_file($this->getFullFilePath());
    }
}
