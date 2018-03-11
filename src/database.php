<?php
namespace rbwebdesigns\core;

/**
 * Class DB
 *   - All database access using PDO
 *   - All SQL errors caught and handled in consistant way
 *   - Limitations/ future improvements:
 *       - update and delete only handle WHERE val=this
 *       - update, insert and delete will only handle one insert
 *       - data needs to be validated and verified before being
 *         passed here.
 *       - could use prepare statements instead?
 * 
 * @method PDO connect(string $server, string $name, string $user, string $password)
 *  Supply connection information and get database connection object in return
 * @method PDO getConnection()
 *  Get the current database connection object - connect() must have already been called
 * @method int getLastInsertID()
 *  This has limited use as a public method as needs to be called within transaction
 * @method PDOStatement query(string $queryString)
 *  Run raw sql - recommended to use the helper functions for simple queries and this for more complicated ones
 * @method bool runPreparedStatement(string $queryString, array $values)
 *  Run prepared statement SQL (that is already formatted with placeholders) with $values to pass through.
 *  Returns the result of the execution
 * @method array selectSingleRow(string $tableName, array/string $columnsToSelect, array $where, string $strOrderBy='', string $strLimit='')
 *  Select a single row from database
 * @method array selectMultipleRows(string $tableName, array/string $columnsToSelect, array $where, string $strOrderBy='', string $strLimit='')
 *  Select multiple rows from database
 * @method array selectAllRows(string $tableName, array/string $columnsToSelect, string $strOrderBy='')
 * @method int countRows(string $tableName, array $where='') Get a row count for conditions
 * @method bool insertRow($tableName, $values)
 * @method bool updateRow($tableName, $where, $values)
 * @method PDOStatement deleteRow($tableName, $where)
 * 
 * Documentation:
 * https://github.com/rbertram90/core/wiki/Database
 * 
 * @author R.Bertram <ricky@rbwebdesigns.co.uk>
*/
class Database
{
    protected $db_name = '';
    protected $db_user = '';
    protected $db_pass = '';
    protected $db_server = '';
    private $db_connection = null;

    public function __construct()
    {
        
    }
    
    /**
     * Establish the connection to the database
     * 
     * @return PDO
     */
    public function connect($server, $name, $user, $pass)
    {
        $this->db_name = $name;
        $this->db_user = $user;
        $this->db_pass = $pass;
        $this->db_server = $server;

        return $this->getConnection();
    }
    
    /**
     * @return bool
     *   Has the connect method been run?
     */
    public function isConnected()
    {
        return $this->db_connection != null;
    }

    /**
     * Connect to database using PDO
     * 
     * @return PDO
     */
    public function getConnection()
    {
        if($this->isConnected()) return $this->db_connection;

        try {
            // Try to connect
            $db_connect = new \PDO('mysql:host='.$this->db_server.';dbname='.$this->db_name, $this->db_user, $this->db_pass);
            // Set exceptions to show
            $db_connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        // Catch connection errors
        catch(\PDOException $e) {
            die($this->showSQLError($e));
        }

        $this->db_connection = $db_connect;

        return $this->db_connection;
    }

    /**
     * Get the ID number of the last inserted row using PDO standard methods
     * 
     * @return int
     */
    public function getLastInsertID()
    {
        if(!$this->db_connection) return false;

        return $this->db_connection->lastInsertId();
    }
    
    /**
     * Run a standard query straight into the database
     * ideally shouldn't be called from outside this class,
     * but sometimes necessary for complex queries.
     * 
     * @return PDOStatement
     */
    public function query($queryString)
    {
        if(!$this->db_connection) throw new \Exception('Database not connected');

        try {
            $query = $this->db_connection->query($queryString);
        }
        catch(\PDOException $e) {
            die($this->showSQLError($e, $queryString));
        }

        return $query;
    }
    
    /**
     * Query the database using a prepared statement - much
     * safer than query.
     * 
     * @param string $queryString
     *   SQL string with placeholders for values
     * @param array $values
     *   if the keys of the array are named then these will be used as
     *   placeholder names.
     * 
     * @return bool
     */
    public function runPreparedStatement($queryString, $values)
    {
        if(!$this->db_connection) throw new \Exception('Database not connected');

        try {
            $statement = $this->db_connection->prepare($queryString);
            $j = 1;
            foreach($values as $key => $value) {
                if($key === $j-1) {
                    // print "binding value $j";
                    $statement->bindValue($j, $value);
                }
                else {
                    // print "binding value $key";
                    $statement->bindValue(':' . $key, $value);
                }
                $j++;
            }
            
            return $statement->execute();
        }
        catch(\PDOException $e) {
            die($this->showSQLError($e, $queryString));
        }
    }
    
    /**
     * Format a helpful SQL error message
     * 
     * @todo re-consider IS_DEVELOPMENT flag - ENV variable?
     */
    private function showSQLError($err, $queryString="")
    {
		if(true || defined('IS_DEVELOPMENT') && IS_DEVELOPMENT) {
			$errMessage = '<p class="error">';
			$errMessage.= '  <strong>Database Error!</strong><br>';
			$errMessage.= '  Details: ' . $err->getMessage() . '<br>';
			$errMessage.= '  File: <strong>' . $err->getFile() . '</strong><br>';
			$errMessage.= '  Line: <strong>' . $err->getLine() . '</strong><br>';
			if(strlen($queryString) > 0) $errMessage.= 'SQL: <strong>' . $queryString . '</strong>';
            $errMessage.= '</p>';
            $errMessage.= '<textarea>' . print_r(debug_backtrace(), true) .'</textarea>';
        }
        else {
			$errMessage = '<p>Oh No! Something has gone wrong, please contact support regarding a database error!</p>';
		}
        return $errMessage;
    }
        
    /**
     * Does the grunt work for select
     * 
     * @return string
     */
    private function prepareSimpleSelect($tableName, $columnsToSelect, $where, $orderBy, $limit)
    {
		// Columns to fetch
        if(is_array($columnsToSelect)) $columnsToSelect = implode(',', $columnsToSelect);
        
        // Conditions
        if(is_array($where)) $where = $this->createWhereStatement($where);
        else $where = ' ' . $where;
        
		// Order
		if(strlen($orderBy) > 0) $orderBy = ' ORDER BY ' . $orderBy;
		
		// Limit
		if(strlen($limit) > 0) $limit = ' LIMIT ' . $limit;
	
        return 'SELECT '.$columnsToSelect.' FROM '.$tableName.' WHERE '.$where.$orderBy.$limit;
    }
    
    /**
     * Select a single row from database
     * 
     * @return array
     */
    public function selectSingleRow($tableName, $columns, $where, $orderBy='', $limit='')
    {
        $queryString = $this->prepareSimpleSelect($tableName, $columns, $where, $orderBy, $limit);
		$query = $this->query($queryString);
        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Select a multiple rows from database
     * 
     * @return array
     */
    public function selectMultipleRows($tableName, $columns, $where, $orderBy='', $limit='')
    {
        $queryString = $this->prepareSimpleSelect($tableName, $columns, $where, $orderBy, $limit);
		$query = $this->query($queryString);
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Select all rows from a table in the database
     * 
     * @param string $tableName
     * @param mixed $columns
     * @param string $orderBy
     */
    public function selectAllRows($tableName, $columns, $orderBy='')
    {
        return $this->selectMultipleRows($tableName, $columns, [1 => 1], $orderBy);
    }
    
    /**
     * Get a count of rows
     * 
     * @return int
     */
    public function countRows($tableName, $where='')
    {
        if(getType($where) == 'array') {
            $querystring = $this->prepareSimpleSelect($tableName, 'count(*) as rowcount', $where, '', '');
        }
        else {
            $querystring = 'SELECT count(*) as rowcount from '.$tableName;
        }

		$query = $this->query($querystring);
        $result = $query->fetch(\PDO::FETCH_ASSOC);
		return $result['rowcount'];
	}
	
    /*
        ISSUE! Cannot have multiple limitations on the same field as array(
            'timestamp' => '>now'
            'timestamp' => '<later'
        ) will only keep the second rule!
    */
    private function createWhereStatement($where)
    {
        $i = 0;
        $whereString = '';

        foreach($where as $key => $value) {
            if($value[0] == '>') {
                $value = ltrim($value, '>');
                $op = '>';
            }
            elseif($value[0] == '<') {
                $value = ltrim($value, '<');
                $op = '<';
            }
            elseif($value[0] == '!') {
                $value = ltrim($value, '!');
                $op = '!=';
            }
            else {
                $op = '=';
            }
            
            // Look for dates
            if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $value)) {
                $value = 'TIMESTAMP("'.$value.'")';
            }
            elseif(trim($value) == 'CURRENT_TIMESTAMP') {
                 $value = "CURRENT_TIMESTAMP"; // hack around for sql keyword.
            }
            else {
                $value = "'".$value."'";
            }
            
            $comma = ($i == 0) ? '': ' AND '; $i++;
            $whereString.= $comma.' '.$key.$op.$value;
        }

        return $whereString;
    }
    
    /**
     * Insert a new row into the database
     * 
     * @return bool
     */
    public function insertRow($tableName, $values)
    {
        $i = 0;
        $columnNames = $valuesString = '';

        foreach($values as $key => $value) {
            $comma = ($i == 0) ? '': ', ';
            $i++;
            $columnNames.= $comma.'`'.$key.'`';
            if(is_numeric($key)) {
                $valuesString.= $comma.'?';
            }
            else {
                $valuesString.= $comma . ':' . $key;
            }
        }

        $queryString = 'INSERT INTO ' . $tableName . ' (' . $columnNames . ') VALUES (' . $valuesString . ')';

        return $this->runPreparedStatement($queryString, $values);
    }
    
    /**
     * Update a (single) row into the database
     * 
     * @return bool
     */
    public function updateRow($tableName, $where, $values)
    {
        $i = 0;
        $columnNames = $whereString = '';

        foreach($values as $key => $value) {
            $comma = ($i == 0) ? '': ', ';
            $columnNames.= $comma . ' `' . $key . '`= :'.$key;
			$i++;
        }

        $whereString = $this->createWhereStatement($where);
        $queryString = 'UPDATE '.$tableName.' SET '.$columnNames.' WHERE '.$whereString;

        return $this->runPreparedStatement($queryString, $values);
    }
    
    /**
     * Delete a row into the database
     * 
     * @return PDOStatement
     */
    public function deleteRow($tableName, $where)
    {
        $whereString = $this->createWhereStatement($where);
        return $this->query("DELETE FROM {$tableName} WHERE {$whereString}");
    }
}
