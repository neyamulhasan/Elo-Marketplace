<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/helpers/image_helper.php';
require_once '../app/models/Product.php';

if (!isset($_GET['id'])) {
    header('Location: /Bio-Organic-Herbal-Solutions/public/shop.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$productModel = new Product($db);

$product = $productModel->getById($_GET['id']);

if (!$product) {
    header('Location: /Bio-Organic-Herbal-Solutions/public/shop.php');
    exit();
}

$page_title = $product['name'];
include 'includes/header.php';
?>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/Bio-Organic-Herbal-Solutions/public/">Home</a></li>
            <li class="breadcrumb-item"><a href="/Bio-Organic-Herbal-Solutions/public/shop.php">Shop</a></li>
            <li class="breadcrumb-item active"><?php echo e($product['name']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Image -->
        <div class="col-md-5 mb-4">
            <?php 
            $productImages = getProductImages($product);
            $hasMultiple = hasMultipleImages($product);
            ?>
            
            <?php if ($hasMultiple): ?>
            <!-- Image Slider for Multiple Images -->
            <div class="card">
                <div id="productImageCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($productImages as $index => $image): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo e($image); ?>" 
                                 class="d-block w-100" 
                                 alt="<?php echo e($product['name']); ?>"
                                 style="height: 400px; object-fit: cover;"
                                 onerror="this.src='/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-large.svg'">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Previous/Next Arrows -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#productImageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productImageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    
                    <!-- Image Indicators -->
                    <div class="carousel-indicators">
                        <?php foreach ($productImages as $index => $image): ?>
                        <button type="button" 
                                data-bs-target="#productImageCarousel" 
                                data-bs-slide-to="<?php echo $index; ?>" 
                                class="<?php echo $index === 0 ? 'active' : ''; ?>" 
                                aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                                aria-label="Slide <?php echo $index + 1; ?>">
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Thumbnail Navigation -->
                <div class="card-body">
                    <div class="row g-2">
                        <?php foreach ($productImages as $index => $image): ?>
                        <div class="col-3">
                            <img src="<?php echo e($image); ?>" 
                                 class="img-thumbnail cursor-pointer thumbnail-nav" 
                                 data-bs-target="#productImageCarousel" 
                                 data-bs-slide-to="<?php echo $index; ?>"
                                 alt="Thumbnail <?php echo $index + 1; ?>"
                                 style="height: 80px; object-fit: cover; cursor: pointer;">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Single Image -->
            <div class="card">
                <img src="<?php echo e($productImages[0]); ?>" 
                     class="card-img-top" 
                     alt="<?php echo e($product['name']); ?>"
                     style="height: 400px; object-fit: cover;"
                     onerror="this.src='/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-large.svg'">
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Details -->
        <div class="col-md-7">
            <h1 class="mb-3"><?php echo e($product['name']); ?></h1>
            
            <div class="mb-3">
                <span class="badge bg-success"><?php echo e($product['category_name']); ?></span>
                <?php if ($product['stock'] > 0): ?>
                    <span class="badge bg-success ms-2">In Stock (<?php echo $product['stock']; ?> available)</span>
                <?php else: ?>
                    <span class="badge bg-danger ms-2">Out of Stock</span>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <h3 class="text-success"><?php echo formatPrice($product['price']); ?></h3>
            </div>

            <div class="mb-4">
                <h5>Description</h5>
                <p><?php echo nl2br(e($product['description'])); ?></p>
            </div>

            <div class="mb-3">
                <p class="mb-1"><strong>SKU:</strong> <?php echo e($product['sku']); ?></p>
            </div>

            <?php if ($product['stock'] > 0): ?>
            <form action="/Bio-Organic-Herbal-Solutions/public/cart-action.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="action" value="add">
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                    <a href="/Bio-Organic-Herbal-Solutions/public/shop.php" class="btn btn-outline-secondary btn-lg">
                        Continue Shopping
                    </a>
                </div>
            </form>
            <?php else: ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> This product is currently out of stock.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
