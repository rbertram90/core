<?php
namespace rbwebdesigns\core;

/**********************************************************
    core/request.php
    
    Functions
        $request->getString('name', 'Hello world');
        $request->getInt('age', 21);
    
**********************************************************/

class Request
{

    /**
     * Get the value of a string request variable
     * @param string - request key
     * @param string - value to use if key not found (default = '')
     * @return string - default if not found
     */
    public function getString($key, $default = '')
    {
        return isset($_REQUEST[$key]) ? sanitize_string($_REQUEST[$key]) : $default;
    }
    
    /**
     * Get the value of a integer request variable
     * @param string - request key
     * @param string - value to use if key not found (default = 0)
     * @return int - default if not found
     */
    public static function getInt($key, $default = 0)
    {
        return isset($_REQUEST[$key]) ? sanitize_number($_REQUEST[$key]) : $default;
    }

}