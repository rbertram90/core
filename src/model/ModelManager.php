<?php
namespace rbwebdesigns\core\model;

use rbwebdesigns\core\Database;

class ModelManager
{
    protected $models;
    protected static $instances = [];
    protected $databaseConnection;
    protected $name;

    /**
     * ModelManager constructor.
     */
    protected function __construct()
    {
        // Default database abstraction class to rbwebdesigns\core\Database
        // though can be overwritten via setDatabaseClass()
        $this->databaseConnection = new Database();

        // Storage for the list of models under this database
        $this->models = [];
    }

    /**
     * Get the ModelManager instance (Multiton pattern) for this database
     * 
     * @param string $databaseName
     * 
     * @return \rbwebdesigns\core\model\ModelManager
     */
    public static function getInstance($databaseName)
    {
        if(array_key_exists($databaseName, self::$instances)) {
            return self::$instances[$databaseName];
        }
        else {
            self::$instances[$databaseName] = new ModelManager();
            self::$instances[$databaseName]->name = $databaseName;
            return self::$instances[$databaseName];
        }
    }

    /**
     * Get the class instance for a model, instantiates if needed
     * 
     * @return object
     */
    public function get($model)
    {
        if(!array_key_exists($model, $this->models)) {

            if(!class_exists($model)) {
                // todo: throw exception
                return false;
            }

            $this->models[$model] = new $model($this);
        }
        return $this->models[$model];
    }

    /**
     * Get the database class
     * 
     * @return \rbwebdesigns\core\Database|object
     */
    public function getDatabaseConnection()
    {
        return $this->databaseConnection;
    }

    /**
     * Set the database
     */
    public function setDatabaseClass($class)
    {
        $this->databaseConnection = $class;
    }

}