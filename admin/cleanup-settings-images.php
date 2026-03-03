<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/models/Setting.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$settingModel = new Setting($db);

// Get current images from database
$home_banner = $settingModel->get('home_banner_image');
$about_us = $settingModel->get('about_us_image');

$used_images = [];
if ($home_banner && strpos($home_banner, 'uploads/settings/') !== false) {
    $used_images[] = basename($home_banner);
}
if ($about_us && strpos($about_us, 'uploads/settings/') !== false) {
    $used_images[] = basename($about_us);
}

// Get all files in uploads/settings directory
$upload_dir = '../public/uploads/settings/';
$all_files = [];
if (is_dir($upload_dir)) {
    $all_files = array_diff(scandir($upload_dir), ['.', '..']);
}

// Find unused files
$unused_files = [];
foreach ($all_files as $file) {
    if (!in_array($file, $used_images)) {
        $unused_files[] = $file;
    }
}

// Handle cleanup action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cleanup'])) {
    $deleted_count = 0;
    foreach ($unused_files as $file) {
        $filepath = $upload_dir . $file;
        if (file_exists($filepath) && is_file($filepath)) {
            if (unlink($filepath)) {
                $deleted_count++;
            }
        }
    }
    
    setFlashMessage('success', "Cleanup complete! $deleted_count unused image(s) deleted.");
    header('Location: ' . BASE_URL . '/admin/cleanup-settings-images.php');
    exit;
}

$page_title = "Cleanup Settings Images";
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-trash"></i> Cleanup Settings Images
    </h2>
    <a href="<?= BASE_URL ?>/admin/settings.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Settings
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-images"></i> Image Status</h5>
            </div>
            <div class="card-body">
                
                <h6 class="fw-bold mb-3">Currently Used Images:</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Setting</th>
                                <th>Image File</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Home Banner</strong></td>
                                <td>
                                    <?php if ($home_banner && strpos($home_banner, '/uploads/settings/') !== false): ?>
                                        <code><?php echo basename($home_banner); ?></code>
                                    <?php else: ?>
                                        <span class="text-muted">Using default image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($home_banner && strpos($home_banner, '/uploads/settings/') !== false): ?>
                                        <span class="badge bg-success">Custom</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Default</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>About Us</strong></td>
                                <td>
                                    <?php if ($about_us && strpos($about_us, '/uploads/settings/') !== false): ?>
                                        <code><?php echo basename($about_us); ?></code>
                                    <?php else: ?>
                                        <span class="text-muted">Using default image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($about_us && strpos($about_us, '/uploads/settings/') !== false): ?>
                                        <span class="badge bg-success">Custom</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Default</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php if (count($unused_files) > 0): ?>
                    <div class="alert alert-warning">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Found <?php echo count($unused_files); ?> Unused Image(s)
                        </h6>
                        <p class="mb-2">The following images are stored on the server but not currently used:</p>
                        <ul class="mb-3">
                            <?php foreach ($unused_files as $file): ?>
                                <li><code><?php echo $file; ?></code></li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete <?php echo count($unused_files); ?> unused image(s)? This action cannot be undone.');">
                            <input type="hidden" name="cleanup" value="1">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Delete Unused Images
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> 
                        <strong>All clean!</strong> No unused images found. All uploaded images are currently in use.
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Information</h5>
            </div>
            <div class="card-body">
                <h6 class="fw-bold">What This Does:</h6>
                <ul class="small">
                    <li>Scans the uploads/settings/ folder</li>
                    <li>Compares with currently used images</li>
                    <li>Identifies unused/orphaned images</li>
                    <li>Allows you to delete them to free space</li>
                </ul>

                <hr>

                <h6 class="fw-bold">When to Use:</h6>
                <ul class="small">
                    <li>After uploading new banner/about images</li>
                    <li>To free up server storage space</li>
                    <li>Regular maintenance cleanup</li>
                </ul>

                <hr>

                <h6 class="fw-bold">Storage Info:</h6>
                <ul class="small">
                    <li><strong>Total Files:</strong> <?php echo count($all_files); ?></li>
                    <li><strong>In Use:</strong> <?php echo count($used_images); ?></li>
                    <li><strong>Unused:</strong> <?php echo count($unused_files); ?></li>
                </ul>

                <hr>

                <div class="alert alert-info mb-0 small">
                    <i class="bi bi-shield-check"></i> <strong>Safe:</strong> Only deletes images not referenced in the database.
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
