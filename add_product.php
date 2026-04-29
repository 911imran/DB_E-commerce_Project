<?php
require_once '../db.php';
require_once 'admin_check.php';
include '../header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category = trim($_POST['category']);
    
    // Image Upload
    $image_name = 'default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($tmp_name, "../images/$image_name");
    }

    if (empty($name) || empty($category) || $price <= 0) {
        $error = 'Please fill all required fields correctly.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, stock, category, image) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $price, $stock, $category, $image_name])) {
            $success = 'Product added successfully!';
        } else {
            $error = 'Failed to add product.';
        }
    }
}
?>

<div class="form-container" style="max-width: 600px;">
    <h2>Add New Product</h2>
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex: 1;">
                <label>Price ($) *</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Stock *</label>
                <input type="number" name="stock" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label>Category *</label>
            <input type="text" name="category" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Save Product</button>
            <a href="manage_products.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
        </div>
    </form>
</div>

<?php include '../footer.php'; ?>
