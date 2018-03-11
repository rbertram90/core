<?php
namespace rbwebdesigns\core;

use rbwebdesigns\core\Sanitize;
// notes - logins - should we add a session id so that only one session can exist at once
// only when this function is called will it be reset?
// - auto logout?
// session manager class

class AccountManager {

    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
        $this->modelUsers = $GLOBALS['modelUsers'];
    }
    
    /**
        Note - this function assumes parameters have been sanitized!
        Should be safe as only accessible from this class
    **/
    private function findUser($strUsername, $strPassword) {
        $arrWhere = array(
            'username' => $strUsername,
            'password' => $strPassword
        );
        return $this->db->selectSingleRow(TBL_USERS, array('id','admin'), $arrWhere);
    }

    public function login($strUsername, $strPassword) {
        
        // User Input
        $strUsername = Sanitize::string($strUsername);
        $strPassword = md5(Sanitize::string($strPassword));
        
        $arrUser = $this->findUser($strUsername, $strPassword);
        
        if(gettype($arrUser) == 'array') {
            
            // Set the user that is logged in
            $_SESSION['user'] = $arrUser['id'];
            $_SESSION['admin'] = $arrUser['admin'];
            
            return true;
        }
        else return false;
    }
    
    public function logout() {
    
    }

    public function changePassword() {
        // put here?
    }
    
    public function newAccount()
    {
        // Check a username and password were provided
        if(strlen($_POST['fld_password']) < 8) return false;
        if(strlen($_POST['fld_username']) < 2) return false;
        
        // Check passwords match
        if(sanitize_string($_POST['fld_password']) != Sanitize::string($_POST['fld_password_2']))
        {
            return false;
        }
        
        // Check if this username already exists
        if($this->modelUsers->getCount(array('username' => Sanitize::string($_POST['fld_username']))) > 0)
        {
            return false;
        }
        
        return $this->modelUsers->insert(array(
            'name'     => Sanitize::string($_POST['fld_name']),
            'surname'  => Sanitize::string($_POST['fld_surname']),
            'username' => Sanitize::string($_POST['fld_username']),
            'password' => md5(Sanitize::string($_POST['fld_password'])),
            'email'    => Sanitize::string($_POST['fld_email']),
            'admin'    => 0,
            'signup_date' => date('Y-m-d H:i:s')
        ));
    }
}
?>