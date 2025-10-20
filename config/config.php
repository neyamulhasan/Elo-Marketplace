<?php
/**
 * Global Configuration
 * Application-wide constants and settings
 */

// Site Information
define('SITE_NAME', 'Bio-Organic Herbal Solutions');
define('SITE_URL', 'http://localhost/Bio-Organic-Herbal-Solutions');
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
