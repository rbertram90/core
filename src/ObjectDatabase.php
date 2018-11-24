<?php
namespace rbwebdesigns\core;

/**
 * ObjectDatabase
 * 
 * Same functionality of the rbwebdesigns\Database class but results
 * are returned as objects rather than arrays.
 */
class ObjectDatabase extends Database
{

    /**
     * Select a single row from the database
     * 
     * @param string $className
     * @param string $tableName
     * @param mixed $columns
     * @param array $where
     * @param string $orderBy
     * @param string $limit
     * 
     * @return object
     */
    public function selectSingleRow($className, $tableName, $columns, $where, $orderBy='', $limit='')
    {
        $queryString = $this->prepareSimpleSelect($tableName, $columns, $where, $orderBy, $limit);
        return $this->query($queryString)->fetchObject($className);
    }

    /**
     * Select multiple rows from the database
     * 
     * @param string $className
     * @param string $tableName
     * @param mixed $columns
     * @param array $where
     * @param string $orderBy
     * @param string $limit
     * 
     * @return object[]
     */
    public function selectMultipleRows($className, $tableName, $columns, $where, $orderBy='', $limit='')
    {
        $queryString = $this->prepareSimpleSelect($tableName, $columns, $where, $orderBy, $limit);
		$query = $this->query($queryString);
        return $query->fetchAll(\PDO::FETCH_CLASS, $className);
    }

    /**
     * Select all rows from a table in the database
     * 
     * @param string $className
     * @param string $tableName
     * @param mixed $columns
     * @param string $orderBy
     * 
     * @return object[]
     */
    public function selectAllRows($className, $tableName, $columns, $orderBy='')
    {
        return $this->selectMultipleRows($className, $tableName, $columns, [1 => 1], $orderBy);
    }
}