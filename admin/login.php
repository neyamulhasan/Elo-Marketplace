<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/User.php';

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

$errors = [];

// Check if already logged in as admin
if (isAdmin()) {
    header('Location: /Bio-Organic-Herbal-Solutions/admin/dashboard.php');
    exit();
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeString($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!validateEmail($email)) {
        $errors[] = 'Invalid email address';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    if (empty($errors)) {
        $user = $userModel->login($email, $password);
        
        if ($user && $user['role'] === ROLE_ADMIN) {
            setUserSession($user);
            setFlashMessage('success', 'Welcome to Admin Panel!');
            header('Location: /Bio-Organic-Herbal-Solutions/admin/dashboard.php');
            exit();
        } else {
            $errors[] = 'Invalid admin credentials';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bio-Organic Herbal Solutions</title>
    <link href="/Bio-Organic-Herbal-Solutions/public/assets/css/vendor/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Bio-Organic-Herbal-Solutions/public/assets/css/vendor/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="/Bio-Organic-Herbal-Solutions/public/assets/images/bio.jpg" alt="Bio-Organic Herbal Solutions" style="height: 80px; margin-bottom: 15px; border-radius: 10px;">
                            <h3 class="mt-3">Admin Login</h3>
                            <p class="text-muted">Bio-Organic Herbal Solutions</p>
                        </div>
                        
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
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required autofocus>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100 btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="/Bio-Organic-Herbal-Solutions/public/" class="text-decoration-none">
                                <i class="bi bi-arrow-left"></i> Back to Website
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3 text-white">
                    <small>Default admin: admin@bioorganic.com / admin123</small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="/Bio-Organic-Herbal-Solutions/public/assets/js/vendor/bootstrap.bundle.min.js"></script>
</body>
</html>
