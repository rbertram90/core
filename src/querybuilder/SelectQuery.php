<?php

namespace rbwebdesigns\core\querybuilder;

class SelectQuery {

    /** @var string */
    protected $table;

    /** @var string */
    protected $alias = '';

    /** @var string[][] */
    protected $joins = [];

    /** @var string[] */
    protected $fields = [];

    /** @var \rbwebdesigns\core\querybuilder\SelectQuery[] */
    protected $selectSubqueries = [];

    /** @var \rbwebdesigns\core\querybuilder\QueryCondition[] */
    protected $conditions = [];

    /** @var \rbwebdesigns\core\querybuilder\Database */
    protected $database;

    /** @var string */
    protected $orderByField;

    /** @var string */
    protected $orderByDirection = 'ASC';

    /** @var string[] */
    protected $groupBy = [];

    /** @var bool|int */
    protected $limit = false;

    /** @var bool|int */
    protected $offset = false;

    /**
     * @param rbwebdesigns\core\querybuilder\Database $database
     * @param string $table
     *   Name of the table
     */
    public function __construct(Database $database, string $table, string $alias = '') {
        $this->table = $table;
        $this->alias = $alias;
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
     * @param SelectQuery $query
     * 
     * @return self
     */
    public function selectSubquery(SelectQuery $query) {
        $this->selectSubqueries[] = $query;
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
    public function groupBy($field) {
        $this->groupBy[] = $field;
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
     * @return self
     */
    public function join(string $type, string $tableName, string $alias, string $on) {
        if (!isset($this->joins[$type])) $this->joins[$type] = [];

        $this->joins[$type][] = [
            'table' => $tableName,
            'alias' => $alias,
            'on' => $on
        ];

        return $this;
    }

    /**
     * Add a inner join to query, shorthand join function
     * 
     * @return self
     */
    public function innerJoin(string $tableName, string $alias, string $on) {
        return $this->join('inner', $tableName, $alias, $on);
    }

    /**
     * Add a left (inner) join to query, shorthand join function
     * 
     * @return self
     */
    public function leftJoin(string $tableName, string $alias, string $on) {
        return $this->join('left', $tableName, $alias, $on);
    }

    /**
     * Add a right (inner) join to query, shorthand join function
     * 
     * @return self
     */
    public function rightJoin(string $tableName, string $alias, string $on) {
        return $this->join('right', $tableName, $alias, $on);
    }

    /**
     * Add a full (outer) join to query, shorthand join function
     * 
     * @return self
     */
    public function fullJoin(string $tableName, string $alias, string $on) {
        return $this->join('full', $tableName, $alias, $on);
    }

    /**
     * Return the SQL for the query (alias for toString)
     * 
     * @return string
     */
    public function sql() {
        return $this->__toString();
    }

    /**
     * Return the SQL for the query
     * 
     * @return string
     */
    public function __toString() {
        if (is_array($this->fields) && count($this->fields) > 0) {
            $fields = implode(',', $this->fields);
        }
        else {
            $fields = '*';
        }

        foreach ($this->selectSubqueries as $subquery) {
            $fields .= ', (' . $subquery->sql() . ')';
        }
        
        $alias = strlen($this->alias) ? ' AS ' . $this->alias : '';
        $sql = "SELECT {$fields} FROM {$this->table}{$alias}";

        foreach ($this->joins as $joinType => $joins) {
            foreach ($joins as $join) {
                $alias = strlen($join['alias']) ? ' AS ' . $join['alias'] : '';
                switch ($joinType) {
                    case 'left':
                        $sql .= " LEFT JOIN {$join['table']}{$alias} ON {$join['on']}";
                    break;
                    case 'right':
                        $sql .= " RIGHT JOIN {$join['table']}{$alias} ON {$join['on']}";
                    break;
                    case 'inner':
                        $sql .= " JOIN {$join['table']}{$alias} ON {$join['on']}";
                    break;
                    case 'full':
                        $sql .= " FULL JOIN {$join['table']}{$alias} ON {$join['on']}";
                    break;
                }
            }
        }

        if (count($this->conditions) > 0) {
            array_walk($this->conditions, 'strval');
            $sql .= ' WHERE ' . implode(' AND ', $this->conditions);
        }

        if (count($this->groupBy)) {
            $sql .= ' GROUP BY ' . implode(',', $this->groupBy);
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

        return $sql;
    }

    /**
     * Execute the query
     * 
     * @return \PDOStatement
     */
    public function execute() {
        return $this->database->query($this->sql());
    }

}
