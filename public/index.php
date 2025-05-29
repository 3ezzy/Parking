<?php
/**
 * Main entry point for the Parking Management System
 * Routes all requests to the appropriate controller
 */

// Define the application root directory
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APP', ROOT . 'app' . DIRECTORY_SEPARATOR);

// Require the bootstrap file
require_once APP . 'core/bootstrap.php';

// Initialize the application
$app = new App\Core\Application();

// Run the application
$app->run();
