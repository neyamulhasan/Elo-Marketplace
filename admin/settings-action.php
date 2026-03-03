<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/models/Setting.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/settings.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$settingModel = new Setting($db);

$upload_dir = '../public/uploads/settings/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0775, true);
}
chmod($upload_dir, 0775);

$success_count = 0;
$errors = [];
$deleted = [];

// Handle delete actions first
if (isset($_POST['delete_banner'])) {
    $old_image = $settingModel->get('home_banner_image');
    if ($old_image && strpos($old_image, '/uploads/settings/') !== false) {
        $old_file = '../public' . str_replace(BASE_URL . '/public', '', $old_image);
        if (file_exists($old_file)) {
            @unlink($old_file);
        }
    }
    $settingModel->delete('home_banner_image');
    $deleted[] = 'Banner image removed';
}

if (isset($_POST['delete_about'])) {
    $old_image = $settingModel->get('about_us_image');
    if ($old_image && strpos($old_image, '/uploads/settings/') !== false) {
        $old_file = '../public' . str_replace(BASE_URL . '/public', '', $old_image);
        if (file_exists($old_file)) {
            @unlink($old_file);
        }
    }
    $settingModel->delete('about_us_image');
    $deleted[] = 'About Us image removed';
}

// Note: default_category_logo is no longer managed via settings

// Handle Home Banner Image Upload
if (isset($_FILES['home_banner_image']) && $_FILES['home_banner_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['home_banner_image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file['type'], $allowed_types)) {
        $errors[] = 'Banner image must be JPG, PNG, GIF, or WebP format.';
    } elseif ($file['size'] > 5 * 1024 * 1024) {
        $errors[] = 'Banner image must be less than 5MB.';
    } else {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'banner_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $relative_path = BASE_URL . '/public/uploads/settings/' . $filename;
            
            // Delete old image if exists and not default
            $old_image = $settingModel->get('home_banner_image');
            if ($old_image && strpos($old_image, '/uploads/settings/') !== false) {
                $old_file = '../public' . str_replace(BASE_URL . '/public', '', $old_image);
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            $settingModel->set('home_banner_image', $relative_path);
            $success_count++;
        } else {
            $errors[] = 'Failed to upload banner image.';
        }
    }
}

// Handle About Us Image Upload
if (isset($_FILES['about_us_image']) && $_FILES['about_us_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['about_us_image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file['type'], $allowed_types)) {
        $errors[] = 'About Us image must be JPG, PNG, GIF, or WebP format.';
    } elseif ($file['size'] > 5 * 1024 * 1024) {
        $errors[] = 'About Us image must be less than 5MB.';
    } else {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'about_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $relative_path = BASE_URL . '/public/uploads/settings/' . $filename;
            
            // Delete old image if exists and not default
            $old_image = $settingModel->get('about_us_image');
            if ($old_image && strpos($old_image, '/uploads/settings/') !== false) {
                $old_file = '../public' . str_replace(BASE_URL . '/public', '', $old_image);
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            $settingModel->set('about_us_image', $relative_path);
            $success_count++;
        } else {
            $errors[] = 'Failed to upload About Us image.';
        }
    }
}

// No default category logo upload supported

// Set flash messages
if ($success_count > 0 || count($deleted) > 0) {
    $parts = [];
    if ($success_count > 0) $parts[] = "$success_count image(s) uploaded";
    if (count($deleted) > 0) $parts[] = implode(', ', $deleted);
    setFlashMessage('success', 'Settings updated successfully! ' . implode(' | ', $parts));
} elseif (count($errors) > 0) {
    setFlashMessage('error', implode(' ', $errors));
} else {
    setFlashMessage('info', 'No changes were made.');
}

header('Location: ' . BASE_URL . '/admin/settings.php');
exit;
