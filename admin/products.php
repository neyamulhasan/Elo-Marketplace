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

// Get filter parameter
$show_all = isset($_GET['show_all']) && $_GET['show_all'] == '1';

// Get ALL products for admin (including inactive ones)
$products = $productModel->getAll(null, 0, false);
$categories = $categoryModel->getAll();

// Count active and inactive products
$active_count = 0;
$inactive_count = 0;
foreach ($products as $product) {
    if ($product['status'] == 1) {
        $active_count++;
    } else {
        $inactive_count++;
    }
}

$page_title = "Products";
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Products Management</h2>
        <p class="text-muted mb-0">
            <span class="badge bg-success"><?php echo $active_count; ?> Active</span>
            <span class="badge bg-danger"><?php echo $inactive_count; ?> Inactive</span>
        </p>
    </div>
    <a href="<?= BASE_URL ?>/admin/product-add.php" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Add Product
    </a>
</div>

<!-- Filter Buttons -->
<div class="card mb-3">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="<?= BASE_URL ?>/admin/products.php" 
               class="btn btn-<?php echo !$show_all ? 'success' : 'outline-success'; ?>">
                <i class="bi bi-check-circle"></i> Active Products (<?php echo $active_count; ?>)
            </a>
            <a href="<?= BASE_URL ?>/admin/products.php?show_all=1" 
               class="btn btn-<?php echo $show_all ? 'primary' : 'outline-primary'; ?>">
                <i class="bi bi-list"></i> All Products (<?php echo count($products); ?>)
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $displayed_count = 0;
                    foreach ($products as $product): 
                        // Filter: Show only active products unless "show_all" is enabled
                        if (!$show_all && $product['status'] != 1) {
                            continue;
                        }
                        $displayed_count++;
                    ?>
                    <tr class="<?php echo $product['status'] ? '' : 'table-secondary'; ?>">
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <?php echo e($product['name']); ?>
                            <?php if (!$product['status']): ?>
                                <span class="badge bg-secondary text-white ms-1">Hidden</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($product['category_name']); ?></td>
                        <td>
                            <?php if (hasDiscount($product['discount_percentage'])): ?>
                                <small class="text-muted text-decoration-line-through d-block"><?php echo formatPrice($product['price']); ?></small>
                                <strong><?php echo formatPrice(calculateDiscountedPrice($product['price'], $product['discount_percentage'])); ?></strong>
                            <?php else: ?>
                                <?php echo formatPrice($product['price']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (hasDiscount($product['discount_percentage'])): ?>
                                <span class="badge bg-danger"><?php echo number_format($product['discount_percentage'], 0); ?>% OFF</span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($product['stock'] <= 0): ?>
                                <span class="badge bg-danger">Out of Stock</span>
                            <?php else: ?>
                                <?php echo $product['stock']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($product['status']): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="<?= BASE_URL ?>/admin/product-edit.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ($product['status']): ?>
                                    <button class="btn btn-sm btn-secondary" 
                                            onclick="toggleStatus(<?php echo $product['id']; ?>, 0, '<?php echo e($product['name']); ?>')"
                                            title="Deactivate">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-success" 
                                            onclick="toggleStatus(<?php echo $product['id']; ?>, 1, '<?php echo e($product['name']); ?>')"
                                            title="Activate">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-danger" 
                                        onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo e($product['name']); ?>')"
                                        title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if ($displayed_count == 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">No products to display</p>
                            <?php if (!$show_all && $inactive_count > 0): ?>
                                <a href="<?= BASE_URL ?>/admin/products.php?show_all=1" class="btn btn-sm btn-primary">
                                    Show All Products (<?php echo $inactive_count; ?> inactive)
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>/admin/product-action.php" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong>Warning!</strong> This action cannot be undone.
                    </div>
                    <p>Are you sure you want to permanently delete the product "<strong id="delete_name"></strong>"?</p>
                    <p class="text-danger"><strong>This will:</strong></p>
                    <ul class="text-muted">
                        <li>Remove the product from your catalog</li>
                        <li>Delete all product data permanently</li>
                        <li>Remove from any customer wishlists</li>
                        <li>Cannot be recovered after deletion</li>
                    </ul>
                    <p class="text-info small"><i class="bi bi-info-circle"></i> <strong>Alternative:</strong> Consider deactivating the product instead to hide it from customers while preserving data.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete Permanently</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleStatus(productId, newStatus, productName) {
    const action = newStatus == 1 ? 'activate' : 'deactivate';
    const actionWord = newStatus == 1 ? 'Activate' : 'Deactivate';
    
    if (confirm(`Are you sure you want to ${action} "${productName}"?`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= BASE_URL ?>/admin/product-action.php';
        
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
    document.getElementById('delete_id').value = productId;
    document.getElementById('delete_name').textContent = productName;
    
    new bootstrap.Modal(document.getElementById('deleteProductModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
