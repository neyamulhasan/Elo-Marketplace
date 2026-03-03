<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Order.php';
require_once '../app/models/Product.php';
require_once '../app/models/User.php';

// Require login
requireLogin('checkout.php');

$database = new Database();
$db = $database->getConnection();
$orderModel = new Order($db);
$productModel = new Product($db);
$userModel = new User($db);

// Check cart
initCart();
if (empty($_SESSION['cart'])) {
    header('Location: ' . BASE_URL . '/public/cart.php');
    exit();
}

$errors = [];
$user = $userModel->getUserById(getUserId());

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = sanitizeString($_POST['shipping_address'] ?? '');
    $payment_method = sanitizeString($_POST['payment_method'] ?? '');
    
    if (!validateRequired($shipping_address)) {
        $errors[] = 'Shipping address is required';
    }
    
    if (!in_array($payment_method, ['cash_on_delivery', 'bkash'])) {
        $errors[] = 'Please select a payment method';
    }
    
    if (empty($errors)) {
        // Calculate total
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        // Create order
        $order_data = [
            'user_id' => getUserId(),
            'total' => $total,
            'payment_method' => $payment_method,
            'shipping_address' => $shipping_address
        ];
        
        $order_id = $orderModel->create($order_data);
        
        if ($order_id) {
            // Add order items
            $items = [];
            foreach ($_SESSION['cart'] as $cart_item) {
                $items[] = [
                    'product_id' => $cart_item['id'],
                    'quantity' => $cart_item['quantity'],
                    'price' => $cart_item['price']
                ];
                
                // Update stock
                $productModel->updateStock($cart_item['id'], $cart_item['quantity']);
            }
            
            $orderModel->addItems($order_id, $items);
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            setFlashMessage('success', 'Order placed successfully! Order ID: #' . $order_id);
            header('Location: ' . BASE_URL . '/public/orders.php');
            exit();
        } else {
            $errors[] = 'Failed to place order. Please try again.';
        }
    }
}

$page_title = "Checkout";
include 'includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Checkout</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="row">
            <div class="col-lg-8">
                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" value="<?php echo e($user['name']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo e($user['email']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" value="<?php echo e($user['phone']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address *</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?php echo e($user['address']); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cash_on_delivery" checked>
                            <label class="form-check-label" for="cod">
                                <strong>Cash on Delivery</strong>
                                <p class="text-muted small mb-0">Pay when you receive your order</p>
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="bkash" value="bkash">
                            <label class="form-check-label" for="bkash">
                                <strong>bKash</strong>
                                <p class="text-muted small mb-0">Pay via bKash mobile banking</p>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo e($item['name']); ?> x<?php echo $item['quantity']; ?></span>
                            <span><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong><?php echo formatPrice($total); ?></strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span>Shipping:</span>
                            <strong class="text-success">Free</strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total:</h5>
                            <h5 class="text-success"><?php echo formatPrice($total); ?></h5>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            <i class="bi bi-check-circle"></i> Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
