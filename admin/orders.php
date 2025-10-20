<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Order.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$orderModel = new Order($db);

$orders = $orderModel->getAll();

$page_title = "Orders";
include 'includes/header.php';
?>

<h2 class="mb-4">Orders Management</h2>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo e($order['user_name']); ?></td>
                        <td><?php echo formatPrice($order['total']); ?></td>
                        <td><?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></td>
                        <td>
                            <form action="/Bio-Organic-Herbal-Solutions/admin/order-action.php" method="POST" class="d-inline">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo formatDate($order['created_at']); ?></td>
                        <td>
                            <a href="/Bio-Organic-Herbal-Solutions/admin/order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
