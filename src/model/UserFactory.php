<?php
namespace rbwebdesigns\core\model;

use rbwebdesigns\core\Sanitize;

/**************************************************************************
     class-users.php
     @description provides access the users database
     @author R.Bertram
     @date 2013 - Rewritten Jan 2013 to incorporate PDO
***************************************************************************/

class UserFactory extends RBFactory
{
	protected $db;
	protected $tableName;
	protected $fields;
	
	/**
	 * @param rbwebdesigns\core\Database $databaseConnection
	 * @param string $tableName
	 */
	public function __construct($databaseConnection, $tableName)
    {
		$this->db = $databaseConnection;
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

	// Get user by username
	public function getByUsername($strUsername)
    {
		return $this->db->selectSingleRow(TBL_USERS, '*', array(
            'username' => Sanitize::string($strUsername)
        ));
	}
	
	// Get user by id
	public function getById($intUserId)
    {
		return $this->db->selectSingleRow(TBL_USERS, '*', array(
            'id' => Sanitize::int($intUserId)
        ));
	}
	
	// Get user by email address
	public function getByEmail($strEmail)
    {
		return $this->db->selectSingleRow(TBL_USERS, '*', array(
            'email' => Sanitize::string($strEmail)
        ));
	}
    
    public function searchByUsername($username)
    {
        return $this->db->selectMultipleRows(TBL_USERS, '*', 'username LIKE "%' . $username . '%"');
    }
	
	// Get all memebers
	public function getAll()
    {
        $arrWhat = array('id', 'username', 'signup_date', 'profile_picture');
        return $this->db->selectMultipleRows(TBL_USERS, $arrWhat, $arrWhere);
	}
	
	// Get the last $num most recent members
	public function getRecent($num)
    {
		try
        {
			$query_recent = $this->db->query("SELECT id,name,profile_picture FROM ".TBL_USERS." ORDER BY signup_date DESC LIMIT ".$num);
			
		} catch(PDOException $e) { die(showQueryError($e)); }
		
		return $query_recent->fetchAll(PDO::FETCH_ASSOC);
	}

/*
	// Get an array of friends data
	public function getFriendsList($id)
    {
		$query_string = "SELECT * FROM rbwebdesigns.friends WHERE userid='$id' OR friendid='$id'";
		return $this->db->select_multi($query_string);
	}
	
	// Get an array of friends data full of data from user table
	public function expandedFriendsList($id)
    {
		$larrFriends = $this->getFriendsList($id);
		$res = array();
				
		foreach($larrFriends as $friendid):
		
			if($friendid['friendid'] == $id) {
				$res[] = $this->getUserById($friendid['userid']);
			} else {
				$res[] = $this->getUserById($friendid['friendid']);
			}

		endforeach;
		
		return $res;
	}
*/
}