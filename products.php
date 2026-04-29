<?php
require_once 'db.php';
include 'header.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// Build query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

$query .= " ORDER BY id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch categories for filter
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<h2>All Products</h2>

<form method="GET" class="search-bar">
    <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>" class="search-input">
    <select name="category" class="search-input" style="max-width: 200px;">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary">Filter</button>
</form>

<div class="product-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image" onerror="this.onerror=null;this.src='https://placehold.co/300x200/png?text=No+Image';">
            <div class="product-info">
                <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($product['category']) ?></p>
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
    <?php if(empty($products)): ?>
        <p>No products found matching your criteria.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
