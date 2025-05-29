<?php
namespace App\Core;

/**
 * Main Application class
 * Handles routing and dispatching to controllers
 */
class Application
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];
    
    /**
     * Constructor - parses URL and sets controller, method and parameters
     */
    public function __construct()
    {
        $url = $this->parseUrl();
        
        // Set controller if it exists
        if (isset($url[0]) && file_exists(APP . 'controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }
        
        // Include the controller
        require_once APP . 'controllers/' . $this->controller . '.php';
        
        // Instantiate controller
        $controllerNamespace = 'App\\Controllers\\' . $this->controller;
        $this->controller = new $controllerNamespace();
        
        // Set method if it exists
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }
        
        // Set params if any exist
        $this->params = $url ? array_values($url) : [];
    }
    
    /**
     * Run the application by calling the controller method with parameters
     */
    public function run()
    {
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    /**
     * Parse URL into controller, method and parameters
     */
    protected function parseUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        
        return [];
    }
}
