<?php
require_once 'db.php';
include 'header.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $id => $qty) {
        if ($qty > 0) {
            $_SESSION['cart'][$id] = (int)$qty;
        } else {
            unset($_SESSION['cart'][$id]);
        }
    }
    header("Location: cart.php");
    exit;
}
?>

<h2>Shopping Cart</h2>

<?php if (empty($cart)): ?>
    <div style="background: var(--card-bg); padding: 3rem; text-align: center; border-radius: 12px; margin-top: 2rem;">
        <h3 style="margin-bottom: 1rem;">Your cart is empty</h3>
        <a href="products.php" class="btn btn-primary">Continue Shopping</a>
    </div>
<?php else: ?>
    <form method="POST" action="">
        <div class="table-container" style="margin-top: 2rem;">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($cart as $id => $qty): 
                        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                        $stmt->execute([$id]);
                        $product = $stmt->fetch();
                        if ($product):
                            $subtotal = $product['price'] * $qty;
                            $total += $subtotal;
                    ?>
                        <tr>
                            <td>
                                <div class="cart-item">
                                    <img src="images/<?= htmlspecialchars($product['image']) ?>" class="cart-item-img" onerror="this.onerror=null;this.src='https://placehold.co/300x200/png?text=No+Image';">
                                    <span style="font-weight: 500;"><?= htmlspecialchars($product['name']) ?></span>
                                </div>
                            </td>
                            <td>$<?= number_format($product['price'], 2) ?></td>
                            <td>
                                <input type="number" name="quantities[<?= $id ?>]" value="<?= $qty ?>" min="1" max="<?= $product['stock'] ?>" class="form-control" style="width: 80px;">
                            </td>
                            <td style="font-weight: 600;">$<?= number_format($subtotal, 2) ?></td>
                            <td>
                                <a href="add_to_cart.php?remove=<?= $id ?>" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.9rem;">Remove</a>
                            </td>
                        </tr>
                    <?php endif; endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 2rem;">
            <button type="submit" name="update_cart" class="btn btn-secondary">Update Cart</button>
            <div class="cart-summary">
                <p style="font-size: 1.2rem; color: var(--text-muted); margin-bottom: 0.5rem;">Subtotal</p>
                <h3 style="font-size: 2rem; color: var(--text-main); margin-bottom: 1.5rem;">$<?= number_format($total, 2) ?></h3>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="checkout.php" class="btn btn-primary" style="display: block; font-size: 1.1rem; padding: 1rem;">Proceed to Checkout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary" style="display: block; font-size: 1.1rem; padding: 1rem;">Login to Checkout</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
<?php endif; ?>

<?php include 'footer.php'; ?>
