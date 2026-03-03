<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Order.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$orderModel = new Order($db);

// Pagination settings
$items_per_page = 30;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page); // Ensure page is at least 1

$all_orders = $orderModel->getAll();
$total_orders = count($all_orders);
$total_pages = ceil($total_orders / $items_per_page);
$current_page = min($current_page, max(1, $total_pages)); // Ensure page doesn't exceed total pages

$offset = ($current_page - 1) * $items_per_page;
$orders = array_slice($all_orders, $offset, $items_per_page);

$page_title = "Orders";
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Orders Management</h2>
    <div>
        <span class="badge bg-primary">Total Orders: <?php echo $total_orders; ?></span>
        <span class="badge bg-secondary ms-2">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
    </div>
</div>

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
                            <form action="<?= BASE_URL ?>/admin/order-action.php" method="POST" class="d-inline">
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
                            <a href="<?= BASE_URL ?>/admin/order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($total_pages > 1): ?>
    <div class="card-footer bg-white">
        <nav aria-label="Orders pagination">
            <ul class="pagination justify-content-center mb-0">
                <!-- Previous Button -->
                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>

                <?php
                // Show page numbers
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);

                if ($start_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=1">1</a>
                    </li>
                    <?php if ($start_page > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif;
                endif;

                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor;

                if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
                    </li>
                <?php endif; ?>

                <!-- Next Button -->
                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
