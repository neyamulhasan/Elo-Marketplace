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
            
            // Check if product already in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $product_id) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
            }
            
            setFlashMessage('success', 'Product added to cart successfully!');
        }
    } elseif ($action === 'update') {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
                $item['quantity'] = max(1, $quantity);
                break;
            }
        }
        setFlashMessage('success', 'Cart updated successfully!');
    } elseif ($action === 'remove') {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($product_id) {
            return $item['id'] != $product_id;
        });
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
        setFlashMessage('success', 'Product removed from cart!');
    }
    
    header('Location: /Bio-Organic-Herbal-Solutions/public/cart.php');
    exit();
}

// If GET request, redirect to cart page
header('Location: /Bio-Organic-Herbal-Solutions/public/cart.php');
exit();
?>
