<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/models/Order.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$orderModel = new Order($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    if ($orderModel->updateStatus($order_id, $status)) {
        setFlashMessage('success', 'Order status updated successfully!');
    } else {
        setFlashMessage('error', 'Failed to update order status.');
    }
}

header('Location: /Bio-Organic-Herbal-Solutions/admin/orders.php');
exit();
?>
