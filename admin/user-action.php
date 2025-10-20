<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/models/User.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $status = intval($_POST['status']);
    
    if ($userModel->updateStatus($user_id, $status)) {
        setFlashMessage('success', 'User status updated successfully!');
    } else {
        setFlashMessage('error', 'Failed to update user status.');
    }
}

header('Location: /Bio-Organic-Herbal-Solutions/admin/users.php');
exit();
?>
