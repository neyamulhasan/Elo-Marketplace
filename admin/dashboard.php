<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Product.php';
require_once '../app/models/Order.php';
require_once '../app/models/User.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

$productModel = new Product($db);
$orderModel = new Order($db);
$userModel = new User($db);

// Get statistics
$total_products = $productModel->getTotalCount();
$total_orders = $orderModel->getTotalCount();
$recent_orders = $orderModel->getAll(10);

$page_title = "Dashboard";
include 'includes/header.php';
?>

<h2 class="mb-4">Dashboard</h2>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Products</h6>
                        <h2 class="mb-0"><?php echo $total_products; ?></h2>
                    </div>
                    <i class="bi bi-box-seam" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Orders</h6>
                        <h2 class="mb-0"><?php echo $total_orders; ?></h2>
                    </div>
                    <i class="bi bi-cart-check" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Pending Orders</h6>
                        <h2 class="mb-0">
                            <?php 
                            $pending = array_filter($recent_orders, function($o) { return $o['status'] == 'pending'; });
                            echo count($pending); 
                            ?>
                        </h2>
                    </div>
                    <i class="bi bi-clock-history" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Recent Orders</h5>
    </div>
    <div class="card-body">
        <?php if (empty($recent_orders)): ?>
            <p class="text-muted">No orders yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo e($order['user_name']); ?></td>
                            <td><?php echo formatPrice($order['total']); ?></td>
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
                            <td><?php echo formatDate($order['created_at']); ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-success">
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
</div>

<?php include 'includes/footer.php'; ?>
