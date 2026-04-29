<?php
require_once '../db.php';
require_once 'admin_check.php';

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    
    if ($status === 'Completed') {
        $stmt_pay = $pdo->prepare("UPDATE payments SET status = 'Completed' WHERE order_id = ?");
        $stmt_pay->execute([$order_id]);
    }
    
    header('Location: manage_orders.php?msg=updated');
    exit;
}

include '../header.php';

$stmt = $pdo->query("
    SELECT o.id, c.name as customer_name, c.email, o.order_date, o.total_amount, o.status, p.payment_method
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    LEFT JOIN payments p ON o.id = p.order_id
    ORDER BY o.id DESC
");
$orders = $stmt->fetchAll();
?>

<h2>Manage Orders</h2>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
    <div class="alert alert-success">Order status updated successfully.</div>
<?php endif; ?>

<div class="table-container" style="margin-top: 2rem;">
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td style="font-weight: 600;">#<?= $order['id'] ?></td>
                    <td>
                        <?= htmlspecialchars($order['customer_name']) ?><br>
                        <small style="color: var(--text-muted)"><?= htmlspecialchars($order['email']) ?></small>
                    </td>
                    <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                    <td style="font-weight: 600; color: var(--primary-color);">$<?= number_format($order['total_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></td>
                    <td>
                        <form method="POST" action="" style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" class="form-control" style="padding: 0.3rem; width: auto;">
                                <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Processing" <?= $order['status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="Completed" <?= $order['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Update</button>
                        </form>
                    </td>
                    <td>
                        <a href="../view_order.php?id=<?= $order['id'] ?>" class="btn btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem; text-decoration: none;">View Items</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../footer.php'; ?>
