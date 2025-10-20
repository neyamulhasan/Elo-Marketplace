<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';

initCart();

$page_title = "Shopping Cart";
include 'includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Shopping Cart</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-info">
            <i class="bi bi-cart-x"></i> Your cart is empty.
            <a href="/Bio-Organic-Herbal-Solutions/public/shop.php" class="alert-link">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
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
                                foreach ($_SESSION['cart'] as $item): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $total += $subtotal;
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($item['name']); ?></strong>
                                    </td>
                                    <td><?php echo formatPrice($item['price']); ?></td>
                                    <td>
                                        <form action="/Bio-Organic-Herbal-Solutions/public/cart-action.php" method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="action" value="update">
                                            <div class="input-group" style="width: 120px;">
                                                <input type="number" name="quantity" class="form-control form-control-sm" 
                                                       value="<?php echo $item['quantity']; ?>" min="1" 
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

<?php include 'includes/footer.php'; ?>
