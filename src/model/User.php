<?php
namespace rbwebdesigns\core\model;

/**
 * core/model/User.php
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

	/**
	 * @param rbwebdesigns\core\Database $databaseConnection
	 * @param string $tableName
	 */
	public function __construct($databaseConnection, $tableName)
    {
        $this->tblname = $tableName;
        $this->fields = array(
            'id' => 'number',
            'name' => 'string',
            'surname' => 'string',
            'username' => 'string',
            'password' => 'string',
            'email' => 'string',
            'dob' => 'datetime',
            'gender' => 'string',
            'location' => 'string',
            'profile_picture' => 'string',
            'description' => 'memo',
            'admin' => 'boolean',
            'signup_date' => 'datetime',
            'flickrid' => 'string',
            'security_q' => 'string',
            'security_a' => 'string'
        );
    }

	// Update the details of an account
	public function updateDetails($fields)
    {
		// Loop through all values in the array fields adding them to the SQL WHERE string
		$sql_set_clause = "";
		foreach($fields as $key=> $value)
        {
			$sql_set_clause .= " $key='$value',";
		}
		
		// Remove the final unwanted comma
		$sql_set_clause = substr($sql_set_clause,0,-1);
		
		try {
			$this->db->runQuery("UPDATE ".TBL_USERS." SET $sql_set_clause WHERE id='".$_SESSION['userid']."'");
			
		} catch(PDOException $e) { die(showQueryError($e)); }
		        
		return true;
	}

	// Stage 1 for password update
	public function updatePassword($details)
    {
		// Check the repeated passwords match
		if($details['new_password'] != $details['new_password_rpt'])
        {
			echo showError("Password do not match!");
			return;
		}
		
		$lobjUser = $this->getUserById($this->id);
		
		// Check the given password matches the database
		if(md5($details['current_password']) != $lobjUser['password'])
        {
			echo showError("Existing password didn't match!");
			return;
		}
		
		$md5password = md5($details['new_password']);
		
		try
        {
			$this->db->query("UPDATE ".TBL_USERS." SET password='$md5password' WHERE id='".$this->id."'");
			
		} catch(PDOException $e) { die(showQueryError($e)); }
		
		return true;
	}
	
	// Password reset request submitted
	public function resetPassword()
    {
		// Generate a password
		$newpswd = generateRandomAlphaNumeric(8);
		$hashpswd = md5($newpswd);
		
		// Update database
		try {
			 $this->db->query("UPDATE ".TBL_USERS." SET password='$hashpswd' WHERE id='$this->id'");
			 
		} catch(PDOException $e) { die(showQueryError($e)); }
		
		// Return the new password
		return $newpswd;
    }
    
    // Delete a user
	public function deleteAccount()
    {
		try
        {
			// Query Database
			$this->db->query("DELETE FROM ".TBL_USERS." WHERE id=".$this->id, $this->db);
			
		} catch(PDOException $e) { die(showQueryError($e)); }
        
		// Logout
		session_destroy();
		
		// Redirect to homepage
		header("Location: index.php");
	}
}
