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
    protected $db;
    protected $tableName;
    protected $fields;

    public function __construct($model)
    {
        $this->db = $model->getDatabaseConnection();
    }
    
    public function getFields()
    {
        return $this->fields;
    }
    
    public function getCount($arrayWhere)
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
     */
    public function get($arrayWhat, $arrayWhere, $order='', $limit='', $multi=true)
    {
        // $this->sanitizeFields($arrayWhere);
        if($multi) {
            return $this->db->selectMultipleRows($this->tableName, $arrayWhat, $arrayWhere, $order, $limit);
        } else {
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
