<?php
/**
 * Session Helper Functions
 * Manage user sessions and authentication
 */

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === ROLE_ADMIN;
}

/**
 * Get current user ID
 * @return int|null
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 * @return string|null
 */
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Set user session
 * @param array $user User data
 */
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
}

/**
 * Destroy user session (logout)
 */
function destroyUserSession() {
    session_unset();
    session_destroy();
}

/**
 * Redirect to login if not authenticated
 * @param string $redirect_url URL to redirect after login
 */
function requireLogin($redirect_url = '') {
    if (!isLoggedIn()) {
        $url = '/Bio-Organic-Herbal-Solutions/public/login.php';
        if ($redirect_url) {
            $url .= '?redirect=' . urlencode($redirect_url);
        }
        header('Location: ' . $url);
        exit();
    }
}

/**
 * Redirect to admin login if not admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /Bio-Organic-Herbal-Solutions/admin/login.php');
        exit();
    }
}

/**
 * Set flash message
 * @param string $type success|error|warning|info
 * @param string $message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Initialize cart if not exists
 */
function initCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

/**
 * Get cart items count
 * @return int
 */
function getCartCount() {
    initCart();
    return count($_SESSION['cart']);
}

/**
 * Get cart total
 * @return float
 */
function getCartTotal() {
    initCart();
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}
?>
