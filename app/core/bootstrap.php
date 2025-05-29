<?php
/**
 * Bootstrap file for the Parking Management System
 * Loads configurations and core classes
 */

// Load configurations
require_once ROOT . 'config/config.php';

// Autoload classes
spl_autoload_register(function ($className) {
    // Convert namespace to path
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    
    // Define the file path
    $file = ROOT . $className . '.php';
    
    // Check if file exists and require it
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load helper functions
require_once APP . 'core/helpers.php';

// Start session
session_start();
