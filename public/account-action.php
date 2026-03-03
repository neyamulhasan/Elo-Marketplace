<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/User.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = "Please login to access your account";
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

$action = $_POST['action'] ?? '';

// Update Profile
if ($action === 'update_profile') {
    $name = sanitizeString($_POST['name']);
    $email = sanitizeEmail($_POST['email']);
    $phone = sanitizeString($_POST['phone'] ?? '');
    $address = sanitizeString($_POST['address'] ?? '');

    // Validate email
    if (!validateEmail($email)) {
        $_SESSION['error'] = "Invalid email address";
        header('Location: ' . BASE_URL . '/public/account.php');
        exit();
    }

    // Check if email is already taken by another user
    $existingUser = $userModel->getByEmail($email);
    if ($existingUser && $existingUser['id'] != $_SESSION['user_id']) {
        $_SESSION['error'] = "Email already registered to another account";
        header('Location: ' . BASE_URL . '/public/account.php');
        exit();
    }

    // Update user
    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address
    ];

    if ($userModel->updateProfile($_SESSION['user_id'], $data)) {
        $_SESSION['user_name'] = $name;
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update profile";
    }

    header('Location: ' . BASE_URL . '/public/account.php');
    exit();
}

// Change Password
if ($action === 'change_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Get current user
    $user = $userModel->getUserById($_SESSION['user_id']);

    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['error'] = "Current password is incorrect";
        header('Location: ' . BASE_URL . '/public/account.php#password');
        exit();
    }

    // Validate new password
    if (strlen($new_password) < 6) {
        $_SESSION['error'] = "New password must be at least 6 characters";
        header('Location: ' . BASE_URL . '/public/account.php#password');
        exit();
    }

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match";
        header('Location: ' . BASE_URL . '/public/account.php#password');
        exit();
    }

    // Update password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    if ($userModel->updatePassword($_SESSION['user_id'], $hashed_password)) {
        $_SESSION['success'] = "Password changed successfully!";
    } else {
        $_SESSION['error'] = "Failed to change password";
    }

    header('Location: ' . BASE_URL . '/public/account.php#password');
    exit();
}

// Invalid action
$_SESSION['error'] = "Invalid action";
header('Location: ' . BASE_URL . '/public/account.php');
exit();
