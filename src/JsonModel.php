<?php

namespace WesleyE\JsonModel;

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

    protected $methods;

    /**
     * Create a new model
     *
     * @param string $newId
     */
    public function __construct($newId = null)
    {
        // Generate a new model
        if ($newId !== null) {
            // @todo, move the id and type default attributes here.
            // @todo, check if the model exists
            $this->attributes = $this->defaultAttributes;
            $this->setAttribute('id', $newId);
        }

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
        return Repository::REPO_PATH . $this->getModelDirectory() . '/' . $this->getFilename();
    }

    /**
     * Get the relative path to the repository.
     *
     * @return string
     */
    public function getRelativeFilePath()
    {
        return  $this->getModelDirectory() . '/' . $this->getFilename();
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
        if (!in_array($name, array_keys($this->defaultAttributes))) {
            throw new \Exception(self::class . ' has no attribute ' . $name);
        }
        
        // todo: check for mutators?
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
        if (!array_key_exists($name, $this->defaultAttributes)) {
            throw new \Exception('Attribute '.$name.' does not exist');
        }

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

    public function save()
    {
        $json = json_encode($this, JSON_PRETTY_PRINT);
        
        file_put_contents($this->getFullFilePath(), $json);
    }

    public static function new()
    {
        $class = get_called_class();
        return new $class(self::generateUuid());
    }

    /**
     * Get a UUIDv4
     *
     * @todo, move to the ramsey package.
     *
     * @return string
     */
    public static function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
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

        $repo = Repository::getInstance();
        return $repo->loadModel($fullPath);
    }
}
