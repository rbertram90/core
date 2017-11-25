<?php
namespace rbwebdesigns\core\model;

use rbwebdesigns\core\Database;

class ModelManager
{
    protected $models;
    protected static $instances = [];

    protected $name;

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

    public function add($model) {
        if(!array_key_exists($model, $this->models)) {
            $this->models[$model] = new $model($this);
            return true;
        }
        return false;
    }

    public function get($model) {
        if(array_key_exists($model, $this->models)) {
            return $this->models[$model];
        }
        return false;
    }

    /**
     * @return rbwebdesigns/core/database
     */
    public function getDatabaseConnection() {
        return $this->databaseConnection;
    }
}