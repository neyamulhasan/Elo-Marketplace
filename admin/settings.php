<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Setting.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$settingModel = new Setting($db);

$settings = $settingModel->getAllAsArray();

$page_title = "Site Settings";
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Site Settings</h2>
    <a href="<?= BASE_URL ?>/admin/cleanup-settings-images.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-trash"></i> Cleanup Unused Images
    </a>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="text-tab" data-bs-toggle="tab" data-bs-target="#text-content" type="button" role="tab">
            <i class="bi bi-pencil-square"></i> Homepage Content
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images-content" type="button" role="tab">
            <i class="bi bi-image"></i> Images
        </button>
    </li>
</ul>

<div class="tab-content" id="settingsTabContent">

    <!-- ===================== TAB 1: HOMEPAGE TEXT ===================== -->
    <div class="tab-pane fade show active" id="text-content" role="tabpanel">
        <form action="<?= BASE_URL ?>/admin/settings-action.php" method="POST">
            <input type="hidden" name="form_type" value="text">

            <!-- Hero Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-star"></i> Hero Section</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Heading</label>
                        <input type="text" class="form-control" name="hero_title"
                               value="<?php echo e($settings['hero_title'] ?? 'Elo Marketplace'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subheading</label>
                        <input type="text" class="form-control" name="hero_subtitle"
                               value="<?php echo e($settings['hero_subtitle'] ?? 'Quality products, global sourcing, delivered to your doorstep.'); ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Button Text</label>
                        <input type="text" class="form-control" name="hero_btn_text"
                               value="<?php echo e($settings['hero_btn_text'] ?? 'Shop Now'); ?>">
                    </div>
                </div>
            </div>

            <!-- Categories Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-grid"></i> Categories Section</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Section Title</label>
                        <input type="text" class="form-control" name="categories_title"
                               value="<?php echo e($settings['categories_title'] ?? 'Shop by Category'); ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Section Subtitle</label>
                        <input type="text" class="form-control" name="categories_subtitle"
                               value="<?php echo e($settings['categories_subtitle'] ?? 'Browse our wide selection of quality products by category.'); ?>">
                    </div>
                </div>
            </div>

            <!-- About / Why Choose Us Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-check-circle"></i> Why Choose Us Section</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Section Title</label>
                        <input type="text" class="form-control" name="about_title"
                               value="<?php echo e($settings['about_title'] ?? 'Why Choose Us?'); ?>">
                    </div>
                    <hr>
                    <?php
                    $default_features = [
                        1 => ['title' => 'Trusted Suppliers',      'text' => 'Products sourced directly from verified global manufacturers.'],
                        2 => ['title' => 'Quality Assured',        'text' => 'Rigorous quality control and testing on every product.'],
                        3 => ['title' => 'Competitive Pricing',    'text' => 'Direct sourcing means better prices for you.'],
                        4 => ['title' => 'Reliable Delivery',      'text' => 'Fast and secure delivery straight to your doorstep.'],
                    ];
                    for ($i = 1; $i <= 4; $i++):
                        $def = $default_features[$i];
                    ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Feature <?= $i ?> Title</label>
                            <input type="text" class="form-control form-control-sm" name="about_feature_<?= $i ?>_title"
                                   value="<?php echo e($settings["about_feature_{$i}_title"] ?? $def['title']); ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Feature <?= $i ?> Description</label>
                            <input type="text" class="form-control form-control-sm" name="about_feature_<?= $i ?>_text"
                                   value="<?php echo e($settings["about_feature_{$i}_text"] ?? $def['text']); ?>">
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Contact CTA Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-telephone"></i> Contact CTA Section</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Heading</label>
                        <input type="text" class="form-control" name="contact_cta_title"
                               value="<?php echo e($settings['contact_cta_title'] ?? 'Have Questions? We Are Here to Help.'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subtext</label>
                        <input type="text" class="form-control" name="contact_cta_subtitle"
                               value="<?php echo e($settings['contact_cta_subtitle'] ?? 'Reach out for personalized recommendations and instant support.'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">WhatsApp Number <small class="text-muted fw-normal">(digits only, with country code — e.g. 8801792457594)</small></label>
                        <input type="text" class="form-control" name="whatsapp_number"
                               value="<?php echo e($settings['whatsapp_number'] ?? '8801792457594'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">WhatsApp Pre-filled Message</label>
                        <input type="text" class="form-control" name="whatsapp_message"
                               value="<?php echo e($settings['whatsapp_message'] ?? 'Hello, I am interested in your products.'); ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Facebook Page URL</label>
                        <input type="url" class="form-control" name="facebook_url"
                               value="<?php echo e($settings['facebook_url'] ?? 'https://www.facebook.com/'); ?>">
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle"></i> Save Homepage Content
                </button>
            </div>
        </form>
    </div>

    <!-- ===================== TAB 2: IMAGES ===================== -->
    <div class="tab-pane fade" id="images-content" role="tabpanel">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-images"></i> Homepage Images</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>/admin/settings-action.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="form_type" value="images">

                            <!-- Home Banner Image -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-image"></i> Hero Banner Image
                                </label>
                                <div class="mb-3">
                                    <div class="border rounded p-3 bg-light text-center">
                                        <img src="<?php echo e($settings['home_banner_image'] ?? BASE_URL . '/public/assets/images/hero-image.jpg'); ?>" 
                                             alt="Current Banner" 
                                             class="img-fluid rounded"
                                             style="max-height: 300px; object-fit: cover;"
                                             id="banner-preview"
                                             onerror="this.src='<?= BASE_URL ?>/public/assets/images/placeholder-hero.svg'">
                                        <p class="text-muted small mt-2 mb-2">Current Banner Image</p>
                                        <?php if (!empty($settings['home_banner_image'])): ?>
                                            <button type="submit" name="delete_banner" value="1" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Remove
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <input type="file" class="form-control" name="home_banner_image" accept="image/*"
                                       onchange="previewImage(this, 'banner-preview')">
                                <small class="text-muted">Recommended: 1200x600px. JPG, PNG, WebP. Max 5MB.</small>
                            </div>

                            <hr>

                            <!-- About Us Image -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-image"></i> Why Choose Us — Section Image
                                </label>
                                <div class="mb-3">
                                    <div class="border rounded p-3 bg-light text-center">
                                        <img src="<?php echo e($settings['about_us_image'] ?? BASE_URL . '/public/assets/images/about-image.jpg'); ?>" 
                                             alt="Current About Image" 
                                             class="img-fluid rounded"
                                             style="max-height: 300px; object-fit: cover;"
                                             id="about-preview"
                                             onerror="this.src='<?= BASE_URL ?>/public/assets/images/placeholder-about.svg'">
                                        <p class="text-muted small mt-2 mb-2">Current Section Image</p>
                                        <?php if (!empty($settings['about_us_image'])): ?>
                                            <button type="submit" name="delete_about" value="1" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Remove
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <input type="file" class="form-control" name="about_us_image" accept="image/*"
                                       onchange="previewImage(this, 'about-preview')">
                                <small class="text-muted">Recommended: 800x600px. JPG, PNG, WebP. Max 5MB.</small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-cloud-upload"></i> Upload Images
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Guidelines</h5>
                    </div>
                    <div class="card-body small">
                        <p><strong>Hero Banner:</strong> Landscape image shown in the homepage top section. Ideal: 1200x600px or 16:9 ratio.</p>
                        <p><strong>Why Choose Us Image:</strong> Shown beside the feature list. Ideal: 800x600px or 4:3 ratio.</p>
                        <p>Supported formats: JPG, PNG, GIF, WebP</p>
                        <p class="mb-0">Maximum file size: 5MB per image</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => { document.getElementById(previewId).src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
    }
}

// Re-activate tab from URL hash on page load
const hash = window.location.hash;
if (hash) {
    const tab = document.querySelector('[data-bs-target="' + hash + '-content"]');
    if (tab) { new bootstrap.Tab(tab).show(); }
}
</script>

<?php include 'includes/footer.php'; ?>
