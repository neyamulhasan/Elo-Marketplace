<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Product.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$productModel = new Product($db);

$action = $_POST['action'] ?? '';

/**
 * Handle image upload with validation
 */
function handleImageUpload($file) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    // Check if file was uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error occurred'];
    }
    
    // Get file info
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_type = $file['type'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Validate file type
    if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_extensions)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed'];
    }
    
    // Validate file size
    if ($file_size > $max_size) {
        return ['success' => false, 'error' => 'File size exceeds 2MB limit'];
    }
    
    // Generate unique filename
    $new_filename = 'product_' . time() . '_' . uniqid() . '.' . $file_ext;
    $upload_dir = __DIR__ . '/../public/uploads/products/';
    
    // Create directory if not exists
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
        chmod($upload_dir, 0777);
    }
    
    $destination = $upload_dir . $new_filename;
    
    // Move uploaded file
    if (move_uploaded_file($file_tmp, $destination)) {
        // Set proper permissions on the uploaded file
        chmod($destination, 0644);
        return ['success' => true, 'filename' => 'uploads/products/' . $new_filename];
    }
    
    // Provide detailed error information
    $error_msg = 'Failed to move uploaded file';
    if (!is_writable($upload_dir)) {
        $error_msg .= ' - Directory not writable';
    }
    if (!file_exists($file_tmp)) {
        $error_msg .= ' - Temp file does not exist';
    }
    
    return ['success' => false, 'error' => $error_msg];
}

/**
 * Delete image file from server
 */
function deleteImageFile($image_path) {
    if (!empty($image_path)) {
        $file_path = __DIR__ . '/../public/' . $image_path;
        if (file_exists($file_path)) {
            return unlink($file_path);
        }
    }
    return false;
}

if ($action === 'add') {
    $data = [
        'name' => sanitizeString($_POST['name']),
        'description' => sanitizeString($_POST['description']),
        'price' => floatval($_POST['price']),
        'stock' => intval($_POST['stock']),
        'category_id' => intval($_POST['category_id']),
        'sku' => sanitizeString($_POST['sku']),
        'status' => intval($_POST['status'])
    ];
    
    $uploaded_images = [];
    $upload_errors = [];
    
    // Handle multiple image uploads
    if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
        $file_count = count($_FILES['images']['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            // Skip if no file or error
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            
            // Create single file array for handleImageUpload
            $single_file = [
                'name' => $_FILES['images']['name'][$i],
                'type' => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error' => $_FILES['images']['error'][$i],
                'size' => $_FILES['images']['size'][$i]
            ];
            
            $upload_result = handleImageUpload($single_file);
            
            if ($upload_result['success']) {
                $uploaded_images[] = $upload_result['filename'];
            } else {
                $upload_errors[] = $upload_result['error'];
            }
        }
    }
    
    // Set first image as primary (legacy single image field)
    if (!empty($uploaded_images)) {
        $data['image'] = $uploaded_images[0];
        $data['images'] = json_encode($uploaded_images);
    }
    
    // Show upload errors if any
    if (!empty($upload_errors)) {
        setFlashMessage('warning', 'Some images failed to upload: ' . implode(', ', $upload_errors));
    }
    
    if ($productModel->create($data)) {
        $success_msg = 'Product added successfully';
        if (!empty($uploaded_images)) {
            $success_msg .= ' with ' . count($uploaded_images) . ' image(s)!';
        }
        setFlashMessage('success', $success_msg);
    } else {
        // Delete uploaded images if database insert fails
        foreach ($uploaded_images as $image_path) {
            deleteImageFile($image_path);
        }
        setFlashMessage('error', 'Failed to add product.');
    }
    
    header('Location: /Bio-Organic-Herbal-Solutions/admin/products.php');
    exit();
    
} elseif ($action === 'edit') {
    $id = intval($_POST['id']);
    
    // Get existing product data
    $existing_product = $productModel->getById($id);
    $old_image = $existing_product['image'] ?? null;
    $old_images = $existing_product['images'] ?? null; // Preserve existing images JSON
    
    $data = [
        'name' => sanitizeString($_POST['name']),
        'description' => sanitizeString($_POST['description']),
        'price' => floatval($_POST['price']),
        'stock' => intval($_POST['stock']),
        'category_id' => intval($_POST['category_id']),
        'sku' => sanitizeString($_POST['sku']),
        'status' => intval($_POST['status']),
        'image' => $old_image, // Keep old image by default
        'images' => $old_images // Keep old images array by default
    ];
    
    // Handle new image upload (legacy single image field - optional)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = handleImageUpload($_FILES['image']);
        if ($upload_result['success']) {
            // Delete old image if exists
            if ($old_image) {
                deleteImageFile($old_image);
            }
            $data['image'] = $upload_result['filename'];
            
            // Also add to images array if not already there
            $images_array = $old_images ? json_decode($old_images, true) : [];
            if (!in_array($upload_result['filename'], $images_array)) {
                array_unshift($images_array, $upload_result['filename']); // Add as first image
                $data['images'] = json_encode($images_array);
            }
        } else {
            setFlashMessage('error', $upload_result['error']);
            header('Location: /Bio-Organic-Herbal-Solutions/admin/product-edit.php?id=' . $id);
            exit();
        }
    }
    
    if ($productModel->update($id, $data)) {
        setFlashMessage('success', 'Product updated successfully!');
    } else {
        setFlashMessage('error', 'Failed to update product.');
    }
    
    header('Location: /Bio-Organic-Herbal-Solutions/admin/products.php');
    exit();
    
} elseif ($action === 'toggle_status') {
    $id = intval($_POST['id']);
    $status = intval($_POST['status']);
    
    if ($productModel->updateStatus($id, $status)) {
        $statusText = $status ? 'activated' : 'deactivated';
        setFlashMessage('success', "Product $statusText successfully!");
    } else {
        setFlashMessage('error', 'Failed to update product status.');
    }
    
    // Preserve the filter parameter when redirecting
    $redirect_url = '/Bio-Organic-Herbal-Solutions/admin/products.php';
    if (isset($_POST['show_all']) && $_POST['show_all'] == '1') {
        $redirect_url .= '?show_all=1';
    }
    
    header('Location: ' . $redirect_url);
    exit();
    
} elseif ($action === 'delete') {
    $id = intval($_POST['id']);
    
    // Get product data to find image paths
    $product_data = $productModel->getById($id);
    
    if ($productModel->delete($id)) {
        $deleted_count = 0;
        
        // Delete all images from the images JSON array
        if (!empty($product_data['images'])) {
            $images = json_decode($product_data['images'], true);
            if (is_array($images)) {
                foreach ($images as $image_path) {
                    if (deleteImageFile($image_path)) {
                        $deleted_count++;
                    }
                }
            }
        }
        
        // Delete single legacy image if exists and not in images array
        if (!empty($product_data['image'])) {
            $images_array = !empty($product_data['images']) ? json_decode($product_data['images'], true) : [];
            if (!in_array($product_data['image'], $images_array)) {
                if (deleteImageFile($product_data['image'])) {
                    $deleted_count++;
                }
            }
        }
        
        $message = 'Product deleted permanently';
        if ($deleted_count > 0) {
            $message .= ' with ' . $deleted_count . ' image(s)';
        }
        $message .= '!';
        
        setFlashMessage('success', $message);
    } else {
        setFlashMessage('error', 'Failed to delete product. It may have related orders.');
    }
    
    header('Location: /Bio-Organic-Herbal-Solutions/admin/products.php');
    exit();
}

// Default redirect
header('Location: /Bio-Organic-Herbal-Solutions/admin/products.php');
exit();
?>
