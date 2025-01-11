<?php

namespace rbwebdesigns\core;

class Router
{
    protected $routes;
    
    function __construct($routes) {
        $this->routes = $routes;
    }
    
    public function lookup($query) {
        if (array_key_exists($query, $this->routes)) {
            return $this->routes[$query];
        }
        
        return false;
    }

    /**
     * Get the contents for a controller endpoint.
     * 
     * Route example:
     * [
     *    'controller' => MyController::class,
     *    'function' => 'viewPage',
     *    'methods' => ['GET'], // optional - restrict the request methods.
     * ]
     * 
     * @todo parameters...
     */
    public function getContents(string $routeName) {
        if (! $route = $this->lookup($routeName)) {
            throw new \Exception("Unable to find route");
        }

        if (key_exists('methods', $route) && ! in_array($_SERVER['REQUEST_METHOD'], $route['methods'])) {
            throw new \Exception("{$_SERVER['REQUEST_METHOD']} requests are not permitted on this route.");
        }

        if (! class_exists($route['controller'])) {
            throw new \Exception("Unknown class: " . $route['controller']);
        }

        if (! method_exists($route['controller'], $route['function'])) {
            throw new \Exception("Unknown method `{$route['function']}` on class `{$route['controller']}`");
        }

        $controller = new ($route['controller']);
        return call_user_func([$controller, $route['function']]);
    }
}
