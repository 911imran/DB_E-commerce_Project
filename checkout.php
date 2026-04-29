<?php
require_once 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$error = '';
$success = '';

// Calculate Total
$total = 0;
$order_items = [];
foreach ($cart as $id => $qty) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND stock >= ?");
    $stmt->execute([$id, $qty]);
    $product = $stmt->fetch();
    if ($product) {
        $total += $product['price'] * $qty;
        $order_items[] = ['id' => $id, 'qty' => $qty, 'price' => $product['price']];
    } else {
        $error = "Some items in your cart are out of stock or requested quantity is unavailable.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    try {
        $pdo->beginTransaction();

        // Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $order_id = $pdo->lastInsertId();

        // Insert Order Items and Update Stock
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        
        foreach ($order_items as $item) {
            $stmt_item->execute([$order_id, $item['id'], $item['qty']]);
            $stmt_stock->execute([$item['qty'], $item['id']]);
        }

        // Add Payment Record (Cash on Delivery)
        $stmt_payment = $pdo->prepare("INSERT INTO payments (order_id, payment_method, status) VALUES (?, 'Cash on Delivery', 'Pending')");
        $stmt_payment->execute([$order_id]);

        $pdo->commit();
        
        // Clear Cart
        unset($_SESSION['cart']);
        $success = "Order placed successfully! Your order ID is #$order_id";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to place order. Please try again.";
    }
}
?>

<div class="form-container" style="max-width: 600px;">
    <h2>Checkout</h2>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <a href="order_history.php" class="btn btn-primary">View Orders</a>
        <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
    <?php elseif ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
    <?php else: ?>
        <div style="background: #F9FAFB; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem;">Order Summary</h3>
            <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 600;">
                <span>Total Amount:</span>
                <span style="color: var(--primary-color)">$<?= number_format($total, 2) ?></span>
            </div>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" class="form-control" readonly>
                    <option value="Cash on Delivery">Cash on Delivery (COD)</option>
                </select>
                <small style="color: var(--text-muted); display: block; margin-top: 0.5rem;">For this lab, only Cash on Delivery is supported.</small>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;">Confirm Order</button>
        </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
