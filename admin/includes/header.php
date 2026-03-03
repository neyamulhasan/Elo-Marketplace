<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Admin Panel</title>
    <link href="<?= BASE_URL ?>/public/assets/css/vendor/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/vendor/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #28a745;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3 text-white">
                    <div class="d-flex align-items-center mb-2">
                        <img src="<?= BASE_URL ?>/public/assets/images/elo.png" alt="Logo" style="height: 40px; margin-right: 10px; border-radius: 5px;">
                        <h4 class="mb-0">Elo Marketplace</h4>
                    </div>
                    <p class="small mb-0">Admin Panel</p>
                </div>
                <nav class="nav flex-column p-3">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/admin/dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/admin/products.php">
                        <i class="bi bi-box-seam"></i> Products
                    </a>
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/admin/orders.php">
                        <i class="bi bi-cart-check"></i> Orders
                    </a>
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/admin/categories.php">
                        <i class="bi bi-tags"></i> Categories
                    </a>
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/admin/users.php">
                        <i class="bi bi-people"></i> Users
                    </a>
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/admin/settings.php">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                    <hr class="text-white">
                    <a class="nav-link" href="<?= BASE_URL ?>/public/logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <!-- Top Bar -->
                <nav class="navbar navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-text">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                        </span>
                    </div>
                </nav>

                <!-- Flash Messages -->
                <?php
                if (isset($_SESSION['flash_message'])) {
                    $flash = $_SESSION['flash_message'];
                    $alert_class = 'alert-' . ($flash['type'] == 'error' ? 'danger' : $flash['type']);
                    echo '<div class="container-fluid mt-3">';
                    echo '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">';
                    echo htmlspecialchars($flash['message']);
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    echo '</div>';
                    echo '</div>';
                    unset($_SESSION['flash_message']);
                }
                ?>

                <!-- Page Content -->
                <div class="container-fluid p-4">
