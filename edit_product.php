<?php
require_once '../db.php';
require_once 'admin_check.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: manage_products.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category = trim($_POST['category']);
    
    // Image Upload
    $image_name = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($tmp_name, "../images/$image_name");
    }

    if (empty($name) || empty($category) || $price <= 0) {
        $error = 'Please fill all required fields correctly.';
    } else {
        $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, stock=?, category=?, image=? WHERE id=?");
        if ($stmt->execute([$name, $price, $stock, $category, $image_name, $id])) {
            $success = 'Product updated successfully!';
            // Refresh product data
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();
        } else {
            $error = 'Failed to update product.';
        }
    }
}
include '../header.php';
?>

<div class="form-container" style="max-width: 600px;">
    <h2>Edit Product</h2>
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex: 1;">
                <label>Price ($) *</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Stock *</label>
                <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($product['stock']) ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>Category *</label>
            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($product['category']) ?>" required>
        </div>
        <div class="form-group">
            <label>Product Image (Leave blank to keep current)</label>
            <div style="margin-bottom: 0.5rem;">
                <img src="../images/<?= htmlspecialchars($product['image']) ?>" alt="Current Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
            </div>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Update Product</button>
            <a href="manage_products.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Back to Products</a>
        </div>
    </form>
</div>

<?php include '../footer.php'; ?>
