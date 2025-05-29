<?php
namespace App\Core;

/**
 * Base Controller class
 * All controllers will extend this class
 */
class Controller
{
    /**
     * Load and return a model
     *
     * @param string $model The model name
     * @return object The model instance
     */
    protected function model($model)
    {
        // Include the model file
        require_once APP . 'models/' . $model . '.php';
        
        // Instantiate the model
        $modelNamespace = 'App\\Models\\' . $model;
        return new $modelNamespace();
    }
    
    /**
     * Load a view
     *
     * @param string $view The view name
     * @param array $data Data to pass to the view
     * @return void
     */
    protected function view($view, $data = [])
    {
        // Check if the view file exists
        $viewFile = APP . 'views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            // Extract data to make variables available in the view
            extract($data);
            
            // Include the view
            require_once $viewFile;
        } else {
            // View not found
            die('View "' . $view . '" not found');
        }
    }
    
    /**
     * Redirect to another URL
     *
     * @param string $url The URL to redirect to
     * @return void
     */
    protected function redirect($url)
    {
        header('Location: ' . URL_ROOT . '/' . $url);
        exit;
    }
    
    /**
     * Check if the user is logged in
     *
     * @return boolean True if logged in, false otherwise
     */
    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if the user has the required role
     *
     * @param string $role The required role
     * @return boolean True if the user has the role, false otherwise
     */
    protected function hasRole($role)
    {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return $_SESSION['user_role'] === $role;
    }
}
