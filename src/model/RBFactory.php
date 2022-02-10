<?php
namespace rbwebdesigns\core\model;

use rbwebdesigns\core\Sanitize;

/**
 * core/model/RBFactory.php
 * @author: R Bertram <ricky@rbwebdesigns.co.uk>
 *
 * This class provides the generic basic CRUD functionality for any /
 * every model under RBwebdesigns projects.
 *
 * The functions provide an interface to the database class - which can
 * be used directly but this way prevents having to repeat myself in the
 * different models.
 *
 * It also now sanitizes the values that are being inserted by using the
 * $fields array which needs to be set for each model. The key must match
 * the database field name and the value is the datatype - values as below.
 *
 * Example:
 * $this->fields = array(
 *  'name' => 'string',
 *  'age' => 'number',
 *  'bio' => 'memo',
 *  'dateofbirth' => 'datetime', // Must match YYYY-MM-DD HH:mm:SS
 *  'male' => 'boolean'
 * );
 */
class RBFactory
{
    /**
     * @var \rbwebdesigns\core\ObjectDatabase
     */
    protected $db;
    /**
     * @var string Name of the table for which this model primarily relates
     */
    protected $tableName;
    /**
     * @var array List of the database fields and their associated datatypes
     */
    protected $fields;
    /**
     * @var string If set, enables fetching objects automatically, rather than array
     */
    protected $subClass;

    /**
     * @var \rbwebdesigns\core\model\ModelManager
     */
    protected $modelManager;

    /**
     * @param \rbwebdesigns\core\model\ModelManager $model
     */
    public function __construct($model)
    {
        $this->modelManager = $model;
        $this->db = $model->getDatabaseConnection();
    }
    
    /**
     * @return array
     *   List of fields in the database [name => datatype]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Add a field
     */
    public function registerField($name, $type) {
        $this->fields[$name] = $type;
    }
    
    /**
     * Alias of getCount()
     */
    public function count($arrayWhere = '')
    {
        return $this->getCount($arrayWhere);
    }

    /**
     * Count the number of rows in the database matching criteria
     * 
     * @param array $arrayWhere
     * 
     * @return int Row count
     */
    public function getCount($arrayWhere = '')
    {
        if(getType($arrayWhere) == 'array') {
            return $this->db->countRows($this->tableName, $arrayWhere);
        } else {
            return $this->db->countRows($this->tableName);
        }
    }
    
    /**
     * Select from database
     * 
     * @param mixed $arrayWhat
     *   Columns to fetch from db, pass as either string ('*') or array
     * @param array $arrayWhere
     *   Rows to fetch
     * @param string $orderBy
     *   Ordering - Standard SQL format
     * @param string $limit
     *   Limit rows - matches standard SQL format
     * @param bool $multi
     *   Is the expected output a single row (false) or multiple (true)?
     * @param bool $array
     *   Ignore subclass and force return type to be an array.
     */
    public function get($arrayWhat, $arrayWhere, $order='', $limit='', $multi=true, $array=false)
    {
        if ($this->subClass && !$array) {
            if ($multi) {
                return $this->db->selectMultipleObjects($this->subClass, $this->tableName, $arrayWhat, $arrayWhere, $order, $limit);
            }
            else {
                return $this->db->selectSingleObject($this->subClass, $this->tableName, $arrayWhat, $arrayWhere, $order, $limit);
            }
        }

        if ($multi) {
            return $this->db->selectMultipleRows($this->tableName, $arrayWhat, $arrayWhere, $order, $limit);
        }
        else {
            return $this->db->selectSingleRow($this->tableName, $arrayWhat, $arrayWhere, $order, $limit);
        }
    }
    
    public function insert($arrayWhat)
    {
        return $this->db->insertRow($this->tableName, $arrayWhat);
    }
    
    public function update($arrayWhere, $arrayWhat)
    {
        // $this->sanitizeFields($arrayWhat);
        // $this->sanitizeFields($arrayWhere);
        return $this->db->updateRow($this->tableName, $arrayWhere, $arrayWhat);
    }
    
    public function delete($arrayWhere)
    {
        return $this->db->deleteRow($this->tableName, $arrayWhere);
    }
    
    public function sanitizeFields(&$values)
    {
        foreach($values as $key => $value) {
            $values[$key] = $this->sanitizeField($key, $value);
        }
    }
    
    public function sanitizeField($fieldkey, $value)
    {
        if(array_key_exists($fieldkey, $this->fields)):
            switch($this->fields[$fieldkey]):
                case "string":
                case "memo":
                    return Sanitize::string($value);
                    break;
                case "number":
                    return Sanitize::int($value);
                    break;
                case "boolean":
                    return Sanitize::boolean($value);
                    break;
                case "datetime":
                    return Sanitize::timestamp($value);
                    break;
            endswitch;
        endif;
        
        // Should have returned by now...
        die("Unable to sanitize field: ".$fieldkey." value = ".$value." (model.php function sanitize_field)");
    }

}
