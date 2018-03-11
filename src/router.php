<?php
namespace rbwebdesigns\core;

class Router
{
    private $routes;
    
    function __construct($routes) {
		$this->routes = $routes;
    }
    
    public function lookup($query) {
        if(array_key_exists($query, $this->routes)) {
           return $this->routes[$query];
        }
        else return false;
    }
}
