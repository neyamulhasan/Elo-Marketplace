<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Elo Marketplace - Your Online Shopping Destination">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Elo Marketplace</title>
    
    <!-- Bootstrap 5 CSS (Local) -->
    <link href="<?= BASE_URL ?>/public/assets/css/vendor/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (Local) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/vendor/bootstrap-icons.css">
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <?php
    // Load categories for "Shop by category" dropdown if not already available
    if (!isset($nav_categories)) {
        if (isset($db)) {
            if (!class_exists('Category')) require_once ROOT_PATH . '/app/models/Category.php';
            $_navCatModel = new Category($db);
            $nav_categories = $_navCatModel->getAll();
        } else {
            $nav_categories = [];
        }
    }
    $cart_count = (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) ? count($_SESSION['cart']) : 0;
    $search_val = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
    ?>
    <nav class="navbar bg-white sticky-top d-block" style="border-bottom: 1px solid #e5e5e5; padding: 0;">

        <!-- Top row -->
        <div class="container d-flex align-items-center" style="gap: 10px; padding-top: 8px; padding-bottom: 8px; flex-wrap: nowrap;">

            <!-- Brand -->
            <a class="navbar-brand fw-bold d-flex align-items-center flex-shrink-0 me-0" href="<?= BASE_URL ?>/public/" style="color:#191919; letter-spacing:-0.5px;">
                <img src="<?= BASE_URL ?>/public/assets/images/elo.png" alt="Elo Marketplace" style="height:36px;">
            </a>

            <!-- Shop by category (desktop) -->
            <div class="dropdown d-none d-lg-block flex-shrink-0" style="border-right:1px solid #ddd; padding-right:14px;">
                <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown"
                   style="font-size:0.75rem; line-height:1.3; color:#555; white-space:nowrap;">
                    Shop by<br><strong style="color:#191919; font-size:0.82rem;">category</strong>
                </a>
                <ul class="dropdown-menu shadow-sm" style="min-width:180px; border-radius:10px; border:1px solid #e5e5e5;">
                    <li>
                        <a class="dropdown-item" href="<?= BASE_URL ?>/public/shop.php"
                           style="font-size:0.88rem; color:#191919; font-weight:600;">
                            <i class="bi bi-grid me-2"></i>All Products
                        </a>
                    </li>
                    <?php if (!empty($nav_categories)): ?>
                        <li><hr class="dropdown-divider my-1"></li>
                        <?php foreach ($nav_categories as $cat): ?>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/public/shop.php?category=<?= (int)$cat['id'] ?>"
                                   style="font-size:0.85rem; color:#333;">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Search bar (desktop) -->
            <form class="flex-grow-1 d-none d-lg-flex" action="<?= BASE_URL ?>/public/shop.php" method="GET" role="search">
                <div class="input-group navbar-search-bar w-100">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search for anything"
                           value="<?= $search_val ?>"
                           style="padding: 8px 16px;">
                    <button class="btn navbar-search-btn" type="submit">
                        <i class="bi bi-search" style="font-size:0.95rem;"></i>
                    </button>
                </div>
            </form>

            <!-- Right side: Home, Contact, Cart, Account (desktop) -->
            <div class="d-none d-lg-flex align-items-center gap-1 flex-shrink-0">
                <a href="<?= BASE_URL ?>/public/" class="nav-text-link">Home</a>
                <a href="<?= BASE_URL ?>/public/shop.php" class="nav-text-link">Shop</a>
                <a href="<?= BASE_URL ?>/public/contact.php" class="nav-text-link">Contact</a>

                <!-- Cart -->
                <a href="<?= BASE_URL ?>/public/cart.php" class="nav-icon-link position-relative" title="Cart">
                    <i class="bi bi-cart2" style="font-size:1.35rem;"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                              style="background:#e53238; font-size:0.6rem; padding:3px 5px;"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>

                <!-- Account -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a class="nav-icon-link dropdown-toggle d-flex align-items-center gap-1" href="#"
                           id="userDropdownDesktop" role="button" data-bs-toggle="dropdown" style="text-decoration:none;">
                            <i class="bi bi-person-circle" style="font-size:1.3rem;"></i>
                            <span style="font-size:0.82rem; font-weight:600; max-width:80px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">
                                <?= htmlspecialchars($_SESSION['user_name']) ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:10px; border:1px solid #e5e5e5;">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/public/account.php"><i class="bi bi-person me-2"></i>My Account</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/public/orders.php"><i class="bi bi-box me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/public/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/public/login.php" class="btn btn-success btn-sm ms-1"
                       style="font-size:0.82rem; padding:6px 16px;">Sign in</a>
                <?php endif; ?>
            </div>

            <!-- Mobile: search bar + cart + toggler -->
            <form class="d-lg-none" action="<?= BASE_URL ?>/public/shop.php" method="GET" role="search" style="flex: 1 1 0; min-width: 0;">
                <div class="input-group navbar-search-bar">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search..."
                           value="<?= $search_val ?>"
                           style="padding:7px 14px; font-size:0.88rem; background:#f6f6f6;">
                    <button class="btn navbar-search-btn" type="submit">
                        <i class="bi bi-search" style="font-size:0.9rem;"></i>
                    </button>
                </div>
            </form>
            <div class="d-lg-none d-flex align-items-center flex-shrink-0 gap-2">
                <a href="<?= BASE_URL ?>/public/cart.php" class="nav-icon-link position-relative">
                    <i class="bi bi-cart2" style="font-size:1.3rem;"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                              style="background:#e53238; font-size:0.6rem;"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
                <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMobile"
                        style="font-size:1.2rem; color:#191919;">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div><!-- end top row container -->

        <!-- Mobile collapsible nav -->
        <div class="collapse" id="navbarMobile" style="border-top:1px solid #f0f0f0;">
            <div class="container py-2">
                <!-- Shop by category -->
                <a class="d-flex align-items-center justify-content-between py-2 fw-semibold"
                   href="#mobileCats" data-bs-toggle="collapse" role="button"
                   style="color:#191919; text-decoration:none; font-size:0.9rem;">
                    <span><i class="bi bi-grid-3x3-gap me-2"></i>Shop by category</span>
                    <i class="bi bi-chevron-down" style="font-size:0.75rem;"></i>
                </a>
                <div class="collapse ms-1 mb-1" id="mobileCats">
                    <a class="d-block py-1" href="<?= BASE_URL ?>/public/shop.php" style="color:#555; text-decoration:none; font-size:0.88rem;">All Products</a>
                    <?php foreach ($nav_categories as $cat): ?>
                        <a class="d-block py-1" href="<?= BASE_URL ?>/public/shop.php?category=<?= (int)$cat['id'] ?>" style="color:#555; text-decoration:none; font-size:0.88rem;"><?= htmlspecialchars($cat['name']) ?></a>
                    <?php endforeach; ?>
                </div>
                <hr class="my-2" style="border-color:#f0f0f0;">
                <a class="d-block py-2" href="<?= BASE_URL ?>/public/" style="color:#333; text-decoration:none; font-size:0.9rem;">Home</a>
                <a class="d-block py-2" href="<?= BASE_URL ?>/public/shop.php" style="color:#333; text-decoration:none; font-size:0.9rem;">Shop</a>
                <a class="d-block py-2" href="<?= BASE_URL ?>/public/contact.php" style="color:#333; text-decoration:none; font-size:0.9rem;">Contact</a>
                <hr class="my-2" style="border-color:#f0f0f0;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="d-block py-2" href="<?= BASE_URL ?>/public/account.php" style="color:#333; text-decoration:none; font-size:0.9rem;"><i class="bi bi-person me-2"></i>My Account</a>
                    <a class="d-block py-2" href="<?= BASE_URL ?>/public/orders.php" style="color:#333; text-decoration:none; font-size:0.9rem;"><i class="bi bi-box me-2"></i>My Orders</a>
                    <a class="d-block py-2 text-danger" href="<?= BASE_URL ?>/public/logout.php" style="text-decoration:none; font-size:0.9rem;"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                <?php else: ?>
                    <a class="d-block py-2 fw-semibold" href="<?= BASE_URL ?>/public/login.php" style="color:#191919; text-decoration:none; font-size:0.9rem;">Sign in / Register</a>
                <?php endif; ?>
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
