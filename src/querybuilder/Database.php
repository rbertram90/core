<?php

namespace rbwebdesigns\core\querybuilder;

class Database {

    /** @var \PDO $connection */
    protected $connection;

    /** @var \PDOStatement|bool $statement */
    protected $statement = false;

    /**
     * @param string[] $config
     *  [
     *    'host' => 'HOST NAME',
     *    'port' => 'PORT NUMBER',
     *    'name' => 'DATABASE NAME',
     *    'user' => 'USERNAME',
     *    'password' => 'PASSWORD'
     *  ]
     * 
     * @throws PDOException
     */
    public function __construct($config) {
        $this->config = array_merge([
            'host' => 'localhost',
            'port' => 3306,
            'name' => 'hamletcms',
            'user' => 'root',
            'password' => '',
        ], $config);

        $this->connect();
    }

    /**
     * Try and connect to the database
     * 
     * @throws PDOException
     */
    protected function connect() {
        $this->connection = new \PDO("mysql:host={$this->config['host']}; port={$this->config['port']}; dbname={$this->config['name']}", $this->config['user'], $this->config['password']);
    }

    /**
     * Create a select query on the database
     * 
     * @param string $table
     *  Database table name
     * @param string $alias
     *  Database table alias
     * 
     * @return \rbwebdesigns\core\querybuilder\SelectQuery
     */
    public function select(string $table, string $alias = '') {
        return new SelectQuery($this, $table, $alias);
    }

    /**
     * Create a insert query on the database
     * 
     * @param string $table
     *  Database table name
     * 
     * @return \rbwebdesigns\core\querybuilder\InsertQuery
     */
    public function insert(string $table) {
        return new InsertQuery($this, $table);
    }

    /**
     * Create an update query on the database
     * 
     * @param string $table
     *  Database table name
     * 
     * @return \rbwebdesigns\core\querybuilder\UpdateQuery
     */
    public function update(string $table) {
        return new UpdateQuery($this, $table);
    }

    /**
     * @see https://www.php.net/manual/en/pdo.query.php
     * 
     * @return PDOStatement|bool
     */
    public function query($sql) {
        if ($result = $this->connection->query($sql)) {
            return $result;
        }
        else {
            // @todo something?
            return $this->connection->errorInfo();
        }
    }

    /**
     * @see https://www.php.net/manual/en/pdo.prepare.php
     * 
     * @param string $sql
     * 
     * @return self
     */
    public function prepare(string $sql) {
        $this->statement = $this->connection->prepare($sql);
        return $this;
    }

    /**
     * @see https://www.php.net/manual/en/pdostatement.execute.php
     * 
     * @param mixed[] $values
     * 
     * @return bool
     */
    public function execute(array $values = []) {
        if (!$this->statement) {
            return false;
        }
        return $this->statement->execute($values);
    }
    
}