<?php
/**
 * Image Cleanup Utility
 * Finds and optionally removes orphaned product images
 * (images that are not referenced by any product in the database)
 */

require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/models/Product.php';

// Check admin access
requireAdmin();

$database = new Database();
$db = $database->getConnection();
$productModel = new Product($db);

// Get action
$action = $_GET['action'] ?? 'scan';
$confirm = $_GET['confirm'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Cleanup Utility</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/vendor/bootstrap-icons.css">
    <style>
        body { padding: 20px; }
        .stats { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .stats h4 { margin-bottom: 15px; }
        .stat-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #dee2e6; }
        .stat-item:last-child { border-bottom: none; }
        .orphan-list { max-height: 400px; overflow-y: auto; }
        .orphan-item { display: flex; align-items: center; padding: 10px; border: 1px solid #dee2e6; margin-bottom: 10px; border-radius: 4px; }
        .orphan-item img { width: 80px; height: 80px; object-fit: cover; margin-right: 15px; }
        .warning-box { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .success-box { background: #d1e7dd; border: 1px solid #0f5132; padding: 15px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-trash"></i> Image Cleanup Utility</h1>
            <a href="products.php" class="btn btn-secondary">Back to Products</a>
        </div>

        <?php
        $upload_dir = __DIR__ . '/../public/uploads/products/';
        
        // Get all image files
        $all_files = glob($upload_dir . 'product_*.*');
        $total_files = count($all_files);
        $total_size = 0;
        
        foreach ($all_files as $file) {
            $total_size += filesize($file);
        }
        
        // Get all products with their images
        $products = $productModel->getAll(null, 0, false); // Get all products including inactive
        
        $used_images = [];
        foreach ($products as $product) {
            // Add single image
            if (!empty($product['image'])) {
                $full_path = __DIR__ . '/../public/' . $product['image'];
                $used_images[$full_path] = true;
            }
            
            // Add images from JSON array
            if (!empty($product['images'])) {
                $images = json_decode($product['images'], true);
                if (is_array($images)) {
                    foreach ($images as $image) {
                        $full_path = __DIR__ . '/../public/' . $image;
                        $used_images[$full_path] = true;
                    }
                }
            }
        }
        
        // Find orphaned images
        $orphaned_files = [];
        $orphaned_size = 0;
        
        foreach ($all_files as $file) {
            if (!isset($used_images[$file])) {
                $orphaned_files[] = $file;
                $orphaned_size += filesize($file);
            }
        }
        
        $used_count = $total_files - count($orphaned_files);
        $used_size = $total_size - $orphaned_size;
        
        // Handle cleanup action
        if ($action === 'delete' && $confirm === 'yes') {
            $deleted_count = 0;
            $deleted_size = 0;
            
            foreach ($orphaned_files as $file) {
                $size = filesize($file);
                if (unlink($file)) {
                    $deleted_count++;
                    $deleted_size += $size;
                }
            }
            
            echo '<div class="success-box">';
            echo '<h4><i class="bi bi-check-circle"></i> Cleanup Complete!</h4>';
            echo '<p>Deleted <strong>' . $deleted_count . '</strong> orphaned image(s)</p>';
            echo '<p>Freed up <strong>' . formatBytes($deleted_size) . '</strong> of disk space</p>';
            echo '<a href="image-cleanup.php" class="btn btn-primary">Run Scan Again</a>';
            echo '</div>';
        } else {
        ?>

        <div class="stats">
            <h4>Storage Statistics</h4>
            <div class="stat-item">
                <span><i class="bi bi-images"></i> Total Images</span>
                <strong><?php echo $total_files; ?></strong>
            </div>
            <div class="stat-item">
                <span><i class="bi bi-hdd"></i> Total Size</span>
                <strong><?php echo formatBytes($total_size); ?></strong>
            </div>
            <div class="stat-item">
                <span><i class="bi bi-check-circle text-success"></i> Images in Use</span>
                <strong><?php echo $used_count; ?> (<?php echo formatBytes($used_size); ?>)</strong>
            </div>
            <div class="stat-item">
                <span><i class="bi bi-trash text-danger"></i> Orphaned Images</span>
                <strong><?php echo count($orphaned_files); ?> (<?php echo formatBytes($orphaned_size); ?>)</strong>
            </div>
        </div>

        <?php if (count($orphaned_files) > 0): ?>
            <div class="warning-box">
                <h4><i class="bi bi-exclamation-triangle"></i> Orphaned Images Found</h4>
                <p>These images are not referenced by any product in the database. You can safely delete them to free up disk space.</p>
            </div>

            <h3>Orphaned Images (<?php echo count($orphaned_files); ?>)</h3>
            <div class="orphan-list mb-4">
                <?php foreach ($orphaned_files as $file): 
                    $filename = basename($file);
                    $size = filesize($file);
                    $relative_path = 'uploads/products/' . $filename;
                ?>
                    <div class="orphan-item">
                        <img src="<?= BASE_URL ?>/public/<?php echo $relative_path; ?>" 
                             alt="<?php echo $filename; ?>"
                             onerror="this.src='<?= BASE_URL ?>/public/assets/images/placeholder-product.svg'">
                        <div class="flex-grow-1">
                            <div><strong><?php echo $filename; ?></strong></div>
                            <small class="text-muted"><?php echo formatBytes($size); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="alert alert-danger">
                <h5><i class="bi bi-exclamation-triangle"></i> Warning!</h5>
                <p>This action will <strong>permanently delete</strong> all orphaned images. This cannot be undone!</p>
                <p>Make sure you have a backup before proceeding.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="image-cleanup.php?action=delete&confirm=yes" 
                   class="btn btn-danger"
                   onclick="return confirm('⚠️ WARNING!\n\nThis will permanently delete <?php echo count($orphaned_files); ?> image(s) and free up <?php echo formatBytes($orphaned_size); ?> of disk space.\n\nThis action CANNOT be undone!\n\nAre you sure you want to continue?');">
                    <i class="bi bi-trash"></i> Delete All Orphaned Images
                </a>
                <a href="products.php" class="btn btn-secondary">Cancel</a>
            </div>
        <?php else: ?>
            <div class="success-box">
                <h4><i class="bi bi-check-circle"></i> All Clean!</h4>
                <p>No orphaned images found. All images in the upload directory are being used by products.</p>
            </div>
        <?php endif; ?>

        <?php } ?>
    </div>
</body>
</html>

<?php
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>
