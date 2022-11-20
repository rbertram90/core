<?php

namespace rbwebdesigns\core\querybuilder;

class UpdateQuery {

    protected $table;
    protected $values = [];
    protected $conditions = [];
    protected $database;

    /**
     * @param rbwebdesigns\core\querybuilder\Database $database
     * @param string $table
     *   Name of the table
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
    public function addValue(string $name, array $value) {
        $this->values[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $operator
     * 
     * @return self
     */
    public function condition(string $name, string $value, string $operator = QueryCondition::EQUALS_OPERATOR) {
        $condition = new QueryCondition();
        $condition->name = $name;
        $condition->value = $value;
        $condition->operator = $operator;

        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * Execute the select query
     * 
     * @return boolean  Was the query successful?
     */
    public function execute() {        
        $sql = "UPDATE {$this->table} SET";

        if (is_array($this->values) && count($this->values) > 0) {
            $values = array_values($this->values);
            $fieldMapping = [];
            foreach (array_keys($this->values) as $key) {
                $fieldMapping[] = '`' . $key . '` = ?';
            }
            $sql .= implode(', ', $fieldMapping);
        }
        else {
            return false;
        }

        $where = [];
        foreach ($this->conditions as $condition) {
            $where[] = strval($condition);
        }

        if (count($where) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        
        return $this->database->prepare($sql)->execute($values);
    }


}