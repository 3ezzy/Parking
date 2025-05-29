<?php
/**
 * Configuration file for the Parking Management System
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'parking_management');

// URL configuration
define('URL_ROOT', 'http://localhost/Parking');
define('SITE_NAME', 'Parking Management System');

// App configuration
define('APP_VERSION', '1.0.0');
define('ADMIN_ROLE', 'admin');
define('AGENT_ROLE', 'agent');

// Parking configuration
define('STANDARD_HOURLY_RATE', 2.50);  // Standard hourly rate in currency
define('VIP_HOURLY_RATE', 5.00);       // VIP hourly rate in currency
define('HANDICAP_HOURLY_RATE', 1.50);  // Handicap hourly rate in currency
define('RESERVATION_PENALTY', 10.00);  // Penalty for not showing up after reservation
