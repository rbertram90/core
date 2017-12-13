<?php
namespace rbwebdesigns\core;

/**
 * core/Request.php
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 * 
 * Documentation:
 * https://github.com/rbertram90/core/wiki/Request
 */
class Request
{

    protected $urlParameters;
    protected $controller;

    public $isAjax = false;

    /**
     * Requests are routed for pretty urls
     * xyz.com/controller/action/data => xyz.com?p=controller&query=action/data
     */
    public function __construct($options = [])
    {
        $controllerKey = 'p';
        if(array_key_exists('controllerKey', $options)) {
            $controllerKey = $options['controllerKey'];
        }
        
        $this->controller = $this->getString($controllerKey, 0);
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
     * @return string controller class name
     */
    public function getControllerName()
    {
        return $this->controller;
    }

    /**
     * @param int $index
     * @return mixed url parameter value
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