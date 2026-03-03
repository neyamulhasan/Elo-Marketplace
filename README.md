# Elo Marketplace

**Modern E-Commerce Platform — PHP / MySQL / Bootstrap 5**

Elo Marketplace is a full-featured e-commerce platform offering carefully selected, high-quality products sourced directly from trusted global manufacturers. By working closely with reliable suppliers, the platform delivers competitive prices without compromising on quality. The mission is simple: make global shopping simple, secure, and accessible — delivering value and convenience to every customer's doorstep.

---

## Overview

Elo Marketplace is built on a clean MVC-inspired architecture using core PHP, MySQL, and Bootstrap 5. It covers the complete shopping lifecycle — from product discovery and cart management through to checkout and order tracking — alongside a fully featured admin panel for day-to-day operations.

**Local Development URLs:**

- Customer Site: `http://localhost/Elo-Marketplace/public/`
- Admin Panel: `http://localhost/Elo-Marketplace/admin/`

---

## Features

### Customer Features

- Product browsing with category filtering and search
- Detailed product pages with image gallery
- Shopping cart with quantity management
- Wishlist system
- Secure checkout (Cash on Delivery)
- Order placement, tracking, and history
- Product reviews and ratings
- User registration, login, and account management
- Contact form

### Admin Features

- Dashboard with sales and order statistics
- Product management — add, edit, activate/deactivate, delete
- Multi-image product gallery management
- Category management — add, edit, delete
- Order management with status workflow (Pending → Processing → Delivered → Cancelled)
- User account management
- Contact message inbox

### Technical Features

- Fully local static assets — no external CDN dependencies
- PDO prepared statements throughout — no raw SQL interpolation
- Session-based authentication with role separation (Admin / Customer)
- Password hashing via `password_hash()` / `password_verify()`
- Input validation and server-side sanitization
- Secure file upload handling with type and size enforcement
- Apache `.htaccess` configuration with clean routing and browser caching headers
- Responsive layout supporting all viewport sizes

---

## Tech Stack

| Layer      | Technology                        |
|------------|-----------------------------------|
| Backend    | PHP 8.2                           |
| Database   | MySQL 8 (via XAMPP/LAMPP)         |
| Frontend   | Bootstrap 5.3 (local), Bootstrap Icons |
| Server     | Apache 2.4                        |
| Architecture | Custom MVC-inspired, no framework |

---

## Project Structure

```
Elo-Marketplace/
├── admin/                      # Admin panel
│   ├── dashboard.php           # Overview statistics
│   ├── products.php            # Product listing and management
│   ├── product-add.php         # Add new product
│   ├── product-edit.php        # Edit existing product
│   ├── product-images.php      # Product image gallery
│   ├── categories.php          # Category management
│   ├── orders.php              # Order listing
│   ├── order-details.php       # Order detail view
│   ├── users.php               # User management
│   ├── settings.php            # Site settings
│   └── includes/               # Admin header and footer
│
├── public/                     # Customer-facing site
│   ├── index.php               # Homepage
│   ├── shop.php                # Product listing
│   ├── product.php             # Product detail
│   ├── cart.php                # Shopping cart
│   ├── checkout.php            # Checkout
│   ├── orders.php              # Order history
│   ├── order-details.php       # Order detail
│   ├── account.php             # Account management
│   ├── contact.php             # Contact page
│   ├── login.php               # Customer login
│   ├── logout.php              # Logout
│   ├── assets/                 # CSS, JS, fonts, images
│   ├── includes/               # Public header and footer
│   └── uploads/                # Uploaded images
│       ├── products/
│       ├── categories/
│       └── settings/
│
├── app/                        # Core application logic
│   ├── models/                 # Database models (PDO)
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── ProductImage.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   └── Setting.php
│   └── helpers/                # Utility functions
│       ├── session.php
│       ├── validation.php
│       └── image_helper.php
│
├── config/
│   ├── config.php              # Global constants and base URL
│   ├── database.php            # Database credentials (not committed)
│   └── database.example.php   # Credentials template
│
├── database.sql                # Full schema with sample data
├── README.md                   # This file
└── SETUP.md                    # Installation guide
```

---

## Database

**Database Name:** `elo_marketplace`

| Table           | Description                        |
|-----------------|------------------------------------|
| `users`         | Customer and admin accounts        |
| `categories`    | Product categories                 |
| `products`      | Product catalog                    |
| `product_images`| Product image gallery              |
| `orders`        | Customer orders                    |
| `order_items`   | Line items per order               |
| `wishlist`      | Customer saved products            |
| `reviews`       | Product reviews and ratings        |
| `messages`      | Contact form submissions           |
| `settings`      | Site-wide configuration            |

---

## Default Admin Credentials

| Field    | Value               |
|----------|---------------------|
| Email    | `admin@elo.com`     |
| Password | `admin123`          |

Change the default password immediately after first login.

---

## Security

- All database queries use PDO prepared statements
- Passwords are hashed with `password_hash()` (bcrypt)
- Sessions are validated on every protected request
- Role-based access control separates admin and customer routes
- File uploads are validated by MIME type and extension
- All user inputs are sanitized before processing or display

---

## Responsive Design

The interface is built with Bootstrap 5 and tested across all standard breakpoints:

- Desktop (1920px and above)
- Laptop (1024px and above)
- Tablet (768px and above)
- Mobile (320px and above)

---

## Installation

Refer to **SETUP.md** for full step-by-step instructions.

**Quick Start:**

1. Install XAMPP or LAMPP
2. Clone or copy the project to `htdocs/Elo-Marketplace/`
3. Import `database.sql` into MySQL
4. Copy `config/database.example.php` to `config/database.php` and set your credentials
5. Start Apache and MySQL
6. Visit `http://localhost/Elo-Marketplace/public/`

---

## Common Issues

| Symptom                    | Resolution                                          |
|----------------------------|-----------------------------------------------------|
| 500 Internal Server Error  | Ensure `config/database.php` exists with valid credentials |
| Database connection error  | Verify MySQL is running and credentials are correct |
| Images not loading         | Check write permissions on `public/uploads/`        |
| Admin login fails          | Confirm credentials against the `users` table       |
| Page not found             | Verify Apache `mod_rewrite` is enabled              |

**Log locations:**

- Apache error log: `/opt/lampp/logs/error_log`
- PHP errors: configured via `php.ini` `error_log` directive

---

## License

Proprietary. All rights reserved.

---

*Elo Marketplace — Quality products, global sourcing, delivered to your doorstep.*
