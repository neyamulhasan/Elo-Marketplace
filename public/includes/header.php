<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bio-Organic Herbal Solutions - Natural and Organic Herbal Products">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Bio-Organic Herbal Solutions</title>
    
    <!-- Bootstrap 5 CSS (Local) -->
    <link href="/Bio-Organic-Herbal-Solutions/public/assets/css/vendor/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (Local) -->
    <link rel="stylesheet" href="/Bio-Organic-Herbal-Solutions/public/assets/css/vendor/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/Bio-Organic-Herbal-Solutions/public/assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-success d-flex align-items-center" href="/Bio-Organic-Herbal-Solutions/public/">
                <img src="/Bio-Organic-Herbal-Solutions/public/assets/images/bio.jpg" alt="Bio-Organic Herbal Solutions" style="height: 40px; margin-right: 10px;">
                Bio-Organic Herbal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/Bio-Organic-Herbal-Solutions/public/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Bio-Organic-Herbal-Solutions/public/shop.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Bio-Organic-Herbal-Solutions/public/contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Bio-Organic-Herbal-Solutions/public/cart.php">
                            <i class="bi bi-cart"></i> Cart 
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="badge bg-success"><?php echo count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/Bio-Organic-Herbal-Solutions/public/account.php">My Account</a></li>
                                <li><a class="dropdown-item" href="/Bio-Organic-Herbal-Solutions/public/orders.php">My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/Bio-Organic-Herbal-Solutions/public/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/Bio-Organic-Herbal-Solutions/public/login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        $alert_class = 'alert-' . ($flash['type'] == 'error' ? 'danger' : $flash['type']);
        echo '<div class="container mt-3">';
        echo '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        echo '</div>';
        unset($_SESSION['flash_message']);
    }
    ?>
