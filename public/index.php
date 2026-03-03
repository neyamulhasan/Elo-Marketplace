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
$home_banner_image = $settingModel->get('home_banner_image') ?? BASE_URL . '/public/assets/images/hero-image.jpg';
$about_us_image    = $settingModel->get('about_us_image')    ?? BASE_URL . '/public/assets/images/about-image.jpg';
$fallback_category_logo = BASE_URL . '/public/assets/images/elo.png';

// Hero section
$hero_title    = $settingModel->get('hero_title')    ?? 'Elo Marketplace';
$hero_subtitle = $settingModel->get('hero_subtitle') ?? 'Quality products, global sourcing, delivered to your doorstep.';
$hero_btn_text = $settingModel->get('hero_btn_text') ?? 'Shop Now';

// Categories section
$categories_title    = $settingModel->get('categories_title')    ?? 'Shop by Category';
$categories_subtitle = $settingModel->get('categories_subtitle') ?? 'Browse our wide selection of quality products by category.';

// About / Why Choose Us section
$about_title = $settingModel->get('about_title') ?? 'Why Choose Us?';
$about_features = [];
$default_features = [
    1 => ['title' => 'Trusted Suppliers',   'text' => 'Products sourced directly from verified global manufacturers.'],
    2 => ['title' => 'Quality Assured',     'text' => 'Rigorous quality control and testing on every product.'],
    3 => ['title' => 'Competitive Pricing', 'text' => 'Direct sourcing means better prices for you.'],
    4 => ['title' => 'Reliable Delivery',   'text' => 'Fast and secure delivery straight to your doorstep.'],
];
for ($i = 1; $i <= 4; $i++) {
    $about_features[$i] = [
        'title' => $settingModel->get("about_feature_{$i}_title") ?? $default_features[$i]['title'],
        'text'  => $settingModel->get("about_feature_{$i}_text")  ?? $default_features[$i]['text'],
    ];
}

// Contact CTA section
$contact_cta_title    = $settingModel->get('contact_cta_title')    ?? 'Have Questions? We Are Here to Help.';
$contact_cta_subtitle = $settingModel->get('contact_cta_subtitle') ?? 'Reach out for personalized recommendations and instant support.';
$whatsapp_number      = $settingModel->get('whatsapp_number')      ?? '8801792457594';
$whatsapp_message     = $settingModel->get('whatsapp_message')     ?? 'Hello, I am interested in your products.';
$facebook_url         = $settingModel->get('facebook_url')         ?? 'https://www.facebook.com/';

$page_title = "Home";
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-success bg-opacity-10 py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold text-success mb-4"><?php echo e($hero_title); ?></h1>
                <p class="lead mb-5"><?php echo e($hero_subtitle); ?></p>
                <a href="<?= BASE_URL ?>/public/shop.php" class="btn btn-success btn-lg px-5 py-3"><?php echo e($hero_btn_text); ?></a>
            </div>
            <div class="col-lg-6">
                <img src="<?php echo e($home_banner_image); ?>" alt="Hero Banner" class="img-fluid rounded shadow" onerror="this.src='<?= BASE_URL ?>/public/assets/images/placeholder-hero.svg'">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold"><?php echo e($categories_title); ?></h2>
            <p class="text-muted"><?php echo e($categories_subtitle); ?></p>
        </div>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
            <div class="col-md-6 col-lg-3">
                <?php 
                $categoryImage = !empty($category['image']) ? $category['image'] : $fallback_category_logo;
                ?>
                <a href="<?= BASE_URL ?>/public/shop.php?category=<?php echo $category['id']; ?>" 
                   class="text-decoration-none">
                    <div class="category-tile position-relative overflow-hidden rounded shadow-sm" 
                         style="height: 250px;">
                        <img src="<?php echo e($categoryImage); ?>" 
                             alt="<?php echo e($category['name']); ?>" 
                             class="w-100 h-100 object-fit-cover">
                        <div class="position-absolute top-0 start-0 w-100 h-100" 
                             style="background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.7) 100%); z-index: 1;"></div>
                        <div class="position-absolute bottom-0 start-0 w-100 p-3" style="z-index: 2;">
                            <h5 class="fw-bold mb-2 text-white"><?php echo e($category['name']); ?></h5>
                            <span class="btn btn-success btn-sm">View Products</span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4 fw-bold">Featured Products</h2>
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-6 col-md-6 col-lg-3">
                <a href="<?= BASE_URL ?>/public/product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                    <div class="card h-100 product-card d-flex flex-column" style="cursor: pointer;">
                        <div class="product-image-container">
                            <img src="<?php echo e(getProductPrimaryImage($product)); ?>" 
                                 class="product-image" 
                                 alt="<?php echo e($product['name']); ?>"
                                 onerror="this.src='<?= BASE_URL ?>/public/assets/images/placeholder-product.svg'">
                        </div>
                        <div class="card-body p-2 p-md-3 flex-grow-1">
                            <div class="mb-2 d-flex flex-wrap gap-1">
                                <?php if (hasDiscount($product['discount_percentage'])): ?>
                                    <span class="badge bg-danger small"><?php echo number_format($product['discount_percentage'], 0); ?>% OFF</span>
                                <?php else: ?>
                                    <span class="badge bg-info small">Regular Price</span>
                                <?php endif; ?>
                            </div>
                            <h6 class="card-title small mb-2"><?php echo e($product['name']); ?></h6>
                            <div class="mb-2" style="min-height: 48px; display: flex; flex-direction: column; justify-content: center;">
                                <?php if (hasDiscount($product['discount_percentage'])): ?>
                                    <p class="text-muted text-decoration-line-through small mb-1"><?php echo formatPrice($product['price']); ?></p>
                                    <p class="text-success fw-bold mb-0"><?php echo formatPrice(calculateDiscountedPrice($product['price'], $product['discount_percentage'])); ?></p>
                                <?php else: ?>
                                    <p class="text-success fw-bold mb-0"><?php echo formatPrice($product['price']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-2 p-md-3 mt-auto">
                            <button class="btn btn-sm btn-success w-100 add-to-cart" data-id="<?php echo $product['id']; ?>" onclick="event.preventDefault(); event.stopPropagation();">
                                <i class="bi bi-cart-plus"></i> <span class="d-none d-md-inline">Add to Cart</span><span class="d-inline d-md-none">Add</span>
                            </button>
                        </div>
                    </div>
                </a>
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
                <h2 class="mb-3"><?php echo e($about_title); ?></h2>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="feature-item mb-3">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <strong><?php echo e($about_features[$i]['title']); ?>:</strong> <?php echo e($about_features[$i]['text']); ?>
                </div>
                <?php endfor; ?>
            </div>
            <div class="col-lg-6">
                <img src="<?php echo e($about_us_image); ?>" alt="Why Choose Us" class="img-fluid rounded" onerror="this.src='<?= BASE_URL ?>/public/assets/images/placeholder-about.svg'">
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA Section -->
<section class="py-5 bg-success text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 text-center text-lg-start mb-3 mb-lg-0">
                <h3 class="mb-2"><?php echo e($contact_cta_title); ?></h3>
                <p class="mb-0"><?php echo e($contact_cta_subtitle); ?></p>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <a href="https://wa.me/<?php echo e($whatsapp_number); ?>?text=<?php echo rawurlencode($whatsapp_message); ?>" 
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
                        <span class="ms-2"><?php echo e($whatsapp_number); ?></span>
                    </div>
                    <?php if (!empty($facebook_url)): ?>
                    <div>
                        <i class="bi bi-facebook fs-4"></i>
                        <a href="<?php echo e($facebook_url); ?>" target="_blank" class="text-white text-decoration-none ms-2">
                            Facebook Page
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
