<?php
namespace rbwebdesigns\core;

/**
 * core/session.php
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 *
 * @property rbwebdesigns\core\model\User $currentUser;
 * @method mixed get(string $name, mixed $default)
 * @method void set(string $name, mixed $value)
 */
class Session
{

    public $currentUser = null;

    /**
     * Start the session
     */
    public function __construct() {
        session_start();
    }

    /**
     * Get a session variable
     * @param string $name
     * @param mixed $default
     */
    public function get($name, $default = '')
    {
        if(isset($_SESSION[$name])) return $_SESSION[$name];
        else return $default;
    }

    /**
     * Set a session variable
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }


    /**
     * Add flash message
     */
    public function addMessage($message)
    {
        if(!isset($_SESSION['messagetoshow'])) $_SESSION['messagetoshow'] = [];

        $_SESSION['messagetoshow'][] = $message;
    }

    /**
     * Get & remove the first message
     */
    public function getMessage()
    {
        // Return false if no messages
        if(!isset($_SESSION['messagetoshow']) || count($_SESSION['messagetoshow']) == 0) return false;

        // Return first element of messages array
        return array_shift($_SESSION['messagetoshow']);
    }

    /**
     * Get all messages & empty
     */
    public function getAllMessages()
    {
        // Check variable exists
        if(!isset($_SESSION['messagetoshow'])) $_SESSION['messagetoshow'] = [];

        // Store in temp variable
        $messages = $_SESSION['messagetoshow'];

        // Clear all messages
        $_SESSION['messagetoshow'] = [];

        // Return cached
        return $messages;
    }
}