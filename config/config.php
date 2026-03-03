<?php
/**
 * Global Configuration
 * Application-wide constants and settings
 */

// Auto-detect BASE_URL (works on localhost, VPS, and shared hosting with any folder name)
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $docRoot = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__))), '/');
    $projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
    $subfolder = str_replace($docRoot, '', $projectRoot);
    define('BASE_URL', $protocol . '://' . $host . $subfolder);
}

// Site Information
define('SITE_NAME', 'Bio-Organic Herbal Solutions');
define('SITE_URL', BASE_URL);
define('SITE_EMAIL', 'info@bioorganic.com');

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
