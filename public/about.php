<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Setting.php';

// Load About Us image from settings with fallback
$database = new Database();
$db = $database->getConnection();
$settingModel = new Setting($db);
$about_us_image = $settingModel->get('about_us_image') ?? BASE_URL . '/public/assets/images/about-image.jpg';

$page_title = "About Us";
include 'includes/header.php';
?>

<!-- About Us Section -->
<section class="py-5">
    <div class="container">
        <!-- Hero -->
        <div class="row mb-5">
            <div class="col-lg-12 text-center">
                <h1 class="display-4 mb-3 fw-bold">About Us</h1>
                <p class="lead text-muted">Natural Solutions for a Healthier Life</p>
            </div>
        </div>

        <!-- Our Story -->
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="<?php echo e($about_us_image); ?>" alt="Elo Marketplace" class="img-fluid rounded shadow" onerror="this.src='<?= BASE_URL ?>/public/assets/images/placeholder-about.svg'">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Our Story</h2>
                <p class="mb-3">Elo Marketplace is your trusted partner in natural health and wellness. We are dedicated to providing the finest quality organic and herbal products that promote holistic well-being.</p>
                <p class="mb-3">Based in Gazipur, Bangladesh, we source our ingredients from trusted suppliers and ensure that every product meets the highest standards of quality and purity. Our commitment to natural solutions stems from the belief that nature provides the best remedies for a healthy life.</p>
                <p class="mb-0">We specialize in skincare, haircare, wellness products, herbal teas, and essential oils - all crafted with care and dedication to your health.</p>
            </div>
        </div>

        <!-- Our Mission & Vision -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <i class="bi bi-bullseye text-success" style="font-size: 3rem;"></i>
                        </div>
                        <h3 class="text-center fw-bold mb-3">Our Mission</h3>
                        <p class="text-muted mb-0">To provide natural, organic, and herbal products that enhance health and well-being, while promoting sustainable and eco-friendly practices. We aim to make natural wellness accessible to everyone.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <i class="bi bi-eye text-success" style="font-size: 3rem;"></i>
                        </div>
                        <h3 class="text-center fw-bold mb-3">Our Vision</h3>
                        <p class="text-muted mb-0">To become the leading provider of organic and herbal products in Bangladesh, recognized for quality, authenticity, and customer trust. We envision a healthier future powered by nature's gifts.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Us -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">Why Choose Us?</h2>
                <p class="text-muted">What makes Elo Marketplace special</p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-shield-check text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">100% Natural</h4>
                    <p class="text-muted">All our products are made from natural and organic ingredients, free from harmful chemicals and synthetic additives.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-award text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Quality Assured</h4>
                    <p class="text-muted">Every product undergoes rigorous quality checks to ensure you receive only the best herbal solutions.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-heart text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Customer First</h4>
                    <p class="text-muted">Your satisfaction and well-being are our top priorities. We're always here to help and guide you.</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-recycle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Eco-Friendly</h4>
                    <p class="text-muted">We are committed to sustainable practices and environmentally responsible packaging.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-cash-coin text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Fair Pricing</h4>
                    <p class="text-muted">High-quality natural products at affordable prices, making wellness accessible to all.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-truck text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Fast Delivery</h4>
                    <p class="text-muted">Quick and reliable delivery service across Bangladesh to get products to you promptly.</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-success text-white border-0 shadow">
                    <div class="card-body text-center py-5">
                        <h3 class="fw-bold mb-3">Ready to Start Your Natural Journey?</h3>
                        <p class="mb-4">Explore our range of organic and herbal products today!</p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="<?= BASE_URL ?>/public/shop.php" class="btn btn-light btn-lg">
                                <i class="bi bi-shop"></i> Shop Now
                            </a>
                            <a href="<?= BASE_URL ?>/public/contact.php" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-envelope"></i> Contact Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
