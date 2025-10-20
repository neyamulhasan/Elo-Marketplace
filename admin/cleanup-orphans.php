<?php
/**
 * Automated Orphaned Image Cleanup Script
 * Run this script to clean up orphaned images from the uploads folder
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Product.php';

// Initialize
$database = new Database();
$db = $database->getConnection();
$productModel = new Product($db);

// Get upload directory
$upload_dir = __DIR__ . '/../public/uploads/products/';

// Get all image files
$all_files = glob($upload_dir . 'product_*.*');
$total_files = count($all_files);

echo "🔍 Scanning for orphaned images...\n";
echo "📁 Upload directory: $upload_dir\n";
echo "📊 Total files found: $total_files\n\n";

// Get all products
$products = $productModel->getAll();

// Build list of images in use
$used_images = [];

foreach ($products as $product) {
    // Check single image field
    if (!empty($product['image'])) {
        $used_images[] = $product['image'];
    }
    
    // Check multi-image field
    if (!empty($product['images'])) {
        $images_array = json_decode($product['images'], true);
        if (is_array($images_array)) {
            foreach ($images_array as $img) {
                $used_images[] = $img;
            }
        }
    }
}

$used_images = array_unique($used_images);
echo "✅ Images in use by products: " . count($used_images) . "\n\n";

// Find orphaned images
$orphaned_images = [];
$orphaned_size = 0;

foreach ($all_files as $file_path) {
    $relative_path = 'uploads/products/' . basename($file_path);
    
    if (!in_array($relative_path, $used_images)) {
        $orphaned_images[] = $file_path;
        $orphaned_size += filesize($file_path);
    }
}

echo "🗑️  Orphaned images found: " . count($orphaned_images) . "\n";
echo "💾 Total size: " . formatBytes($orphaned_size) . "\n\n";

// Display orphaned images
if (count($orphaned_images) > 0) {
    echo "📋 Orphaned images list:\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($orphaned_images as $index => $file_path) {
        $filename = basename($file_path);
        $size = formatBytes(filesize($file_path));
        echo sprintf("%3d. %-50s %10s\n", $index + 1, $filename, $size);
    }
    
    echo str_repeat("-", 80) . "\n\n";
    
    // Ask for confirmation
    echo "⚠️  WARNING: This will permanently delete " . count($orphaned_images) . " image(s)!\n";
    echo "💾 Total disk space to be freed: " . formatBytes($orphaned_size) . "\n\n";
    echo "Do you want to proceed with deletion? (yes/no): ";
    
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($line) === 'yes') {
        echo "\n🗑️  Deleting orphaned images...\n\n";
        
        $deleted_count = 0;
        $failed_count = 0;
        
        foreach ($orphaned_images as $file_path) {
            if (unlink($file_path)) {
                $deleted_count++;
                echo "✅ Deleted: " . basename($file_path) . "\n";
            } else {
                $failed_count++;
                echo "❌ Failed: " . basename($file_path) . "\n";
            }
        }
        
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "✅ Cleanup Complete!\n";
        echo "   Deleted: $deleted_count image(s)\n";
        if ($failed_count > 0) {
            echo "   Failed: $failed_count image(s)\n";
        }
        echo "   Freed up: " . formatBytes($orphaned_size) . " of disk space\n";
        echo str_repeat("=", 80) . "\n";
        
    } else {
        echo "\n❌ Cleanup cancelled. No files were deleted.\n";
    }
    
} else {
    echo "✨ No orphaned images found! Your uploads folder is clean.\n";
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>
