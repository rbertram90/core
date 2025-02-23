<?php
namespace rbwebdesigns\core\model;

use rbwebdesigns\core\ObjectDatabase;
use rbwebdesigns\macscomputers\App;

/**
 * core/model/RBModel.php
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
    /**
     * Field name for the primary key.
     */
    public static string $identifier = 'id';

    public function __construct(protected ObjectDatabase $db) {}

    /**
     * Get the table name for this model.
     */
    public static abstract function tableName(): string;

    /**
     * Load an instance of the model by it's primary key.
     * 
     * False returned if could not find a match for the ID.
     */
    public static function load(string|int $id): static|false
    {
        /** @var \rbwebdesigns\core\ObjectDatabase */
        $db = App::container()->get('database');

        return $db->selectSingleObject(static::class, static::tableName(), '*', [static::$identifier => $id]);
    }

    /**
     * Delete this model.
     */
    public function delete()
    {
        return $this->db->deleteRow(static::tableName(), [static::$identifier => $this->{static::$identifier}]);
    }
    
}
