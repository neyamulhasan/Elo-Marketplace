<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/Product.php';

// Initialize cart
initCart();

$database = new Database();
$db = $database->getConnection();
$productModel = new Product($db);

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    if ($action === 'add' && $product_id > 0) {
        $product = $productModel->getById($product_id);
        
        if ($product && $product['stock'] > 0) {
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
            
            // Check current quantity in cart
            $current_cart_qty = 0;
            $found_index = -1;
            
            foreach ($_SESSION['cart'] as $index => $item) {
                if (intval($item['id']) === $product_id) {
                    $current_cart_qty = $item['quantity'];
                    $found_index = $index;
                    break;
                }
            }
            
            // Check if total quantity exceeds stock
            $total_qty = $current_cart_qty + $quantity;
            if ($total_qty > $product['stock']) {
                setFlashMessage('error', 'Cannot add more items. Only ' . $product['stock'] . ' items available in stock!');
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/public/shop.php'));
                exit();
            }
            
            // Update existing item or add new one
            if ($found_index >= 0) {
                $_SESSION['cart'][$found_index]['quantity'] = $current_cart_qty + $quantity;
            } else {
                $_SESSION['cart'][] = [
                    'id' => intval($product['id']),
                    'name' => $product['name'],
                    'price' => floatval($product['price']),
                    'quantity' => $quantity
                ];
            }
            
            setFlashMessage('success', 'Product added to cart successfully!');
        } else {
            setFlashMessage('error', 'Product not available or out of stock!');
        }
    } elseif ($action === 'update') {
        // Get product to check stock
        $product = $productModel->getById($product_id);
        
        foreach ($_SESSION['cart'] as $key => $item) {
            if (intval($item['id']) === $product_id) {
                $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
                
                // Check if requested quantity exceeds stock
                if ($product && $quantity > $product['stock']) {
                    $_SESSION['cart'][$key]['quantity'] = intval($product['stock']);
                    setFlashMessage('warning', 'Quantity adjusted to available stock (' . $product['stock'] . ' items)');
                } else {
                    $_SESSION['cart'][$key]['quantity'] = max(1, $quantity);
                    setFlashMessage('success', 'Cart updated successfully!');
                }
                break;
            }
        }
    } elseif ($action === 'remove') {
        $new_cart = [];
        foreach ($_SESSION['cart'] as $item) {
            if (intval($item['id']) !== $product_id) {
                $new_cart[] = $item;
            }
        }
        $_SESSION['cart'] = $new_cart;
        setFlashMessage('success', 'Product removed from cart!');
    }
    
    header('Location: ' . BASE_URL . '/public/cart.php');
    exit();
}

// If GET request, redirect to cart page
header('Location: ' . BASE_URL . '/public/cart.php');
exit();
?>
