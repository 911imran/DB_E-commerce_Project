<?php
require_once '../db.php';
require_once 'admin_check.php';
include '../header.php';

// Stats
$user_count = $pdo->query("SELECT COUNT(*) FROM customers WHERE role='customer'")->fetchColumn();
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status='Completed'")->fetchColumn() ?: 0;
?>

<h2>Admin Dashboard</h2>
<p style="color: var(--text-muted); margin-bottom: 2rem;">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>

<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Total Customers</h3>
        <p><?= $user_count ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Products</h3>
        <p><?= $product_count ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Orders</h3>
        <p><?= $order_count ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Revenue</h3>
        <p>$<?= number_format($revenue, 2) ?></p>
    </div>
</div>

<div style="display: flex; gap: 1rem; margin-top: 2rem;">
    <a href="manage_products.php" class="btn btn-primary">Manage Products</a>
    <a href="manage_orders.php" class="btn btn-secondary">Manage Orders</a>
    <a href="manage_users.php" class="btn btn-primary" style="background: var(--text-main)">Manage Users</a>
</div>

<?php include '../footer.php'; ?>
