<?php
namespace rbwebdesigns\core\model;

/**
 * core/model/User.php
 * 
 * Starter class for a user model, contains only
 * the really basic fields and functions
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 * 
 * @method boolean updateDetails(array $fields)
 * @method boolean updatePassword(array $fields)
 * @method boolean deleteAccount()
 */
class User extends RBModel
{
	protected $db;
	protected $tableName;
    protected $fields;
    
    public $id;

	public function __construct()
    {
        $this->tblname = $tableName;
        $this->fields = array(
            'id' => 'number',
            'username' => 'string',
            'password' => 'string',
        );
    }

	/**
	 * Update the details of an account
	 * 
	 * @param \rbwebdesigns\core\Request $request
	 *   Request with POST variables for:
	 *    * password
	 *    * password_confirm
	 */
	public function updateDetails($request)
    {
		if ($request->method() != 'POST') return false;

		$password = $request->get('password');
		$passwordConfirm = $request->get('password_confirm');

		if ($password !== $passwordConfirm) return false;

		
	}
    
    // Delete a user
	public function deleteAccount($request)
    {
		
	}
}
