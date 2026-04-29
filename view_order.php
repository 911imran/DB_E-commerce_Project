<?php
require_once 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['id'] ?? 0;

// Check if user is admin or owns the order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order || ($order['customer_id'] != $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin')) {
    echo "<div class='alert alert-error'>Order not found or access denied.</div>";
    include 'footer.php';
    exit;
}

// Fetch items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.price, p.image 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<h2>Order Details #<?= $order['id'] ?></h2>
<p style="color: var(--text-muted); margin-bottom: 2rem;">Placed on: <?= date('M d, Y H:i', strtotime($order['order_date'])) ?></p>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <div class="cart-item">
                            <img src="images/<?= htmlspecialchars($item['image']) ?>" class="cart-item-img" onerror="this.onerror=null;this.src='https://placehold.co/80x80/png?text=No+Img'">
                            <span style="font-weight: 500;"><?= htmlspecialchars($item['name']) ?></span>
                        </div>
                    </td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td style="font-weight: 600;">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: 600; font-size: 1.1rem; padding: 1.5rem;">Total Amount:</td>
                <td style="font-weight: 700; font-size: 1.2rem; color: var(--primary-color); padding: 1.5rem;">$<?= number_format($order['total_amount'], 2) ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<div style="margin-top: 2rem;">
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <a href="admin/manage_orders.php" class="btn btn-secondary">Back to Orders</a>
    <?php else: ?>
        <a href="order_history.php" class="btn btn-secondary">Back to My Orders</a>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
