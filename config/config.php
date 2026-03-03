<?php
/**
 * Global Configuration
 * Application-wide constants and settings
 */

// Site Information
define('SITE_NAME', 'Elo Marketplace');
define('SITE_EMAIL', 'info@elo-marketplace.com');

// Auto-detect Base URL — works on localhost (XAMPP), shared hosting, and VPS
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Strip /admin/ or /public/ (and anything after) to find the project root path
    $script   = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
    $base     = preg_replace('#/(admin|public)(/.*)?\z#', '', $script);
    define('BASE_URL', rtrim($protocol . '://' . $host . $base, '/'));
}
define('SITE_URL', BASE_URL);

// Path Constants
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('ADMIN_PATH', ROOT_PATH . '/admin');

// Database Configuration (imported from database.php)
require_once ROOT_PATH . '/config/database.php';

// Security Settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 6);

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 20);

// Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);

// Order Status
define('ORDER_PENDING', 'pending');
define('ORDER_PROCESSING', 'processing');
define('ORDER_DELIVERED', 'delivered');
define('ORDER_CANCELLED', 'cancelled');

// User Roles
define('ROLE_USER', 'user');
define('ROLE_ADMIN', 'admin');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Dhaka');
?>
