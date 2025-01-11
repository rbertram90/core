<?php
namespace rbwebdesigns\core\model;

use rbwebdesigns\core\ObjectDatabase;

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
abstract class RBModel
{
    public string $identifier = 'id';

    public function __construct(protected ObjectDatabase $db) {}

    /**
     * Get the table name for this model.
     */
    public abstract function tableName(): string;

    /**
     * Delete this model.
     */
    public function delete()
    {
        return $this->db->deleteRow($this->tableName(), [$this->identifier => $this->{$this->identifier}]);
    }
    
}
