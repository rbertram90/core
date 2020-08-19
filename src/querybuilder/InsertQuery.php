<?php

namespace rbwebdesigns\core\querybuilder;

/**
 * Insert database query
 */
class InsertQuery {

    protected $table;
    protected $values = [];
    protected $database;

    /**
     * @param rbwebdesigns\core\querybuilder\Database $database
     * @param string $table
     *  Name of the table
     */
    public function __construct(Database $database, string $table) {
        $this->table = $table;
        $this->database = $database;
    }

    /**
     * Specify the values that should be inserted into the database
     * 
     * @param string[] $values
     *  Multi-dimentional array keyed with field names, values are values to insert
     * 
     * @return self
     */
    public function values(array $values) {
        $this->values = $values;
        return $this;
    }

    /**
     * @param string $value
     * 
     * @return self
     */
    public function addValue(array $value) {
        $this->values[] = $value;
        return $this;
    }

    /**
     * @return boolean
     *  Was the query successful?
     */
    public function execute() {
        if (is_array($this->values) && count($this->values) > 0) {
            $keys = implode(', ', array_keys($this->values));
            $values = array_values($this->values);

            $placeholders = [];
            for ($i = 0; $i < count($values); $i++) {
                $placeholders[] = '?';
            }
            $placeholders = implode(', ', $placeholders);
        }
        
        $sql = "INSERT INTO {$this->table} ({$keys}) VALUES ({$placeholders})";
        
        return $this->database->prepare($sql)->execute($values);
    }

}
