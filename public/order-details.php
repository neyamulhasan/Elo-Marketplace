<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Order.php';

requireLogin();

if (!isset($_GET['id'])) {
    header('Location: /Bio-Organic-Herbal-Solutions/public/orders.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$orderModel = new Order($db);

$order = $orderModel->getById($_GET['id']);

// Check if order exists and belongs to user
if (!$order || $order['user_id'] != getUserId()) {
    header('Location: /Bio-Organic-Herbal-Solutions/public/orders.php');
    exit();
}

$order_items = $orderModel->getItems($order['id']);

$page_title = "Order Details";
include 'includes/header.php';
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order Details #<?php echo $order['id']; ?></h2>
        <a href="/Bio-Organic-Herbal-Solutions/public/orders.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo e($item['product_name']); ?></td>
                                <td><?php echo formatPrice($item['price']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th><?php echo formatPrice($order['total']); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Order Date:</strong><br><?php echo formatDate($order['created_at']); ?></p>
                    <p><strong>Payment Method:</strong><br><?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></p>
                    <p><strong>Status:</strong><br>
                        <?php
                        $status_colors = [
                            'pending' => 'warning',
                            'processing' => 'info',
                            'delivered' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $badge_color = $status_colors[$order['status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?php echo $badge_color; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </p>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Shipping Address</h5>
                </div>
                <div class="card-body">
                    <p><?php echo nl2br(e($order['shipping_address'])); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
