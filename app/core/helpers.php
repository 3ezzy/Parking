<?php
/**
 * Helper functions for the application
 */

/**
 * Redirect to a specific URL
 *
 * @param string $url The URL to redirect to
 * @return void
 */
function redirect($url)
{
    header('Location: ' . URL_ROOT . '/' . $url);
    exit;
}

/**
 * Display flash message
 *
 * @param string $name The message name
 * @param string $message The message text
 * @param string $class The CSS class for styling
 * @return void
 */
function flash($name = '', $message = '', $class = 'alert alert-success')
{
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            if (!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
            
            if (!empty($_SESSION[$name . '_class'])) {
                unset($_SESSION[$name . '_class']);
            }
            
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

/**
 * Check if the current user is logged in
 *
 * @return boolean True if logged in, false otherwise
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Check if the current user has the specified role
 *
 * @param string $role The role to check
 * @return boolean True if the user has the role, false otherwise
 */
function hasRole($role)
{
    if (!isLoggedIn()) {
        return false;
    }
    
    return $_SESSION['user_role'] === $role;
}

/**
 * Sanitize input data
 *
 * @param mixed $data The data to sanitize
 * @return mixed The sanitized data
 */
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generate a CSRF token
 *
 * @return string The CSRF token
 */
function generateCsrfToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify a CSRF token
 *
 * @param string $token The token to verify
 * @return boolean True if valid, false otherwise
 */
function verifyCsrfToken($token)
{
    if (!isset($_SESSION['csrf_token']) || !$token) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format a date
 *
 * @param string $date The date to format
 * @param string $format The format string
 * @return string The formatted date
 */
function formatDate($date, $format = 'd/m/Y H:i')
{
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Calculate price based on duration and rates
 *
 * @param string $startTime The start time
 * @param string $endTime The end time (or current time if still parked)
 * @param float $hourlyRate The hourly rate
 * @return float The calculated price
 */
function calculatePrice($startTime, $endTime, $hourlyRate)
{
    $start = new DateTime($startTime);
    $end = new DateTime($endTime);
    $diff = $start->diff($end);
    
    // Calculate hours (including partial hours)
    $hours = $diff->h + ($diff->i / 60);
    $days = $diff->d;
    
    $totalHours = $days * 24 + $hours;
    
    // Calculate price
    return round($totalHours * $hourlyRate, 2);
}
