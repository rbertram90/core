<?php
namespace rbwebdesigns\core;

/**
 * core/session.php
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class Session
{
    public $currentUser = null;

    /**
     * Start the session
     */
    public function __construct()
    {
        if(!isset($_SESSION)) session_start();

        if(array_key_exists('user', $_SESSION)) {
            $this->currentUser = $_SESSION['user'];
        }
    }

    /**
     * Manually set the current user object
     * 
     * @param mixed $user
     *   Data to store in the current user object
     */
    public function setCurrentUser($user)
    {
        $_SESSION['user'] = $user;
        $this->currentUser = $_SESSION['user'];
    }

    /**
     * Get a session variable
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($name, $default = '')
    {
        if(isset($_SESSION[$name])) return $_SESSION[$name];
        else return $default;
    }

    /**
     * Set a session variable
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Unset a session variable
     *
     * @param string $name
     */
    public function delete($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Destroy the session
     */
    public function end()
    {
        session_destroy();
    }

    /**
     * Add flash message
     */
    public static function addMessage($message, $type='info')
    {
        if(!isset($_SESSION['messagetoshow'])) $_SESSION['messagetoshow'] = [];

        $_SESSION['messagetoshow'][] = [
            'text' => $message,
            'type' => $type
        ];
    }

    /**
     * Get & remove the first message
     */
    public static function getMessage()
    {
        // Return false if no messages
        if(!isset($_SESSION['messagetoshow']) || count($_SESSION['messagetoshow']) == 0) return false;

        // Return first element of messages array
        return array_shift($_SESSION['messagetoshow']);
    }

    /**
     * Get all messages & empty
     */
    public static function getAllMessages()
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