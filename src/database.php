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
 * @method PDO connect(string $server, string $name, string $user, string $password) Supply connection
 *  information and get database connection object in return
 * @method PDO getConnection() Get the current database connection object - connect() must have already
 *  been called
 * @method int getLastInsertID() This has limited use as a public method as needs to be called within
 *  transaction
 * @method PDOStatement query($queryString) Run raw sql - recommended to use the helper functions for
 *  simple queries and this for more complicated ones 
 * @method array selectSingleRow(string $tableName, array/string $columnsToSelect, array $where, string $strOrderBy='', string $strLimit='')
 *  Select a single row from database
 * @method array selectMultipleRows(string $tableName, array/string $columnsToSelect, array $where, string $strOrderBy='', string $strLimit='')
 *  Select multiple rows from database
 * @method int countRows(string $tableName, array $where='') Get a row count for conditions
 * @method PDOStatement insertRow($tableName, $values)
 * @method PDOStatement updateRow($tableName, $where, $values)
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
    
    public function connect($server, $name, $user, $pass)
    {
        $this->db_name = $name;
        $this->db_user = $user;
        $this->db_pass = $pass;
        $this->db_server = $server;

        return $this->getConnection();
    }
    
    /**
     *   Connect to database using PDO
     */
    public function getConnection()
    {
        if($this->db_connection !== null) return $this->db_connection;

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
     */
    public function getLastInsertID()
    {
        if(!$this->db_connection) return false;

        return $this->db_connection->lastInsertId();
    }
    
    /**
     * Run a standard query straight into the database
     * (not really the safest way but sometimes necessary!)
     * 
     * @return PDOStatement
     */
    public function query($queryString)
    {
        if(!$this->db_connection) return false;

        try {
            $query = $this->db_connection->query($queryString);
        }
        catch(\PDOException $e) {
            die($this->showSQLError($e, $queryString));
        }

        return $query;
	}
    
    
    /**
     * Format a helpful SQL error message
     * 
     * @todo change IS_DEVELOPMENT flag
     */
    private function showSQLError($err, $psQueryString="")
    {
		if(IS_DEVELOPMENT) {
			$errMessage = '<p class="error">';
			$errMessage.= '  <strong>Database Error!</strong><br>';
			$errMessage.= '  Details: '.$err->getMessage().'<br>';
			$errMessage.= '  File: <strong>'.$err->getFile().'</strong><br>';
			$errMessage.= '  Line: <strong>'.$err->getLine().'</strong><br>';
			if(strlen($psQueryString) > 0) $errMessage.= 'SQL: <strong>'.$psQueryString.'</strong>';
			$errMessage.= '</p>';
		} else {
			$errMessage = 'Oh No! Something has gone wrong, please contact support regarding a database error!';
		}
        return $errMessage;
    }
        
    /**
     * Does the grunt work for select
     */
    private function prepareSimpleSelect($tableName, $pColumnsToSelect, $parrWhere, $strOrderBy, $strLimit)
    {
		// What
        if(gettype($pColumnsToSelect) == 'string') $lsColumnsToSelect = $pColumnsToSelect;  
        else $lsColumnsToSelect = implode(',', $pColumnsToSelect);
        
		if(gettype($parrWhere) == 'string') $psWhere = ' ' . $parrWhere;
        else $psWhere = $this->createWhereStatement($parrWhere);
        
		// Order
		if(strlen($strOrderBy) > 0) $strOrderBy = ' ORDER BY '.$strOrderBy;
		
		// Limit
		if(strlen($strLimit) > 0) $strLimit = ' LIMIT '.$strLimit;
	
        return 'SELECT '.$lsColumnsToSelect.' FROM '.$tableName.' WHERE '.$psWhere.$strOrderBy.$strLimit;
    }
    
    /**
     * Select a single row from database
     */
    public function selectSingleRow($tableName, $columns, $parrWhere, $strOrderBy='', $strLimit='')
    {
        $queryString = $this->prepareSimpleSelect($tableName, $columns, $parrWhere, $strOrderBy, $strLimit);
		$query = $this->query($queryString);
        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Select a multiple rows from database
     */
    public function selectMultipleRows($tableName, $columns, $where, $orderBy='', $limit='')
    {
        $queryString = $this->prepareSimpleSelect($tableName, $columns, $where, $orderBy, $limit);
		$query = $this->query($queryString);
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a count of rows
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
     */
    public function insertRow($tableName, $values)
    {
        $i = $j = 0;
        $columnNames = $valuesString = '';

        foreach($values as $key => $value) {
            $comma = ($i == 0) ? '': ','; $i++;
            $columnNames.= $comma.'`'.$key.'`';
            $valuesString.= $comma.'"'.$value.'"';
        }

        return $this->query('INSERT INTO '.$tableName.' ('.$columnNames.') VALUES ('.$valuesString.')');
    }
    
    /**
     * Update a (single) row into the database
     */
    public function updateRow($tableName, $where, $values)
    {
        $i = 0;
        $columnNames = $whereString = '';

        foreach($values as $key => $value) {
            $comma = ($i == 0) ? '': ',';
            $lsColumnNames.= $comma.' `'.$key.'`="'.$value.'"';
			$i++;
        }

		$whereString = $this->createWhereStatement($where);
        return $this->query('UPDATE '.$tableName.' SET '.$columnNames.' WHERE '.$whereString);
    }
    
    /**
     * Delete a row into the database
     */
    public function deleteRow($tableName, $where)
    {
        $whereString = $this->createWhereStatement($where);
        return $this->query("DELETE FROM {$tableName} WHERE {$whereString}");
    }
}
