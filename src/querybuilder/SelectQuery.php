<?php

namespace rbwebdesigns\core\querybuilder;

class SelectQuery {

    protected $table;
    protected $fields = [];
    protected $conditions = [];
    protected $database;
    protected $orderByField;
    protected $orderByDirection = 'ASC';
    protected $limit = false;
    protected $offset = false;

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
     * @param string[] $fields
     * 
     * @return self
     */
    public function fields(array $fields) {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function addField(string $field) {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $operator
     * 
     * @return self
     */
    public function condition(string $name, $value, string $operator = QueryCondition::EQUALS_OPERATOR) {
        $condition = new QueryCondition();
        $condition->name = $name;
        $condition->value = $value;
        $condition->operator = $operator;

        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * @return self
     */
    public function orderBy(string $field, string $direction='ASC') {
        $this->orderByField = $field;
        $this->orderByDirection = $direction == 'ASC' ? 'ASC' : 'DESC';
        return $this;
    }

    /**
     * @return self
     */
    public function offset(int $offset) {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return self
     */
    public function limit(int $limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Execute the select query
     * 
     * @return \PDOStatement
     */
    public function execute() {
        if (is_array($this->fields) && count($this->fields) > 0) {
            $fields = implode(',', $this->fields);
        }
        else {
            $fields = '*';
        }
        
        $sql = "SELECT {$fields} FROM {$this->table}";

        $where = [];
        foreach ($this->conditions as $condition) {
            $where[] = strval($condition);
        }

        if (count($where) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        if (strlen($this->orderByField)) {
            $sql .= ' ORDER BY ' . $this->orderByField . ' ' . $this->orderByDirection;
        }

        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $this->database->query($sql);
    }


}