<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/User.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

// Pagination settings
$items_per_page = 30;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page); // Ensure page is at least 1

$all_users = $userModel->getAllUsers();
$total_users = count($all_users);
$total_pages = ceil($total_users / $items_per_page);
$current_page = min($current_page, max(1, $total_pages)); // Ensure page doesn't exceed total pages

$offset = ($current_page - 1) * $items_per_page;
$users = array_slice($all_users, $offset, $items_per_page);

$page_title = "Users";
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Users Management</h2>
    <div>
        <span class="badge bg-primary">Total Users: <?php echo $total_users; ?></span>
        <span class="badge bg-secondary ms-2">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
    </div>
</div>

<?php if (count($users) > 0): ?>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-people"></i> Registered Users</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><strong>#<?php echo $user['id']; ?></strong></td>
                        <td>
                            <i class="bi bi-person-circle"></i> 
                            <?php echo e($user['name']); ?>
                        </td>
                        <td>
                            <i class="bi bi-envelope"></i> 
                            <?php echo e($user['email']); ?>
                        </td>
                        <td>
                            <?php if (!empty($user['phone'])): ?>
                                <i class="bi bi-telephone"></i> <?php echo e($user['phone']); ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?>">
                                <i class="bi bi-<?php echo $user['role'] == 'admin' ? 'shield-lock' : 'person'; ?>"></i>
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $user['status'] ? 'success' : 'danger'; ?>">
                                <?php echo $user['status'] ? 'Active' : 'Blocked'; ?>
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                <i class="bi bi-calendar3"></i> 
                                <?php echo formatDate($user['created_at']); ?>
                            </small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($total_pages > 1): ?>
    <div class="card-footer bg-white">
        <nav aria-label="Users pagination">
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
<?php else: ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> No users found in the system.
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
