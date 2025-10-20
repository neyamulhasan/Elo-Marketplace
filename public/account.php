<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/User.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = "Please login to access your account";
    header('Location: /Bio-Organic-Herbal-Solutions/public/login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

// Get current user data
$user = $userModel->getUserById($_SESSION['user_id']);

$page_title = "My Account";
include 'includes/header.php';
?>

<main class="flex-fill">
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">My Account</h2>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 mb-4">
                <div class="list-group">
                    <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                        <i class="bi bi-person"></i> Profile
                    </a>
                    <a href="#password" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-key"></i> Change Password
                    </a>
                    <a href="/Bio-Organic-Herbal-Solutions/public/logout.php" class="list-group-item list-group-item-action text-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="col-md-9">
                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-person-circle"></i> Profile Information</h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <?php 
                                        echo $_SESSION['success'];
                                        unset($_SESSION['success']);
                                        ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <?php 
                                        echo $_SESSION['error'];
                                        unset($_SESSION['error']);
                                        ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <form action="/Bio-Organic-Herbal-Solutions/public/account-action.php" method="POST">
                                    <input type="hidden" name="action" value="update_profile">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" value="<?php echo e($user['name']); ?>" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email" value="<?php echo e($user['email']); ?>" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" name="phone" value="<?php echo e($user['phone']); ?>">
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" name="address" rows="3"><?php echo e($user['address']); ?></textarea>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-save"></i> Update Profile
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Account Details -->
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Account Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Account Type:</strong> <span class="badge bg-primary"><?php echo $user['role'] === 'admin' ? 'Administrator' : 'Customer'; ?></span></p>
                                        <p><strong>Member Since:</strong> <?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Account Status:</strong> <span class="badge bg-success">Active</span></p>
                                        <p><strong>Total Orders:</strong> <?php 
                                        // Get order count
                                        $orderQuery = "SELECT COUNT(*) as count FROM orders WHERE user_id = :user_id";
                                        $stmt = $db->prepare($orderQuery);
                                        $stmt->bindParam(':user_id', $_SESSION['user_id']);
                                        $stmt->execute();
                                        $orderCount = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo $orderCount['count'];
                                        ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="password">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-shield-lock"></i> Change Password</h5>
                            </div>
                            <div class="card-body">
                                <form action="/Bio-Organic-Herbal-Solutions/public/account-action.php" method="POST">
                                    <input type="hidden" name="action" value="change_password">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="new_password" minlength="6" required>
                                        <small class="text-muted">Minimum 6 characters</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="confirm_password" minlength="6" required>
                                    </div>

                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-key"></i> Change Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
