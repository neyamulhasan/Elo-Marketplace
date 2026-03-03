<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Category.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$categoryModel = new Category($db);

$action = $_POST['action'] ?? '';

// Helper function to handle category image upload
function handleCategoryImageUpload($file) {
    $upload_dir = '../public/uploads/categories/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0775, true);
    }
    chmod($upload_dir, 0775);
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Image must be JPG, PNG, GIF, or WebP format.'];
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Image must be less than 5MB.'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'category_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $relative_path = BASE_URL . '/public/uploads/categories/' . $filename;
        return ['success' => true, 'path' => $relative_path];
    }
    
    return ['success' => false, 'error' => 'Failed to upload image.'];
}

// Helper function to delete category image
function deleteCategoryImage($imagePath) {
    if ($imagePath && strpos($imagePath, '/uploads/categories/') !== false) {
        $file = '../public' . str_replace(BASE_URL . '/public', '', $imagePath);
        if (file_exists($file)) {
            @unlink($file);
        }
    }
}

if ($action === 'add') {
    $imagePath = null;
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleCategoryImageUpload($_FILES['image']);
        if ($uploadResult['success']) {
            $imagePath = $uploadResult['path'];
        } else {
            setFlashMessage('error', $uploadResult['error']);
            header('Location: ' . BASE_URL . '/admin/categories.php');
            exit();
        }
    }
    
    $data = [
        'name' => sanitizeString($_POST['name']),
        'description' => sanitizeString($_POST['description']),
        'image' => $imagePath
    ];
    
    if ($categoryModel->create($data)) {
        setFlashMessage('success', 'Category added successfully!');
    } else {
        setFlashMessage('error', 'Failed to add category.');
        if ($imagePath) {
            deleteCategoryImage($imagePath);
        }
    }
} elseif ($action === 'edit') {
    $id = intval($_POST['id']);
    $existingImage = $_POST['existing_image'] ?? null;
    $imagePath = $existingImage;
    
    // Check if delete image checkbox is checked
    if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
        deleteCategoryImage($existingImage);
        $imagePath = null;
    }
    
    // Handle new image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleCategoryImageUpload($_FILES['image']);
        if ($uploadResult['success']) {
            // Delete old image if exists
            if ($existingImage) {
                deleteCategoryImage($existingImage);
            }
            $imagePath = $uploadResult['path'];
        } else {
            setFlashMessage('error', $uploadResult['error']);
            header('Location: ' . BASE_URL . '/admin/categories.php');
            exit();
        }
    }
    
    $data = [
        'name' => sanitizeString($_POST['name']),
        'description' => sanitizeString($_POST['description']),
        'image' => $imagePath
    ];
    
    if ($categoryModel->update($id, $data)) {
        setFlashMessage('success', 'Category updated successfully!');
    } else {
        setFlashMessage('error', 'Failed to update category.');
    }
} elseif ($action === 'delete') {
    $id = intval($_POST['id']);
    
    // Get category to delete its image
    $category = $categoryModel->getById($id);
    if ($category && $category['image']) {
        deleteCategoryImage($category['image']);
    }
    
    if ($categoryModel->delete($id)) {
        setFlashMessage('success', 'Category deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete category. It may have products associated with it.');
    }
}

header('Location: ' . BASE_URL . '/admin/categories.php');
exit();
?>
