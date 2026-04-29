<?php
require_once 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY id DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<h2>My Order History</h2>

<?php if (empty($orders)): ?>
    <div style="background: var(--card-bg); padding: 3rem; text-align: center; border-radius: 12px; margin-top: 2rem;">
        <p style="color: var(--text-muted); font-size: 1.2rem;">You haven't placed any orders yet.</p>
        <a href="products.php" class="btn btn-primary" style="margin-top: 1rem;">Start Shopping</a>
    </div>
<?php else: ?>
    <div class="table-container" style="margin-top: 2rem;">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td style="font-weight: 600;">#<?= $order['id'] ?></td>
                        <td><?= date('M d, Y H:i', strtotime($order['order_date'])) ?></td>
                        <td style="font-weight: 600; color: var(--primary-color);">$<?= number_format($order['total_amount'], 2) ?></td>
                        <td>
                            <?php 
                                $status_color = $order['status'] === 'Completed' ? 'var(--secondary-color)' : 'var(--text-muted)';
                            ?>
                            <span style="background: <?= $status_color ?>; color: white; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.85rem;">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="view_order.php?id=<?= $order['id'] ?>" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.9rem; text-decoration: none;">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
