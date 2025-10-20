<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/helpers/image_helper.php';
require_once '../app/models/Product.php';
require_once '../app/models/Category.php';

$database = new Database();
$db = $database->getConnection();

$productModel = new Product($db);
$categoryModel = new Category($db);

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

<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Categories</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/Bio-Organic-Herbal-Solutions/public/shop.php" 
                       class="list-group-item list-group-item-action <?php echo !$category_id ? 'active' : ''; ?>">
                        All Products
                    </a>
                    <?php foreach ($categories as $category): ?>
                    <a href="/Bio-Organic-Herbal-Solutions/public/shop.php?category=<?php echo $category['id']; ?>" 
                       class="list-group-item list-group-item-action <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                        <?php echo e($category['name']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Search Box -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Search Products</h5>
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo e($search); ?>">
                            <button class="btn btn-success" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Products</h2>
                <p class="text-muted mb-0"><?php echo count($products); ?> products found</p>
            </div>

            <?php if (count($products) > 0): ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 product-card">
                        <img src="<?php echo e(getProductPrimaryImage($product)); ?>" 
                             class="card-img-top" 
                             alt="<?php echo e($product['name']); ?>"
                             style="height: 250px; object-fit: cover;"
                             onerror="this.src='/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-product.svg'">
                        <div class="card-body">
                            <span class="badge bg-success mb-2"><?php echo e($product['category_name']); ?></span>
                            <h6 class="card-title"><?php echo e($product['name']); ?></h6>
                            <p class="card-text small text-muted"><?php echo substr(e($product['description']), 0, 80) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-success fw-bold fs-5"><?php echo formatPrice($product['price']); ?></span>
                                <?php if ($product['stock'] > 0): ?>
                                    <span class="badge bg-success">In Stock</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <div class="d-flex gap-2">
                                <a href="/Bio-Organic-Herbal-Solutions/public/product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-success btn-sm flex-grow-1">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <?php if ($product['stock'] > 0): ?>
                                <button class="btn btn-success btn-sm flex-grow-1 add-to-cart" data-id="<?php echo $product['id']; ?>">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
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
