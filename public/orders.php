<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Order.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$orderModel = new Order($db);

$orders = $orderModel->getByUser(getUserId());

$page_title = "My Orders";
include 'includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">My Orders</h2>
    
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> You haven't placed any orders yet.
            <a href="/Bio-Organic-Herbal-Solutions/public/shop.php" class="alert-link">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo formatDate($order['created_at']); ?></td>
                        <td><?php echo formatPrice($order['total']); ?></td>
                        <td><?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></td>
                        <td>
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
                        </td>
                        <td>
                            <a href="/Bio-Organic-Herbal-Solutions/public/order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
