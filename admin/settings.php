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
    <a href="/Bio-Organic-Herbal-Solutions/admin/cleanup-settings-images.php" class="btn btn-outline-danger">
        <i class="bi bi-trash"></i> Cleanup Unused Images
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-gear"></i> Homepage & About Us Images</h5>
            </div>
            <div class="card-body">
                <form action="/Bio-Organic-Herbal-Solutions/admin/settings-action.php" method="POST" enctype="multipart/form-data">
                    
                    <!-- Home Banner Image -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-image"></i> Home Page Banner Image
                        </label>
                        
                        <div class="mb-3">
                            <div class="border rounded p-3 bg-light text-center">
                                <img src="<?php echo e($settings['home_banner_image'] ?? '/Bio-Organic-Herbal-Solutions/public/assets/images/hero-image.jpg'); ?>" 
                                     alt="Current Banner" 
                                     class="img-fluid rounded"
                                     style="max-height: 300px; object-fit: cover;"
                                     id="banner-preview"
                                     onerror="this.src='/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-hero.svg'">
                                <p class="text-muted small mt-2 mb-0">Current Banner Image</p>
                            </div>
                        </div>

                        <input type="file" 
                               class="form-control" 
                               name="home_banner_image" 
                               accept="image/*"
                               id="banner-input"
                               onchange="previewImage(this, 'banner-preview')">
                        <small class="text-muted">Upload a new banner image for the homepage hero section. Recommended size: 1200x600px. Leave empty to keep current image.</small>
                    </div>

                    <hr>

                    <!-- About Us Image -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-image"></i> About Us Section Image
                        </label>
                        
                        <div class="mb-3">
                            <div class="border rounded p-3 bg-light text-center">
                                <img src="<?php echo e($settings['about_us_image'] ?? '/Bio-Organic-Herbal-Solutions/public/assets/images/about-image.jpg'); ?>" 
                                     alt="Current About Image" 
                                     class="img-fluid rounded"
                                     style="max-height: 300px; object-fit: cover;"
                                     id="about-preview"
                                     onerror="this.src='/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-about.svg'">
                                <p class="text-muted small mt-2 mb-0">Current About Us Image</p>
                            </div>
                        </div>

                        <input type="file" 
                               class="form-control" 
                               name="about_us_image" 
                               accept="image/*"
                               id="about-input"
                               onchange="previewImage(this, 'about-preview')">
                        <small class="text-muted">Upload a new image for the About Us section. Recommended size: 800x600px. Leave empty to keep current image.</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Update Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Information</h5>
            </div>
            <div class="card-body">
                <h6 class="fw-bold">Image Guidelines:</h6>
                <ul class="small">
                    <li><strong>Banner Image:</strong> Used on homepage hero section</li>
                    <li><strong>About Image:</strong> Used in About Us section</li>
                    <li>Supported formats: JPG, PNG, GIF</li>
                    <li>Maximum file size: 5MB</li>
                    <li>Use high-quality images for best results</li>
                </ul>

                <hr>

                <h6 class="fw-bold">Default Images:</h6>
                <ul class="small">
                    <li>If no custom image is uploaded, default placeholder images will be shown</li>
                    <li>You can reset to defaults by uploading the original images</li>
                </ul>

                <hr>

                <div class="alert alert-info mb-0 small">
                    <i class="bi bi-lightbulb"></i> <strong>Tip:</strong> Use landscape images with a 2:1 or 16:9 aspect ratio for the banner.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/footer.php'; ?>
