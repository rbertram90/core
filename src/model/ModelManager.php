<?php
namespace rbwebdesigns\core\model;

class ModelManager
{
    protected $models;
    protected static $instance = null;

    private function __construct()
    {
        $this->models = [];
    }

    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new ModelManager();
        }
        return self::$instance;
    }

    public function add($key, $model) {
        if(!array_key_exists($key, $this->models)) {
            $this->models[$key] = $model;
            return true;
        }
        return false;
    }

    public function get($key) {
        if(array_key_exists($key, $this->models)) {
            return $this->models[$key];
        }
        return false;
    }
}