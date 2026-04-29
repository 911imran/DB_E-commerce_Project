<?php
require_once 'db.php';
include 'header.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='alert alert-error'>Product not found.</div>";
    include 'footer.php';
    exit;
}
?>

<div style="display: flex; gap: 3rem; background: var(--card-bg); padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-top: 2rem;">
    <div style="flex: 1;">
        <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100%; border-radius: 8px; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/300x200/png?text=No+Image';">
    </div>
    <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
        <p style="color: var(--primary-color); font-weight: 600; text-transform: uppercase; font-size: 0.9rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($product['category']) ?></p>
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;"><?= htmlspecialchars($product['name']) ?></h1>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-main); margin-bottom: 1rem;">$<?= number_format($product['price'], 2) ?></div>
        
        <p style="color: var(--text-muted); margin-bottom: 2rem;">
            Stock Available: <?= htmlspecialchars($product['stock']) ?>
        </p>

        <?php if ($product['stock'] > 0): ?>
            <form method="POST" action="add_to_cart.php" style="display: flex; gap: 1rem; max-width: 300px;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="form-control" style="width: 80px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Add to Cart</button>
            </form>
        <?php else: ?>
            <button class="btn btn-secondary" disabled>Out of Stock</button>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
