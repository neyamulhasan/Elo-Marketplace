<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/models/User.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

$users = $userModel->getAllUsers();

$page_title = "Users";
include 'includes/header.php';
?>

<h2 class="mb-4">Users Management</h2>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo e($user['name']); ?></td>
                        <td><?php echo e($user['email']); ?></td>
                        <td><?php echo e($user['phone']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $user['status'] ? 'success' : 'danger'; ?>">
                                <?php echo $user['status'] ? 'Active' : 'Blocked'; ?>
                            </span>
                        </td>
                        <td><?php echo formatDate($user['created_at']); ?></td>
                        <td>
                            <?php if ($user['role'] != 'admin'): ?>
                            <form action="/Bio-Organic-Herbal-Solutions/admin/user-action.php" method="POST" class="d-inline">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="status" value="<?php echo $user['status'] ? 0 : 1; ?>">
                                <button type="submit" class="btn btn-sm btn-<?php echo $user['status'] ? 'danger' : 'success'; ?>" 
                                        onclick="return confirm('Change user status?')">
                                    <?php echo $user['status'] ? 'Block' : 'Unblock'; ?>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
