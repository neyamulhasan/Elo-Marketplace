<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /Bio-Organic-Herbal-Solutions/public/contact.php');
    exit();
}

// Get and sanitize form data
$name = sanitizeString($_POST['name'] ?? '');
$email = sanitizeEmail($_POST['email'] ?? '');
$subject = sanitizeString($_POST['subject'] ?? '');
$message = sanitizeString($_POST['message'] ?? '');

// Validate
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    setFlashMessage('error', 'All fields are required.');
    header('Location: /Bio-Organic-Herbal-Solutions/public/contact.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlashMessage('error', 'Please enter a valid email address.');
    header('Location: /Bio-Organic-Herbal-Solutions/public/contact.php');
    exit();
}

// Save to database
try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO messages (name, email, subject, message) 
              VALUES (:name, :email, :subject, :message)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':message', $message);
    
    if ($stmt->execute()) {
        setFlashMessage('success', 'Thank you for your message! We will get back to you soon.');
    } else {
        setFlashMessage('error', 'Failed to send message. Please try WhatsApp: +880 1792-457594');
    }
} catch (Exception $e) {
    setFlashMessage('error', 'Failed to send message. Please contact us via WhatsApp: +880 1792-457594');
}

header('Location: /Bio-Organic-Herbal-Solutions/public/contact.php');
exit();
?>
