<?php
namespace rbwebdesigns\core\model;

/**
 * core/model/RBModel.php
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
 *   Example:
 *   $this->fields = array(
 *       'name' => 'string',
 *       'age' => 'number',
 *       'bio' => 'memo',
 *       'dateofbirth' => 'datetime', // Must match YYYY-MM-DD HH:mm:SS
 *       'male' => 'boolean'
 *   );
 */
class RBModel
{
    protected $db;
    protected $tblname;
    protected $fields;

    public function __construct($db, $tableName) {
        // These MUST be overridden...
        $this->db = $db;
        $this->fields = [];
        $this->tblname = $tableName;
    }

    public function delete($arrayWhere) {
        return $this->db->deleteRow($this->tblname, $arrayWhere);
    }
    
}

?>