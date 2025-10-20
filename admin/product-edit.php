<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Product.php';
require_once '../app/models/Category.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$productModel = new Product($db);
$categoryModel = new Category($db);

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    $_SESSION['flash_message'] = 'Invalid product ID';
    $_SESSION['flash_type'] = 'danger';
    header('Location: /Bio-Organic-Herbal-Solutions/admin/products.php');
    exit;
}

// Get product details
$product = $productModel->getById($product_id);

if (!$product) {
    $_SESSION['flash_message'] = 'Product not found';
    $_SESSION['flash_type'] = 'danger';
    header('Location: /Bio-Organic-Herbal-Solutions/admin/products.php');
    exit;
}

$categories = $categoryModel->getAll();

$page_title = "Edit Product";
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Bio-Organic-Herbal-Solutions/admin/dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/Bio-Organic-Herbal-Solutions/admin/products.php">Products</a></li>
                    <li class="breadcrumb-item active">Edit Product</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Product</h4>
                </div>
                <div class="card-body">
                    <form action="/Bio-Organic-Herbal-Solutions/admin/product-action.php" method="POST" enctype="multipart/form-data" id="editProductForm">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        
                        <div class="row">
                            <!-- Product Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="<?php echo e($product['name']); ?>" required placeholder="Enter product name">
                            </div>

                            <!-- SKU -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sku" value="<?php echo e($product['sku']); ?>" required placeholder="Product SKU code">
                                <small class="text-muted">Unique product identifier</small>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">-- Select Category --</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo e($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Price -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (৳) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" value="<?php echo $product['price']; ?>" step="0.01" min="0" required placeholder="0.00">
                            </div>

                            <!-- Stock -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stock" value="<?php echo $product['stock']; ?>" min="0" required placeholder="0">
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="1" <?php echo $product['status'] == 1 ? 'selected' : ''; ?>>Active (Visible to customers)</option>
                                    <option value="0" <?php echo $product['status'] == 0 ? 'selected' : ''; ?>>Inactive (Hidden from customers)</option>
                                </select>
                            </div>

                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Product Description</label>
                                <textarea class="form-control" name="description" rows="5" placeholder="Enter product description, benefits, usage instructions, etc."><?php echo e($product['description']); ?></textarea>
                                <small class="text-muted">Detailed information about the product</small>
                            </div>

                            <!-- Product Images Management -->
                            <div class="col-12 mb-4">
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0"><i class="bi bi-images"></i> Product Images</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Current Images Display -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Current Images</label>
                                            <div id="imagesContainer" class="row g-2">
                                                <?php 
                                                // Get all product images
                                                $images = [];
                                                if (!empty($product['images'])) {
                                                    $decoded = json_decode($product['images'], true);
                                                    if (is_array($decoded)) {
                                                        $images = $decoded;
                                                    }
                                                }
                                                if (!empty($product['image'])) {
                                                    $images[] = $product['image'];
                                                }
                                                $images = array_unique($images);
                                                
                                                if (empty($images)): 
                                                ?>
                                                    <div class="col-12 text-center py-4" id="noImagesMessage">
                                                        <img src="/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-product.svg" 
                                                             alt="No images" 
                                                             class="img-thumbnail mb-2" 
                                                             style="max-width: 200px;">
                                                        <p class="text-muted">No images uploaded yet</p>
                                                    </div>
                                                <?php else: ?>
                                                    <?php foreach ($images as $index => $image_path): ?>
                                                        <div class="col-6 col-md-4 col-lg-3" data-image-path="<?php echo e($image_path); ?>">
                                                            <div class="position-relative image-item">
                                                                <img src="/Bio-Organic-Herbal-Solutions/public/<?php echo e($image_path); ?>" 
                                                                     alt="Product image" 
                                                                     class="img-thumbnail w-100" 
                                                                     style="height: 150px; object-fit: cover;">
                                                                <button type="button" 
                                                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-image-btn" 
                                                                        data-image-path="<?php echo e($image_path); ?>"
                                                                        onclick="deleteImage('<?php echo e($image_path); ?>')"
                                                                        title="Delete this image">
                                                                    <i class="bi bi-x-lg"></i>
                                                                </button>
                                                                <?php if ($index === 0): ?>
                                                                    <span class="badge bg-primary position-absolute bottom-0 start-0 m-1">Primary</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Upload New Images -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Upload New Images</label>
                                            <input type="file" 
                                                   class="form-control" 
                                                   id="multiImageInput" 
                                                   accept="image/*" 
                                                   multiple
                                                   onchange="previewMultipleImages(event)">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle"></i> 
                                                Select multiple images (Ctrl/Cmd + Click) | JPG, PNG, GIF, WebP | Max 2MB each
                                            </small>
                                        </div>
                                        
                                        <!-- New Images Preview -->
                                        <div id="newImagesPreview" class="row g-2 mb-3" style="display: none;">
                                            <div class="col-12">
                                                <label class="form-label fw-bold text-success">New Images Preview</label>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="d-flex gap-2">
                                            <button type="button" 
                                                    class="btn btn-primary" 
                                                    onclick="uploadImages()" 
                                                    id="uploadImagesBtn">
                                                <i class="bi bi-cloud-upload"></i> Upload Selected Images
                                            </button>
                                            <?php if (!empty($images)): ?>
                                                <button type="button" 
                                                        class="btn btn-warning" 
                                                        onclick="resetToDefault()">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Reset to Default
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Upload Progress -->
                                        <div id="uploadProgress" class="mt-3" style="display: none;">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                     role="progressbar" 
                                                     style="width: 0%">
                                                    Uploading...
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Legacy Single Image Upload (Hidden but functional for backward compatibility) -->
                            <div class="col-12 mb-3" style="display: none;">
                                <label class="form-label">Legacy Image Field</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="/Bio-Organic-Herbal-Solutions/admin/products.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="bi bi-check-circle"></i> Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Actions -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-gear text-info"></i> Quick Actions</h6>
                    <div class="btn-group" role="group">
                        <?php if ($product['status'] == 1): ?>
                            <button class="btn btn-outline-secondary btn-sm" onclick="toggleStatus(<?php echo $product['id']; ?>, 0, '<?php echo e($product['name']); ?>')">
                                <i class="bi bi-x-circle"></i> Deactivate Product
                            </button>
                        <?php else: ?>
                            <button class="btn btn-outline-success btn-sm" onclick="toggleStatus(<?php echo $product['id']; ?>, 1, '<?php echo e($product['name']); ?>')">
                                <i class="bi bi-check-circle"></i> Activate Product
                            </button>
                        <?php endif; ?>
                        <button class="btn btn-outline-danger btn-sm" onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo e($product['name']); ?>')">
                            <i class="bi bi-trash"></i> Delete Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus(productId, newStatus, productName) {
    const action = newStatus == 1 ? 'activate' : 'deactivate';
    
    if (confirm(`Are you sure you want to ${action} "${productName}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/Bio-Organic-Herbal-Solutions/admin/product-action.php';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'toggle_status';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = productId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = newStatus;
        
        form.appendChild(actionInput);
        form.appendChild(idInput);
        form.appendChild(statusInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteProduct(productId, productName) {
    if (confirm(`⚠️ WARNING!\n\nAre you sure you want to PERMANENTLY DELETE "${productName}"?\n\nThis action CANNOT be undone!\n\nAll product data will be lost forever.\n\nConsider deactivating instead to preserve data.`)) {
        if (confirm(`FINAL CONFIRMATION\n\nYou are about to delete "${productName}" permanently.\n\nClick OK to proceed with deletion, or Cancel to abort.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/Bio-Organic-Herbal-Solutions/admin/product-action.php';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = productId;
            
            form.appendChild(actionInput);
            form.appendChild(idInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
}

// Multi-Image Upload Functions
let selectedFiles = [];

function previewMultipleImages(event) {
    const files = event.target.files;
    const preview = document.getElementById('newImagesPreview');
    const uploadBtn = document.getElementById('uploadImagesBtn');
    
    // Clear previous previews
    const existingPreviews = preview.querySelectorAll('.preview-image');
    existingPreviews.forEach(el => el.remove());
    
    if (files.length === 0) {
        preview.style.display = 'none';
        uploadBtn.disabled = true;
        return;
    }
    
    selectedFiles = Array.from(files);
    preview.style.display = 'flex';
    uploadBtn.disabled = false;
    
    // Validate and preview each file
    selectedFiles.forEach((file, index) => {
        // Validate file size
        if (file.size > 2 * 1024 * 1024) {
            alert(`File "${file.name}" is too large (max 2MB)`);
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert(`File "${file.name}" is not a valid image type`);
            return;
        }
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3 preview-image';
            col.innerHTML = `
                <div class="position-relative">
                    <img src="${e.target.result}" 
                         alt="Preview" 
                         class="img-thumbnail w-100" 
                         style="height: 150px; object-fit: cover; border: 2px solid #28a745;">
                    <span class="badge bg-success position-absolute top-0 end-0 m-1">New</span>
                    <small class="d-block text-center text-muted mt-1">${file.name}</small>
                </div>
            `;
            preview.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
}

function uploadImages() {
    const input = document.getElementById('multiImageInput');
    const files = input.files;
    
    if (files.length === 0) {
        alert('Please select images to upload');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'upload');
    formData.append('product_id', <?php echo $product['id']; ?>);
    
    for (let i = 0; i < files.length; i++) {
        formData.append('images[]', files[i]);
    }
    
    // Show progress
    const progressDiv = document.getElementById('uploadProgress');
    const uploadBtn = document.getElementById('uploadImagesBtn');
    progressDiv.style.display = 'block';
    uploadBtn.disabled = true;
    
    fetch('/Bio-Organic-Herbal-Solutions/admin/product-images-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text(); // Get as text first
    })
    .then(text => {
        console.log('Response text:', text); // Debug log
        try {
            const data = JSON.parse(text);
            progressDiv.style.display = 'none';
            uploadBtn.disabled = false;
            
            if (data.success) {
                alert(data.message);
                location.reload(); // Reload to show new images
            } else {
                alert('Error: ' + data.message);
                if (data.errors && data.errors.length > 0) {
                    console.error('Upload errors:', data.errors);
                }
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response was:', text);
            throw new Error('Invalid JSON response from server');
        }
    })
    .catch(error => {
        progressDiv.style.display = 'none';
        uploadBtn.disabled = false;
        alert('Upload failed: ' + error.message);
        console.error('Error:', error);
    });
}

function deleteImage(imagePath) {
    if (!confirm('Are you sure you want to delete this image?\n\nThis action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('product_id', <?php echo $product['id']; ?>);
    formData.append('image_path', imagePath);
    
    fetch('/Bio-Organic-Herbal-Solutions/admin/product-images-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        console.log('Delete response:', text); // Debug log
        try {
            const data = JSON.parse(text);
            if (data.success) {
                // Remove image from DOM
                const imageItem = document.querySelector(`[data-image-path="${imagePath}"]`);
                if (imageItem) {
                    imageItem.remove();
                }
                
                // Check if no images left
                const container = document.getElementById('imagesContainer');
                if (container.children.length === 0) {
                    container.innerHTML = `
                        <div class="col-12 text-center py-4" id="noImagesMessage">
                            <img src="/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-product.svg" 
                                 alt="No images" 
                                 class="img-thumbnail mb-2" 
                                 style="max-width: 200px;">
                            <p class="text-muted">No images uploaded yet</p>
                        </div>
                    `;
                }
                
                alert(data.message);
            } else {
                alert('Error: ' + data.message);
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response was:', text);
            alert('Failed to parse server response');
        }
    })
    .catch(error => {
        alert('Delete failed: ' + error.message);
        console.error('Error:', error);
    });
}

function resetToDefault() {
    if (!confirm('⚠️ WARNING!\n\nThis will DELETE ALL images for this product!\n\nThe product will be reset to use the default placeholder image.\n\nThis action CANNOT be undone!\n\nAre you sure?')) {
        return;
    }
    
    if (!confirm('FINAL CONFIRMATION\n\nAll images will be permanently deleted.\n\nClick OK to proceed.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'reset');
    formData.append('product_id', <?php echo $product['id']; ?>);
    
    fetch('/Bio-Organic-Herbal-Solutions/admin/product-images-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        console.log('Reset response:', text); // Debug log
        try {
            const data = JSON.parse(text);
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response was:', text);
            alert('Failed to parse server response');
        }
    })
    .catch(error => {
        alert('Reset failed: ' + error.message);
        console.error('Error:', error);
    });
}

// New image preview function (legacy single image)
function previewNewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('newImagePreview');
    const previewImg = document.getElementById('newPreviewImg');
    
    if (file) {
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB!');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a valid image file (JPG, PNG, GIF, or WebP)');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}
</script>

<style>
.image-item {
    transition: transform 0.2s, box-shadow 0.2s;
}

.image-item:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.delete-image-btn {
    opacity: 0.8;
    padding: 2px 6px;
    font-size: 12px;
    border-radius: 50%;
    z-index: 10;
}

.delete-image-btn:hover {
    opacity: 1;
    transform: scale(1.1);
}

#uploadProgress .progress {
    height: 30px;
}

#uploadProgress .progress-bar {
    font-size: 14px;
    line-height: 30px;
}

.preview-image {
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<?php include 'includes/footer.php'; ?>
