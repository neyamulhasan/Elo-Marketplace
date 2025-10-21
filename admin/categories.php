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

$page_title = "Categories";
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Categories Management</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="bi bi-plus-circle"></i> Add Category
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td>
                            <?php 
                            $categoryImage = $category['image'] ?? null;
                            if (empty($categoryImage)) {
                                // Get default category logo from settings
                                require_once '../app/models/Setting.php';
                                $settingModel = new Setting($db);
                                $categoryImage = $settingModel->get('default_category_logo') ?? '/Bio-Organic-Herbal-Solutions/public/assets/images/bio.jpg';
                            }
                            ?>
                            <img src="<?php echo e($categoryImage); ?>" 
                                 alt="<?php echo e($category['name']); ?>" 
                                 class="img-thumbnail"
                                 style="width: 50px; height: 50px; object-fit: contain;"
                                 onerror="this.src='/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-category.svg'">
                        </td>
                        <td><?php echo e($category['name']); ?></td>
                        <td><?php echo e($category['description']); ?></td>
                        <td><?php echo formatDate($category['created_at']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning me-1" onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCategory(<?php echo $category['id']; ?>, '<?php echo e($category['name']); ?>')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/Bio-Organic-Herbal-Solutions/admin/category-action.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Image</label>
                        <div class="mb-2">
                            <img id="add_image_preview" 
                                 src="/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-category.svg" 
                                 alt="Preview" 
                                 class="img-thumbnail d-block"
                                 style="max-height: 150px; object-fit: contain;">
                        </div>
                        <input type="file" 
                               class="form-control" 
                               name="image" 
                               accept="image/*"
                               onchange="previewCategoryImage(this, 'add_image_preview')">
                        <small class="text-muted">Leave empty to use default category logo. Recommended: 300x300px (square)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/Bio-Organic-Herbal-Solutions/admin/category-action.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="existing_image" id="edit_existing_image">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Image</label>
                        <div class="mb-2">
                            <img id="edit_image_preview" 
                                 src="/Bio-Organic-Herbal-Solutions/public/assets/images/placeholder-category.svg" 
                                 alt="Preview" 
                                 class="img-thumbnail d-block"
                                 style="max-height: 150px; object-fit: contain;">
                        </div>
                        <input type="file" 
                               class="form-control" 
                               name="image" 
                               accept="image/*"
                               onchange="previewCategoryImage(this, 'edit_image_preview')">
                        <small class="text-muted">Leave empty to keep current image. Upload new to replace.</small>
                        <div class="form-check mt-2" id="delete_image_checkbox_wrapper" style="display: none;">
                            <input class="form-check-input" type="checkbox" name="delete_image" value="1" id="delete_image_checkbox">
                            <label class="form-check-label text-danger" for="delete_image_checkbox">
                                Delete current custom image (will use default logo)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/Bio-Organic-Herbal-Solutions/admin/category-action.php" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong>Warning!</strong> This action cannot be undone.
                    </div>
                    <p>Are you sure you want to delete the category "<strong id="delete_name"></strong>"?</p>
                    <p class="text-muted small">Note: All products in this category will remain but will be uncategorized.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewCategoryImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function editCategory(category) {
    document.getElementById('edit_id').value = category.id;
    document.getElementById('edit_name').value = category.name;
    document.getElementById('edit_description').value = category.description || '';
    document.getElementById('edit_existing_image').value = category.image || '';
    
    // Set preview image
    const defaultLogo = '/Bio-Organic-Herbal-Solutions/public/assets/images/bio.jpg';
    const previewImg = document.getElementById('edit_image_preview');
    previewImg.src = category.image || defaultLogo;
    
    // Show/hide delete checkbox
    const deleteCheckboxWrapper = document.getElementById('delete_image_checkbox_wrapper');
    const deleteCheckbox = document.getElementById('delete_image_checkbox');
    if (category.image) {
        deleteCheckboxWrapper.style.display = 'block';
        deleteCheckbox.checked = false;
    } else {
        deleteCheckboxWrapper.style.display = 'none';
    }
    
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}

function deleteCategory(id, name) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_name').textContent = name;
    
    new bootstrap.Modal(document.getElementById('deleteCategoryModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
