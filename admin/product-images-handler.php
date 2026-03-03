<?php
/**
 * Product Image Management Handler
 * Handles multiple image uploads, deletes, and reset operations
 */

// Prevent any output before JSON
ob_start();

require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/models/Product.php';

// Check if admin is logged in (return JSON error instead of redirect)
if (!isAdmin()) {
    ob_end_clean();
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Please login as admin.'
    ]);
    exit;
}

// Clear any previous output
ob_end_clean();

$database = new Database();
$db = $database->getConnection();
$productModel = new Product($db);

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$product_id = intval($_POST['product_id'] ?? $_GET['product_id'] ?? 0);

/**
 * Check if image already exists based on content hash
 * Returns existing file path if found, null otherwise
 */
function findExistingImage($file_tmp, $upload_dir) {
    // Calculate hash of uploaded file
    $file_hash = md5_file($file_tmp);
    
    // Scan existing files in upload directory
    $existing_files = glob($upload_dir . 'product_*.*');
    
    foreach ($existing_files as $existing_file) {
        if (is_file($existing_file)) {
            $existing_hash = md5_file($existing_file);
            
            // If hashes match, image already exists
            if ($file_hash === $existing_hash) {
                // Return relative path
                return 'uploads/products/' . basename($existing_file);
            }
        }
    }
    
    return null;
}

/**
 * Upload multiple images for a product
 */
function uploadProductImages($product_id, $files) {
    $uploaded_files = [];
    $errors = [];
    $skipped_duplicates = 0;
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $max_size = 2 * 1024 * 1024; // 2MB
    $upload_dir = __DIR__ . '/../public/uploads/products/';
    
    // Ensure directory exists and has proper permissions
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0775, true);
    }
    chmod($upload_dir, 0775);
    
    // Handle multiple files
    $file_count = count($files['name']);
    error_log("uploadProductImages - File count: $file_count");
    error_log("uploadProductImages - Files structure: " . print_r($files, true));
    
    for ($i = 0; $i < $file_count; $i++) {
        error_log("Processing file $i: " . ($files['name'][$i] ?? 'NO NAME'));
        
        // Skip if no file
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
            error_log("  - Skipped: No file uploaded");
            continue;
        }
        
        // Check for upload errors
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            $error_msg = "Upload error for file " . ($i + 1) . " (Error code: " . $files['error'][$i] . ")";
            error_log("  - Error: $error_msg");
            $errors[] = $error_msg;
            continue;
        }
        
        $file_name = $files['name'][$i];
        $file_tmp = $files['tmp_name'][$i];
        $file_size = $files['size'][$i];
        $file_type = $files['type'][$i];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        error_log("  - Name: $file_name, Size: $file_size, Type: $file_type");
        
        // Validate file type
        if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_extensions)) {
            $error_msg = "Invalid file type for: " . $file_name;
            error_log("  - Error: $error_msg");
            $errors[] = $error_msg;
            continue;
        }
        
        // Validate file size
        if ($file_size > $max_size) {
            $error_msg = "File too large: " . $file_name;
            error_log("  - Error: $error_msg");
            $errors[] = $error_msg;
            continue;
        }
        
        // Check if this exact image already exists (deduplication)
        $existing_image = findExistingImage($file_tmp, $upload_dir);
        
        if ($existing_image) {
            // Image already exists, reuse it
            $uploaded_files[] = $existing_image;
            $skipped_duplicates++;
            error_log("  - ♻️ REUSED: Image already exists at $existing_image (duplicate skipped)");
        } else {
            // New image, upload it
            // Generate unique filename
            $new_filename = 'product_' . $product_id . '_' . time() . '_' . uniqid() . '.' . $file_ext;
            $destination = $upload_dir . $new_filename;
            
            error_log("  - Destination: $destination");
            
            // Move file
            if (move_uploaded_file($file_tmp, $destination)) {
                chmod($destination, 0644);
                $uploaded_files[] = 'uploads/products/' . $new_filename;
                error_log("  - ✅ SUCCESS: Uploaded to $new_filename");
            } else {
                $error_msg = "Failed to move file: " . $file_name;
                error_log("  - ❌ FAILED: $error_msg");
                $errors[] = $error_msg;
            }
        }
    }
    
    error_log("uploadProductImages - Total uploaded: " . count($uploaded_files));
    error_log("uploadProductImages - Duplicates reused: " . $skipped_duplicates);
    
    return [
        'success' => count($uploaded_files) > 0,
        'uploaded' => $uploaded_files,
        'errors' => $errors,
        'duplicates_skipped' => $skipped_duplicates
    ];
}

/**
 * Get product images as array
 */
function getProductImages($product_id, $productModel) {
    $product = $productModel->getById($product_id);
    if (!$product) {
        return [];
    }
    
    $images = [];
    
    // Add images from JSON array
    if (!empty($product['images'])) {
        $decoded = json_decode($product['images'], true);
        if (is_array($decoded)) {
            $images = array_merge($images, $decoded);
        }
    }
    
    // Add single image if exists
    if (!empty($product['image'])) {
        $images[] = $product['image'];
    }
    
    return array_unique($images);
}

/**
 * Save images array to database
 */
function saveProductImages($product_id, $images, $productModel) {
    error_log("saveProductImages - Product ID: $product_id");
    error_log("saveProductImages - Images to save: " . print_r($images, true));
    
    $images_json = json_encode(array_values($images));
    error_log("saveProductImages - JSON: $images_json");
    
    // Get current product data
    $product = $productModel->getById($product_id);
    if (!$product) {
        error_log("saveProductImages - ERROR: Product not found!");
        return false;
    }
    
    error_log("saveProductImages - Current product data: " . print_r($product, true));
    
    // Prepare update data (keep existing data, only update images)
    $data = [
        'name' => $product['name'],
        'description' => $product['description'],
        'price' => $product['price'],
        'stock' => $product['stock'],
        'category_id' => $product['category_id'],
        'sku' => $product['sku'],
        'status' => $product['status'],
        'image' => !empty($images) ? $images[0] : null, // First image as primary
        'images' => $images_json
    ];
    
    error_log("saveProductImages - Data to update: " . print_r($data, true));
    
    $result = $productModel->update($product_id, $data);
    error_log("saveProductImages - Update result: " . ($result ? "SUCCESS" : "FAILED"));
    
    return $result;
}

// Handle different actions
try {
    // Log the request for debugging
    error_log("Image Handler - Action: $action, Product ID: $product_id");
    
    switch ($action) {
        case 'upload':
            // Upload new images
            if (!isset($_FILES['images'])) {
                error_log("Image Handler - No images in FILES array");
                error_log("FILES: " . print_r($_FILES, true));
                throw new Exception('No files uploaded');
            }
            
            error_log("Image Handler - Processing upload for product $product_id");
            error_log("FILES count: " . count($_FILES['images']['name']));
            
            $result = uploadProductImages($product_id, $_FILES['images']);
            
            if ($result['success']) {
                // Get existing images
                $existing_images = getProductImages($product_id, $productModel);
                error_log("Upload Action - Existing images: " . print_r($existing_images, true));
                error_log("Upload Action - New uploads: " . print_r($result['uploaded'], true));
                
                // Merge with new uploads
                $all_images = array_merge($existing_images, $result['uploaded']);
                error_log("Upload Action - All images merged: " . print_r($all_images, true));
                
                // Save to database
                $save_result = saveProductImages($product_id, $all_images, $productModel);
                error_log("Upload Action - Save result: " . ($save_result ? "SUCCESS" : "FAILED"));
                
                $message = count($result['uploaded']) . ' image(s) uploaded successfully';
                if ($result['duplicates_skipped'] > 0) {
                    $message .= ' (' . $result['duplicates_skipped'] . ' duplicate(s) reused)';
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'images' => $all_images,
                    'errors' => $result['errors'],
                    'duplicates_skipped' => $result['duplicates_skipped']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to upload images',
                    'errors' => $result['errors']
                ]);
            }
            exit;
            break;
            
        case 'delete':
            // Delete a specific image
            $image_path = $_POST['image_path'] ?? '';
            
            if (empty($image_path)) {
                throw new Exception('Image path not provided');
            }
            
            // Get current images
            $images = getProductImages($product_id, $productModel);
            
            // Find and remove the image
            $key = array_search($image_path, $images);
            if ($key !== false) {
                unset($images[$key]);
                
                // Delete file from server
                $file_path = __DIR__ . '/../public/' . $image_path;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                
                // Update database
                saveProductImages($product_id, $images, $productModel);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Image deleted successfully',
                    'images' => array_values($images)
                ]);
            } else {
                throw new Exception('Image not found');
            }
            exit;
            break;
            
        case 'reset':
            // Delete all images and reset to default
            $images = getProductImages($product_id, $productModel);
            
            // Delete all files
            foreach ($images as $image_path) {
                $file_path = __DIR__ . '/../public/' . $image_path;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            // Clear images from database using Product model
            $product = $productModel->getById($product_id);
            if ($product) {
                $data = [
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'category_id' => $product['category_id'],
                    'sku' => $product['sku'],
                    'status' => $product['status'],
                    'image' => null,
                    'images' => null
                ];
                $productModel->update($product_id, $data);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'All images deleted. Product reset to default placeholder.',
                'images' => []
            ]);
            exit;
            break;
            
        case 'get':
            // Get all images for a product
            $images = getProductImages($product_id, $productModel);
            
            echo json_encode([
                'success' => true,
                'images' => $images
            ]);
            exit;
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

exit;
