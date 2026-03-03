# 🚀 Setup Guide - Bio-Organic Herbal Solutions

Complete installation and configuration guide for the e-commerce platform.

---

## 📋 Prerequisites

Before installation, ensure you have:

- **XAMPP** (for Linux/Windows/Mac)
  - PHP 8.0 or higher
  - MySQL 5.7 or higher
  - Apache 2.4 or higher
- **Web Browser** (Chrome, Firefox, Edge)
- **Text Editor** (VS Code, Sublime, etc.)

---

## 🔧 Installation Steps

### Step 1: Install XAMPP

**For Linux:**
```bash
# Download XAMPP
wget https://www.apachefriends.org/xampp-files/8.2.12/xampp-linux-x64-8.2.12-0-installer.run

# Make executable
chmod +x xampp-linux-x64-8.2.12-0-installer.run

# Run installer
sudo ./xampp-linux-x64-8.2.12-0-installer.run

# Start services
sudo /opt/lampp/lampp start
```

**For Windows:**
- Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
- Run installer
- Install to `C:\xampp`
- Start Apache and MySQL from XAMPP Control Panel

**For Mac:**
- Download XAMPP for Mac
- Install and start services

---

### Step 2: Clone/Copy Project Files

**Option A: Git Clone (if you have repository)**
```bash
cd /opt/lampp/htdocs/
git clone <repository-url> Bio-Organic-Herbal-Solutions
```

**Option B: Manual Copy**
```bash
# Copy project folder to htdocs
cp -r Bio-Organic-Herbal-Solutions /opt/lampp/htdocs/

# Or on Windows: Copy folder to C:\xampp\htdocs\
```

---

### Step 3: Set File Permissions (Linux/Mac only)

```bash
# Navigate to project directory
cd /opt/lampp/htdocs/Bio-Organic-Herbal-Solutions

# Set ownership
sudo chown -R daemon:daemon .

# Set directory permissions
sudo find . -type d -exec chmod 755 {} \;

# Set file permissions
sudo find . -type f -exec chmod 644 {} \;

# Set uploads directory writable
sudo chmod -R 777 uploads/
```

---

### Step 4: Create Database

**Method 1: Using phpMyAdmin (Recommended)**

1. Open browser: `http://localhost/phpmyadmin`
2. Click "New" in left sidebar
3. Database name: `bio_organic_herbal`
4. Collation: `utf8mb4_general_ci`
5. Click "Create"
6. Click "Import" tab
7. Choose file: `database/bio_organic_herbal.sql` (if provided)
8. Click "Go"

**Method 2: Using MySQL Command Line**

```bash
# Start MySQL shell
/opt/lampp/bin/mysql -u root -p

# Create database
CREATE DATABASE bio_organic_herbal CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

# Exit MySQL
exit;

# Import SQL file (if provided)
/opt/lampp/bin/mysql -u root -p bio_organic_herbal < database/bio_organic_herbal.sql
```

---

### Step 5: Configure Database Connection

Edit the file: `app/config/database.php`

```php
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Your MySQL username
define('DB_PASS', '');              // Your MySQL password (empty by default)
define('DB_NAME', 'bio_organic_herbal');
define('DB_CHARSET', 'utf8mb4');

// Create PDO connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
```

**If you have a MySQL password, update:**
```php
define('DB_PASS', 'your_password_here');
```

---

### Step 6: Create Admin Account

**Option A: Using SQL Query**

Access phpMyAdmin or MySQL shell and run:

```sql
INSERT INTO users (name, email, password, role, status) 
VALUES (
    'Admin User',
    'admin@bioorganic.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'admin',
    1
);
```

**Option B: Using PHP Script**

Create a temporary file `create_admin.php` in root directory:

```php
<?php
require_once 'app/config/database.php';

$name = 'Admin User';
$email = 'admin@bioorganic.com';
$password = password_hash('admin123', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'admin', 1)");
$stmt->execute([$name, $email, $password]);

echo "Admin user created successfully!";
?>
```

Run: `http://localhost/Bio-Organic-Herbal-Solutions/create_admin.php`

**⚠️ Delete this file after use!**

---

### Step 7: Verify Installation

**1. Check Apache & MySQL Status**

```bash
# Linux
sudo /opt/lampp/lampp status

# Windows - Open XAMPP Control Panel
# Ensure Apache and MySQL are running (green)
```

**2. Test Customer Site**

Open browser: `http://localhost/Bio-Organic-Herbal-Solutions/public/`

You should see:
- Homepage with logo
- Product categories
- Navigation menu
- Footer with contact info

**3. Test Admin Panel**

Open browser: `http://localhost/Bio-Organic-Herbal-Solutions/admin/login.php`

Login with:
- Email: `admin@bioorganic.com`
- Password: `admin123`

You should see:
- Dashboard with statistics
- Sidebar menu (Products, Categories, Orders, etc.)

---

## 🔍 Troubleshooting

### Issue 1: "Database connection failed"

**Solution:**
```bash
# Check MySQL is running
sudo /opt/lampp/lampp status

# Restart MySQL
sudo /opt/lampp/lampp restart

# Verify credentials in app/config/database.php
```

### Issue 2: "Page not found" or 404 errors

**Solution:**
```bash
# Enable mod_rewrite
sudo a2enmod rewrite

# Or edit Apache config
sudo nano /opt/lampp/etc/httpd.conf

# Find and change:
AllowOverride None
# To:
AllowOverride All

# Restart Apache
sudo /opt/lampp/lampp restart
```

### Issue 3: Images not loading

**Solution:**
```bash
# Check permissions
cd /opt/lampp/htdocs/Bio-Organic-Herbal-Solutions
sudo chmod -R 777 uploads/
sudo chmod -R 755 public/assets/

# Verify files exist
ls -la uploads/
ls -la public/assets/images/
```

### Issue 4: Admin login not working

**Solution:**
```sql
-- Check admin user exists
SELECT * FROM users WHERE role = 'admin';

-- Reset admin password
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'admin@bioorganic.com';
-- Password is now: admin123
```

### Issue 5: "Internal Server Error" (500)

**Solution:**
```bash
# Check PHP errors
tail -f /opt/lampp/logs/error_log

# Check .htaccess syntax
cat .htaccess

# Verify PHP version
/opt/lampp/bin/php -v
# Should be 8.0 or higher
```

### Issue 6: Slow loading or external resource errors

**Solution:**
- Ensure Bootstrap files are in `public/assets/css/` and `public/assets/js/`
- Check SVG placeholders exist in `public/assets/images/`
- Verify no external CDN links in header/footer

---

## 🔐 Security Configuration

### 1. Change Default Admin Password

After first login:
1. Go to Admin Panel → Profile
2. Change password from `admin123` to a strong password

### 2. Secure Database

```sql
-- Change MySQL root password (if empty)
ALTER USER 'root'@'localhost' IDENTIFIED BY 'strong_password_here';
FLUSH PRIVILEGES;
```

Update `app/config/database.php` with new password.

### 3. File Permissions (Production)

```bash
# Restrict permissions
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;

# Only uploads writable
sudo chmod -R 755 uploads/
```

### 4. Disable Directory Listing

Already configured in `.htaccess`:
```apache
Options -Indexes
```

---

## 📊 Database Structure

### Tables Overview:

| Table | Purpose | Key Fields |
|-------|---------|------------|
| users | User accounts | id, email, password, role |
| products | Product catalog | id, name, price, stock |
| categories | Product categories | id, name |
| product_images | Product photos | id, product_id, image_path |
| orders | Customer orders | id, user_id, total, status |
| order_items | Order line items | id, order_id, product_id, quantity |
| wishlist | Saved products | id, user_id, product_id |
| reviews | Product reviews | id, user_id, product_id, rating |
| messages | Contact messages | id, name, email, message |

---

## 🌐 Access URLs

**Customer Site:**
- Homepage: `http://localhost/Bio-Organic-Herbal-Solutions/public/`
- Shop: `http://localhost/Bio-Organic-Herbal-Solutions/public/shop.php`
- Contact: `http://localhost/Bio-Organic-Herbal-Solutions/public/contact.php`

**Admin Panel:**
- Login: `http://localhost/Bio-Organic-Herbal-Solutions/admin/login.php`
- Dashboard: `http://localhost/Bio-Organic-Herbal-Solutions/admin/dashboard.php`
- Products: `http://localhost/Bio-Organic-Herbal-Solutions/admin/products.php`

**phpMyAdmin:**
- URL: `http://localhost/phpmyadmin`

---

## 📝 Post-Installation Checklist

- [ ] XAMPP installed and running
- [ ] Database created (`bio_organic_herbal`)
- [ ] Database connection configured
- [ ] Admin user created
- [ ] File permissions set correctly
- [ ] Customer site accessible
- [ ] Admin panel login works
- [ ] Products page loads
- [ ] Images display correctly
- [ ] Cart functionality works
- [ ] Default password changed

---

## 🎯 Next Steps

### Add Sample Data

1. **Add Categories:**
   - Go to Admin → Categories
   - Add: Herbs, Oils, Powders, Teas, etc.

2. **Add Products:**
   - Go to Admin → Products → Add Product
   - Fill details, upload images
   - Set price and stock

3. **Test Customer Experience:**
   - Browse products as customer
   - Add to cart
   - Complete checkout
   - Verify order in admin

### Customize

1. **Logo:**
   - Replace `public/assets/images/elo.png` with your logo
   - Recommended size: 200x60px

2. **Contact Info:**
   - Already configured: WhatsApp +880 1792-457594
   - Facebook: Bio.Organic.Herbal.Solutions

3. **Colors/Styling:**
   - Edit `public/assets/css/style.css` (if exists)
   - Customize Bootstrap theme

---

## 🆘 Getting Help

### Log Files

**Apache Logs:**
```bash
# Error log
tail -f /opt/lampp/logs/error_log

# Access log
tail -f /opt/lampp/logs/access_log
```

**PHP Errors:**
Check browser console (F12) or Apache error log

### Common Commands

```bash
# Start XAMPP
sudo /opt/lampp/lampp start

# Stop XAMPP
sudo /opt/lampp/lampp stop

# Restart XAMPP
sudo /opt/lampp/lampp restart

# Check status
sudo /opt/lampp/lampp status

# Open MySQL shell
/opt/lampp/bin/mysql -u root -p
```

---

## 📞 Support

**Documentation:**
- README.md - Project overview
- This file - Installation guide

**Check:**
1. Apache and MySQL running
2. Database credentials correct
3. File permissions proper
4. Error logs for details

---

## ✅ Installation Complete!

Your Bio-Organic Herbal Solutions e-commerce platform is ready!

**Admin Access:**
- URL: `http://localhost/Bio-Organic-Herbal-Solutions/admin/login.php`
- Email: `admin@bioorganic.com`
- Password: `admin123` (change immediately!)

**Customer Site:**
- URL: `http://localhost/Bio-Organic-Herbal-Solutions/public/`

---

**Happy Selling! 🌿**
