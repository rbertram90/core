<?php
namespace rbwebdesigns\core\model;

use rbwebdesigns\core\Database;

class ModelManager
{
    protected $name;
    protected $models;
    protected static $instances = [];

    private function __construct()
    {
        $this->databaseConnection = new Database();
        $this->models = [];
    }

    public static function getInstance($databaseName) {
        if(array_key_exists($databaseName, self::$instances)) {
            return self::$instances[$databaseName];
        }
        else {
            self::$instances[$databaseName] = new ModelManager();
            self::$instances[$databaseName]->name = $databaseName;
            return self::$instances[$databaseName];
        }
    }

    public function get($model) {
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
     * @return rbwebdesigns/core/database
     */
    public function getDatabaseConnection() {
        return $this->databaseConnection;
    }
}