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
    protected $urlParameters;
    protected $controller;

    /**
     * Requests are routed for pretty urls
     * xyz.com/controller/action/data => xyz.com?p=controller&query=action/data
     */
    public function __construct()
    {
        $this->controller = $this->getInt('p', 0);
        $this->urlParameters = explode('/', $this->getString('query'));
    }

    /**
     * @return string request method
     */
    public function method()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get the value of a string request variable
     * @param  string  request key
     * @param  string  value to use if key not found (default = '')
     * @return string  default if not found
     */
    public function getString($key, $default = '')
    {
        return isset($_REQUEST[$key]) ? Sanitize::string($_REQUEST[$key]) : $default;
    }
    
    /**
     * Get the value of a integer request variable
     * @param  string  request key
     * @param  string  value to use if key not found (default = 0)
     * @return int     default if not found
     */
    public function getInt($key, $default = 0)
    {
        return isset($_REQUEST[$key]) ? Sanitize::int($_REQUEST[$key]) : $default;
    }

    /**
     * @param int $index
     */
    public function getUrlParameter($index, $default = false)
    {
        if(array_key_exists($index, $this->urlParameters)) {
            return $this->urlParameters[$index];
        }
        else {
            return $default;
        }
    }

}