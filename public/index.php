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

// Get featured products
$featured_products = $productModel->getAll(8);
$categories = $categoryModel->getAll();

// Get site settings
$home_banner_image = $settingModel->get('home_banner_image') ?? '/Bio-Organic-Herbal-Solutions/public/assets/images/hero-image.jpg';
$about_us_image = $settingModel->get('about_us_image') ?? '/Bio-Organic-Herbal-Solutions/public/assets/images/about-image.jpg';

$page_title = "Home";
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-success bg-opacity-10 py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-success mb-3">Natural & Organic Herbal Solutions</h1>
                <p class="lead mb-4">We bring you the finest organic medicines,
Blending ancient wisdom with modern science.</p>
                <a href="/Bio-Organic-Herbal-Solutions/public/shop.php" class="btn btn-success btn-lg">Shop Now</a>
            </div>
            <div class="col-lg-6">
                <img src="<?php echo e($home_banner_image); ?>" alt="Herbal Products" class="img-fluid rounded" onerror="this.src='/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-hero.svg'">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section py-5">
    <div class="container">
        <h2 class="text-center mb-4">Shop by Category</h2>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm category-card">
                    <div class="card-body text-center">
                        <i class="bi bi-flower2 text-success" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3"><?php echo e($category['name']); ?></h5>
                        <a href="/Bio-Organic-Herbal-Solutions/public/shop.php?category=<?php echo $category['id']; ?>" class="btn btn-outline-success btn-sm mt-2">View Products</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Featured Products</h2>
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 product-card">
                    <img src="<?php echo e(getProductPrimaryImage($product)); ?>" 
                         class="card-img-top" 
                         alt="<?php echo e($product['name']); ?>"
                         style="height: 200px; object-fit: cover;"
                         onerror="this.src='/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-product.svg'">
                    <div class="card-body">
                        <h6 class="card-title"><?php echo e($product['name']); ?></h6>
                        <p class="text-success fw-bold"><?php echo formatPrice($product['price']); ?></p>
                        <div class="d-flex justify-content-between">
                            <a href="/Bio-Organic-Herbal-Solutions/public/product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-success">View</a>
                            <button class="btn btn-sm btn-success add-to-cart" data-id="<?php echo $product['id']; ?>">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="mb-3">Why Choose Us?</h2>
                <div class="feature-item mb-3">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <strong>100% Organic:</strong> All our products are certified organic and natural.
                </div>
                <div class="feature-item mb-3">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <strong>Quality Assured:</strong> Rigorous quality control and testing.
                </div>
                <div class="feature-item mb-3">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <strong>Fast Delivery:</strong> Quick and reliable delivery across Bangladesh.
                </div>
                <div class="feature-item mb-3">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <strong>Expert Support:</strong> Professional guidance and customer support.
                </div>
            </div>
            <div class="col-lg-6">
                <img src="<?php echo e($about_us_image); ?>" alt="About Us" class="img-fluid rounded" onerror="this.src='/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-about.svg'">
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA Section -->
<section class="py-5 bg-success text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 text-center text-lg-start mb-3 mb-lg-0">
                <h3 class="mb-2">Have Questions? Need Help Choosing Products?</h3>
                <p class="mb-0">Contact us on WhatsApp for personalized recommendations and instant support!</p>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <a href="https://wa.me/8801792457594?text=Hello%2C%20I%27m%20interested%20in%20your%20herbal%20products" 
                   target="_blank" 
                   class="btn btn-light btn-lg">
                    <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                </a>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <div class="d-flex justify-content-center gap-4 flex-wrap">
                    <div>
                        <i class="bi bi-whatsapp fs-4"></i>
                        <span class="ms-2">+880 1792-457594</span>
                    </div>
                    <div>
                        <i class="bi bi-facebook fs-4"></i>
                        <a href="https://www.facebook.com/Bio.Organic.Herbal.Solutions" target="_blank" class="text-white text-decoration-none ms-2">
                            Facebook Page
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
