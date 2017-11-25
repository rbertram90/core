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

}