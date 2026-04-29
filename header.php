<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store</title>
    <link rel="stylesheet" href="/ecommerce/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/ecommerce/index.php" class="brand">🛍️ Storefront</a>
            <div class="nav-links">
                <a href="/ecommerce/products.php">Products</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="/ecommerce/cart.php">Cart 🛒</a>
                    <a href="/ecommerce/order_history.php">Orders</a>
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="/ecommerce/admin/dashboard.php">Admin Panel</a>
                    <?php endif; ?>
                    <a href="/ecommerce/logout.php" class="btn btn-danger">Logout</a>
                <?php else: ?>
                    <a href="/ecommerce/login.php" class="btn btn-primary">Login</a>
                    <a href="/ecommerce/register.php" class="btn btn-secondary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="container">
