<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Category.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$categoryModel = new Category($db);

$categories = $categoryModel->getAll();

$page_title = "Add Product";
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Bio-Organic-Herbal-Solutions/admin/dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/Bio-Organic-Herbal-Solutions/admin/products.php">Products</a></li>
                    <li class="breadcrumb-item active">Add Product</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Add New Product</h4>
                </div>
                <div class="card-body">
                    <form action="/Bio-Organic-Herbal-Solutions/admin/product-action.php" method="POST" enctype="multipart/form-data" id="addProductForm">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="row">
                            <!-- Product Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required placeholder="Enter product name">
                            </div>

                            <!-- SKU -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sku" required placeholder="Product SKU code">
                                <small class="text-muted">Unique product identifier</small>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">-- Select Category --</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo e($cat['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Price -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (৳) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" step="0.01" min="0" required placeholder="0.00">
                            </div>

                            <!-- Discount -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount (%) <small class="text-muted">Optional</small></label>
                                <input type="number" class="form-control" name="discount_percentage" step="0.01" min="0" max="100" placeholder="0.00">
                                <small class="text-muted">Leave 0 for no discount</small>
                            </div>

                            <!-- Stock -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stock" min="0" required placeholder="0">
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="1" selected>Active (Visible to customers)</option>
                                    <option value="0">Inactive (Hidden from customers)</option>
                                </select>
                            </div>

                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Product Description</label>
                                <textarea class="form-control" name="description" rows="5" placeholder="Enter product description, benefits, usage instructions, etc."></textarea>
                                <small class="text-muted">Detailed information about the product</small>
                            </div>

                            <!-- Product Images -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Product Images (Multiple)</label>
                                <input type="file" 
                                       class="form-control" 
                                       name="images[]" 
                                       id="imageInput" 
                                       accept="image/*" 
                                       multiple
                                       onchange="previewMultipleImages(event)">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> 
                                    Select multiple images (Ctrl/Cmd + Click) | JPG, PNG, GIF, WebP | Max 2MB each | Recommended: 800x800px
                                </small>
                                
                                <!-- Multiple Images Preview -->
                                <div id="imagePreviewContainer" class="row g-2 mt-3" style="display: none;">
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-success">
                                            <i class="bi bi-eye"></i> Images Preview
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="/Bio-Organic-Herbal-Solutions/admin/products.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Add Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle text-info"></i> Tips for Adding Products</h6>
                    <ul class="mb-0 small text-muted">
                        <li><strong>Product Name:</strong> Use clear, descriptive names</li>
                        <li><strong>SKU:</strong> Create unique codes (e.g., TUR-POW-500G)</li>
                        <li><strong>Price:</strong> Enter price in Bangladeshi Taka (৳)</li>
                        <li><strong>Stock:</strong> Set to 0 if out of stock</li>
                        <li><strong>Status:</strong> Set to Inactive to hide from customers while preparing</li>
                        <li><strong>Image:</strong> Use high-quality product photos for better sales</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Multiple Image preview function
function previewMultipleImages(event) {
    const files = event.target.files;
    const container = document.getElementById('imagePreviewContainer');
    
    // Clear previous previews
    const existingPreviews = container.querySelectorAll('.preview-image');
    existingPreviews.forEach(el => el.remove());
    
    if (files.length === 0) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'flex';
    
    // Preview each file
    Array.from(files).forEach((file, index) => {
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
                         alt="Preview ${index + 1}" 
                         class="img-thumbnail w-100" 
                         style="height: 150px; object-fit: cover;">
                    ${index === 0 ? '<span class="badge bg-primary position-absolute top-0 start-0 m-1">Primary</span>' : ''}
                    <small class="d-block text-center text-muted mt-1">${file.name}</small>
                </div>
            `;
            container.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
}

// Form validation
document.getElementById('addProductForm').addEventListener('submit', function(e) {
    const imageInput = document.getElementById('imageInput');
    if (!imageInput.files || imageInput.files.length === 0) {
        if (!confirm('No images selected. Do you want to add product without images?')) {
            e.preventDefault();
        }
    }
});
</script>

<style>
.preview-image {
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<?php include 'includes/footer.php'; ?>
