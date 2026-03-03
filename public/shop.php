<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/helpers/image_helper.php';
require_once '../app/models/Product.php';
require_once '../app/models/Category.php';
require_once '../app/models/Setting.php';

$database = new Database();
$db = $database->getConnection();

$productModel = new Product($db);
$categoryModel = new Category($db);
$settingModel = new Setting($db);

// Get default category logo
$default_category_logo = $settingModel->get('default_category_logo') ?? BASE_URL . '/public/assets/images/elo.png';

// Get filters
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$search = isset($_GET['search']) ? sanitizeString($_GET['search']) : '';

// Get products based on filters
if ($search) {
    $products = $productModel->search($search);
} elseif ($category_id) {
    $products = $productModel->getByCategory($category_id);
} else {
    $products = $productModel->getAll();
}

$categories = $categoryModel->getAll();

$page_title = "Shop";
include 'includes/header.php';
?>

<div class="container my-4">
    <div class="row">

        <!-- Category Sidebar -->
        <div class="col-lg-2 d-none d-lg-block">
            <div class="shop-cat-sidebar">
                <p class="fw-bold mb-3" style="font-size:0.95rem; color:#191919;">Shop by category</p>
                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="<?= BASE_URL ?>/public/shop.php<?= $search ? '?search='.urlencode($search) : '' ?>"
                           class="shop-cat-link <?= !$category_id ? 'active' : '' ?>">
                            All Products
                        </a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/public/shop.php?category=<?= $category['id'] ?><?= $search ? '&search='.urlencode($search) : '' ?>"
                           class="shop-cat-link <?= $category_id == $category['id'] ? 'active' : '' ?>">
                            <?= e($category['name']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Products -->
        <div class="col-lg-10">

            <!-- Mobile category pills -->
            <div class="d-lg-none shop-filter-wrap mb-3">
                <div class="shop-filter-bar">
                    <a href="<?= BASE_URL ?>/public/shop.php<?= $search ? '?search='.urlencode($search) : '' ?>"
                       class="shop-filter-pill <?= !$category_id ? 'active' : '' ?>">All</a>
                    <?php foreach ($categories as $category): ?>
                        <a href="<?= BASE_URL ?>/public/shop.php?category=<?= $category['id'] ?><?= $search ? '&search='.urlencode($search) : '' ?>"
                           class="shop-filter-pill <?= $category_id == $category['id'] ? 'active' : '' ?>">
                            <?= e($category['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Results count -->
            <p class="text-muted mb-3" style="font-size:0.88rem;"><?= count($products) ?> results</p>

            <?php if (count($products) > 0): ?>
            <div class="row g-3">
                <?php foreach ($products as $product): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="<?= BASE_URL ?>/public/product.php?id=<?= $product['id'] ?>" class="text-decoration-none text-dark">
                        <div class="card h-100 product-card d-flex flex-column">
                            <div class="product-image-container">
                                <img src="<?= e(getProductPrimaryImage($product)) ?>"
                                     class="product-image"
                                     alt="<?= e($product['name']) ?>"
                                     onerror="this.src='<?= BASE_URL ?>/public/assets/images/placeholder-product.svg'">
                            </div>
                            <div class="card-body p-2 p-md-3 flex-grow-1">
                                <div class="mb-1 d-flex flex-wrap gap-1">
                                    <span class="badge bg-success small"><?= e($product['category_name']) ?></span>
                                    <?php if (hasDiscount($product['discount_percentage'])): ?>
                                        <span class="badge bg-danger small"><?= number_format($product['discount_percentage'], 0) ?>% OFF</span>
                                    <?php endif; ?>
                                </div>
                                <h6 class="card-title small mb-2"><?= e($product['name']) ?></h6>
                                <div class="mb-2">
                                    <?php if (hasDiscount($product['discount_percentage'])): ?>
                                        <p class="text-muted text-decoration-line-through small mb-0"><?= formatPrice($product['price']) ?></p>
                                        <p class="text-success fw-bold mb-0"><?= formatPrice(calculateDiscountedPrice($product['price'], $product['discount_percentage'])) ?></p>
                                    <?php else: ?>
                                        <p class="text-success fw-bold mb-0"><?= formatPrice($product['price']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php if ($product['stock'] <= 0): ?>
                                    <span class="badge bg-danger small">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent border-0 p-2 p-md-3 mt-auto">
                                <?php if ($product['stock'] > 0): ?>
                                    <button class="btn btn-success btn-sm w-100 add-to-cart" data-id="<?= $product['id'] ?>" onclick="event.preventDefault(); event.stopPropagation();">
                                        <i class="bi bi-cart-plus"></i> <span class="d-none d-md-inline">Add to Cart</span><span class="d-inline d-md-none">Add</span>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm w-100" disabled>
                                        <i class="bi bi-x-circle"></i> Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No products found.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
