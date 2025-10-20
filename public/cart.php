<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Product.php';

initCart();

// Get product details including stock
$database = new Database();
$db = $database->getConnection();
$productModel = new Product($db);

// Create a copy of cart with stock information (don't modify session directly)
$cart_items = [];
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $product = $productModel->getById($item['id']);
        $item_with_stock = $item; // Create a copy
        if ($product) {
            $item_with_stock['stock'] = $product['stock'];
            $item_with_stock['discount_percentage'] = $product['discount_percentage'];
            // Calculate actual price with discount
            $item_with_stock['actual_price'] = calculateDiscountedPrice($product['price'], $product['discount_percentage']);
        } else {
            $item_with_stock['stock'] = 0;
            $item_with_stock['discount_percentage'] = 0;
            $item_with_stock['actual_price'] = $item['price'];
        }
        $cart_items[] = $item_with_stock;
    }
}

$page_title = "Shopping Cart";
include 'includes/header.php';
?>

<main class="flex-fill">
    <div class="container my-5">
        <h2 class="mb-4 fw-bold">Shopping Cart</h2>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="alert alert-info">
                <i class="bi bi-cart-x"></i> Your cart is empty.
                <a href="/Bio-Organic-Herbal-Solutions/public/shop.php" class="alert-link">Continue Shopping</a>
            </div>
        <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <!-- Desktop View -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total = 0;
                                    foreach ($cart_items as $item): 
                                        $subtotal = $item['actual_price'] * $item['quantity'];
                                        $total += $subtotal;
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($item['name']); ?></strong>
                                            <?php if (isset($item['stock'])): ?>
                                                <br><small class="text-muted">Stock: <?php echo $item['stock']; ?> available</small>
                                            <?php endif; ?>
                                            <?php if (hasDiscount($item['discount_percentage'])): ?>
                                                <br><span class="badge bg-danger small"><?php echo number_format($item['discount_percentage'], 0); ?>% OFF</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (hasDiscount($item['discount_percentage'])): ?>
                                                <small class="text-muted text-decoration-line-through d-block"><?php echo formatPrice($item['price']); ?></small>
                                                <?php echo formatPrice($item['actual_price']); ?>
                                            <?php else: ?>
                                                <?php echo formatPrice($item['price']); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form action="/Bio-Organic-Herbal-Solutions/public/cart-action.php" method="POST" class="d-inline">
                                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                <input type="hidden" name="action" value="update">
                                                <div class="input-group" style="width: 120px;">
                                                    <input type="number" name="quantity" class="form-control form-control-sm" 
                                                           value="<?php echo $item['quantity']; ?>" min="1" 
                                                           max="<?php echo isset($item['stock']) ? $item['stock'] : 999; ?>"
                                                           onchange="this.form.submit()">
                                                </div>
                                            </form>
                                        </td>
                                        <td class="fw-bold"><?php echo formatPrice($subtotal); ?></td>
                                        <td>
                                            <form action="/Bio-Organic-Herbal-Solutions/public/cart-action.php" method="POST" class="d-inline">
                                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                <input type="hidden" name="action" value="remove">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove this item?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile View -->
                        <div class="d-md-none">
                            <?php 
                            $total = 0;
                            foreach ($cart_items as $item): 
                                $subtotal = $item['actual_price'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                            <div class="card mb-3 border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0"><?php echo e($item['name']); ?></h6>
                                            <?php if (isset($item['stock'])): ?>
                                                <small class="text-muted">Stock: <?php echo $item['stock']; ?> available</small>
                                            <?php endif; ?>
                                            <?php if (hasDiscount($item['discount_percentage'])): ?>
                                                <br><span class="badge bg-danger small"><?php echo number_format($item['discount_percentage'], 0); ?>% OFF</span>
                                            <?php endif; ?>
                                        </div>
                                        <form action="/Bio-Organic-Herbal-Solutions/public/cart-action.php" method="POST" class="d-inline ms-2">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="action" value="remove">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove this item?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="mb-2">
                                        <?php if (hasDiscount($item['discount_percentage'])): ?>
                                            <small class="text-muted text-decoration-line-through d-block">৳<?php echo number_format($item['price'], 2); ?></small>
                                            <p class="text-muted mb-0">Price: <?php echo formatPrice($item['actual_price']); ?></p>
                                        <?php else: ?>
                                            <p class="text-muted mb-0">Price: <?php echo formatPrice($item['price']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <form action="/Bio-Organic-Herbal-Solutions/public/cart-action.php" method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="action" value="update">
                                            <div class="input-group" style="width: 120px;">
                                                <button type="submit" name="quantity" value="<?php echo max(1, $item['quantity'] - 1); ?>" class="btn btn-outline-secondary btn-sm" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>-</button>
                                                <input type="number" name="quantity" class="form-control form-control-sm text-center" 
                                                       value="<?php echo $item['quantity']; ?>" min="1" 
                                                       max="<?php echo isset($item['stock']) ? $item['stock'] : 999; ?>" readonly>
                                                <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" class="btn btn-outline-secondary btn-sm" <?php echo isset($item['stock']) && $item['quantity'] >= $item['stock'] ? 'disabled' : ''; ?>>+</button>
                                            </div>
                                        </form>
                                        <strong class="text-success"><?php echo formatPrice($subtotal); ?></strong>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Cart Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong><?php echo formatPrice($total); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span>Shipping:</span>
                            <strong>Free</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total:</h5>
                            <h5 class="text-success"><?php echo formatPrice($total); ?></h5>
                        </div>
                        
                        <?php if (isLoggedIn()): ?>
                            <a href="/Bio-Organic-Herbal-Solutions/public/checkout.php" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-credit-card"></i> Proceed to Checkout
                            </a>
                        <?php else: ?>
                            <a href="/Bio-Organic-Herbal-Solutions/public/login.php?redirect=checkout.php" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-box-arrow-in-right"></i> Login to Checkout
                            </a>
                        <?php endif; ?>
                        
                        <a href="/Bio-Organic-Herbal-Solutions/public/shop.php" class="btn btn-outline-secondary w-100">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
