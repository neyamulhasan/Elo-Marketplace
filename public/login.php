<?php
require_once '../config/config.php';
require_once '../app/helpers/session.php';
require_once '../app/helpers/validation.php';
require_once '../app/models/User.php';

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

$errors = [];
$success = false;

// Check if already logged in
if (isLoggedIn()) {
    header('Location: /Bio-Organic-Herbal-Solutions/public/');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
    
    if ($action === 'login') {
        // Login
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
            
            if ($user) {
                // Check if admin trying to login on customer side
                if ($user['role'] === 'admin') {
                    $errors[] = 'Admin users should login through admin panel';
                } else {
                    setUserSession($user);
                    setFlashMessage('success', 'Welcome back, ' . $user['name'] . '!');
                    
                    // Redirect
                    $redirect = $_GET['redirect'] ?? 'index.php';
                    header('Location: /Bio-Organic-Herbal-Solutions/public/' . $redirect);
                    exit();
                }
            } else {
                $errors[] = 'Invalid email or password';
            }
        }
    } else {
        // Register
        $name = sanitizeString($_POST['name'] ?? '');
        $email = sanitizeString($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $phone = sanitizeString($_POST['phone'] ?? '');
        $address = sanitizeString($_POST['address'] ?? '');
        
        if (!validateRequired($name)) {
            $errors[] = 'Name is required';
        }
        
        if (!validateEmail($email)) {
            $errors[] = 'Invalid email address';
        }
        
        if ($userModel->emailExists($email)) {
            $errors[] = 'Email already registered';
        }
        
        if (!validatePassword($password)) {
            $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        }
        
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match';
        }
        
        if (empty($errors)) {
            $data = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'address' => $address
            ];
            
            if ($userModel->register($data)) {
                $success = true;
                setFlashMessage('success', 'Registration successful! Please login.');
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}

$page_title = "Login / Register";
include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="text-center mb-4">Customer Login</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Registration successful! Please login below.
                </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Login Form -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0">Login</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="login">
                                
                                <div class="mb-3">
                                    <label for="login_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="login_email" name="email" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="login_password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="login_password" name="password" required>
                                </div>
                                
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Register Form -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0">Register</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="register">
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="01XXXXXXXXX">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-person-plus"></i> Register
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
