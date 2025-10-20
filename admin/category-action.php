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

if ($action === 'add') {
    $data = [
        'name' => sanitizeString($_POST['name']),
        'description' => sanitizeString($_POST['description'])
    ];
    
    if ($categoryModel->create($data)) {
        setFlashMessage('success', 'Category added successfully!');
    } else {
        setFlashMessage('error', 'Failed to add category.');
    }
} elseif ($action === 'edit') {
    $id = intval($_POST['id']);
    $data = [
        'name' => sanitizeString($_POST['name']),
        'description' => sanitizeString($_POST['description'])
    ];
    
    if ($categoryModel->update($id, $data)) {
        setFlashMessage('success', 'Category updated successfully!');
    } else {
        setFlashMessage('error', 'Failed to update category.');
    }
} elseif ($action === 'delete') {
    $id = intval($_POST['id']);
    
    if ($categoryModel->delete($id)) {
        setFlashMessage('success', 'Category deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete category. It may have products associated with it.');
    }
}

header('Location: /Bio-Organic-Herbal-Solutions/admin/categories.php');
exit();
?>
