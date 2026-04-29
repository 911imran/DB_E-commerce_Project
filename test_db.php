<?php
require_once 'db.php';
$stmt = $pdo->query('SELECT id, name, image FROM products');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($products);
?>
