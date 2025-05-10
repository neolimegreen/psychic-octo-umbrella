<?php
namespace App\Core;

/**
 * Router Class
 * 
 * Handles URL routing and maps URLs to controller actions.
 */
class Router {
    private $routes = [];
    private $params = [];
    private $notFoundHandler = null;
    
    /**
     * Add a route to the routing table
     * 
     * @param string $route  The route URL
     * @param array  $params Parameters (controller, action, etc.)
     * @return void
     */
    public function add($route, $params = []) {
        // Store the route with the parameters
        $this->routes[$route] = $params;
    }
    
    /**
     * Match the route to the routes in the routing table
     * 
     * @param string $url The route URL
     * @return boolean  True if a match found, false otherwise
     */
    public function match($url) {
        // Remove query string from URL if present
        if ($pos = strpos($url, '?')) {
            $url = substr($url, 0, $pos);
        }
        
        // Trim leading and trailing slashes
        $url = trim($url, '/');
        
        // Match to the fixed routes first (no variables)
        if (array_key_exists($url, $this->routes)) {
            $this->params = $this->routes[$url];
            return true;
        }
        
        // Match to the variable routes
        foreach ($this->routes as $route => $params) {
            // Convert route to regex
            $regexRoute = $this->convertRouteToRegex($route);
            
            if ($regexRoute === false) {
                continue; // Skip invalid routes
            }
            
            if (preg_match($regexRoute, $url, $matches)) {
                // Get named capture group values
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                
                $this->params = $params;
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Convert a route string to a valid regex pattern
     * 
     * @param string $route The route URL pattern
     * @return string|false The regex pattern or false if invalid
     */
    private function convertRouteToRegex($route) {
        // Skip routes that are already regex patterns
        if (strpos($route, '^') === 0) {
            return $route;
        }
        
        // Check for variable sections {var}
        if (strpos($route, '{') !== false && strpos($route, '}') !== false) {
            // Convert the route to a regular expression
            $route = str_replace('/', '\/', $route);
            $route = preg_replace('/\{([a-z]+)\}/', '(?P<$1>[^/]+)', $route);
            return '#^' . $route . '/?$#i';
        }
        
        // Standard routes (no variables)
        return '#^' . str_replace('/', '\/', $route) . '/?$#i';
    }
    
    /**
     * Dispatch the route, creating the controller object and running the
     * action method
     * 
     * @param string $url The route URL
     * @return void
     */
    public function dispatch($url) {
        if ($this->match($url)) {
            $controller = $this->getNamespace() . $this->params['controller'];
            
            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);
                
                $action = $this->params['action'] ?? 'index';
                $action = lcfirst(str_replace('-', '', ucwords($action, '-')));
                
                if (method_exists($controller_object, $action)) {
                    $controller_object->$action();
                } else {
                    throw new \Exception("Method $action in controller $controller not found");
                }
            } else {
                throw new \Exception("Controller class $controller not found");
            }
        } else {
            // If a route is not found, call the not found handler if it exists
            if ($this->notFoundHandler) {
                call_user_func($this->notFoundHandler);
            } else {
                header('HTTP/1.1 404 Not Found');
                echo '404 Page not found';
            }
        }
    }
    
    /**
     * Get the namespace for the controller class
     * 
     * @return string The namespace
     */
    private function getNamespace() {
        $namespace = 'App\\Controllers\\';
        
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }
        
        return $namespace;
    }
    
    /**
     * Get the currently matched parameters
     * 
     * @return array
     */
    public function getParams() {
        return $this->params;
    }
    
    /**
     * Set a 404 not found handler
     * 
     * @param callable $handler The function to be called when a route is not found
     * @return void
     */
    public function setNotFoundHandler($handler) {
        $this->notFoundHandler = $handler;
    }
}