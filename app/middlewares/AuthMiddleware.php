<?php
namespace App\Middlewares;

/**
 * Authentication Middleware
 * Controls access to routes based on authentication status and user roles
 */
class AuthMiddleware
{
    /**
     * Check if the user is logged in
     *
     * @return bool True if logged in, false otherwise
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if the user has the specified role
     *
     * @param string $role The role to check for
     * @return bool True if the user has the role, false otherwise
     */
    public static function hasRole($role)
    {
        if (!self::isLoggedIn()) {
            return false;
        }
        
        return $_SESSION['user_role'] === $role;
    }
    
    /**
     * Require login to access a page
     * Redirects to login page if not logged in
     *
     * @return void
     */
    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            flash('login_required', 'Please log in to access this page', 'alert alert-danger');
            header('Location: ' . URL_ROOT . '/users/login');
            exit;
        }
    }
    
    /**
     * Require admin role to access a page
     * Redirects to home page if not an admin
     *
     * @return void
     */
    public static function requireAdmin()
    {
        self::requireLogin();
        
        if (!self::hasRole(ADMIN_ROLE)) {
            flash('access_denied', 'You do not have permission to access this page', 'alert alert-danger');
            header('Location: ' . URL_ROOT);
            exit;
        }
    }
    
    /**
     * Require agent role to access a page
     * Redirects to home page if not an agent
     *
     * @return void
     */
    public static function requireAgent()
    {
        self::requireLogin();
        
        if (!self::hasRole(AGENT_ROLE) && !self::hasRole(ADMIN_ROLE)) {
            flash('access_denied', 'You do not have permission to access this page', 'alert alert-danger');
            header('Location: ' . URL_ROOT);
            exit;
        }
    }
}
