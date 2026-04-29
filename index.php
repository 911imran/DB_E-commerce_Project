<?php
require_once 'db.php';
include 'header.php';

// Fetch top 4 recent products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
$recent_products = $stmt->fetchAll();
?>

<div style="text-align: center; margin-bottom: 3rem;">
    <h1 style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;">Welcome to Storefront</h1>
    <p style="font-size: 1.2rem; color: var(--text-muted); margin-bottom: 2rem;">Find the best products at the best prices.</p>
    <a href="products.php" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">Shop Now</a>
</div>

<h2>Featured Products</h2>
<div class="product-grid">
    <?php foreach ($recent_products as $product): ?>
        <div class="product-card">
            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image" onerror="this.onerror=null;this.src='https://placehold.co/300x200/png?text=No+Image';">
            <div class="product-info">
                <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                <div style="margin-top: auto; display: flex; gap: 1rem;">
                    <a href="product_details.php?id=<?= $product['id'] ?>" class="btn btn-secondary" style="flex: 1;">View</a>
                    <form method="POST" action="add_to_cart.php" style="flex: 1;">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if(empty($recent_products)): ?>
        <p>No products found.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
