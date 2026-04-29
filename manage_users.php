<?php
require_once '../db.php';
require_once 'admin_check.php';

// Handle Role Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];
    
    // Prevent self demotion
    if ($user_id == $_SESSION['user_id']) {
        $error = "You cannot change your own role.";
    } else {
        $stmt = $pdo->prepare("UPDATE customers SET role = ? WHERE id = ?");
        $stmt->execute([$role, $user_id]);
        header('Location: manage_users.php?msg=updated');
        exit;
    }
}

include '../header.php';

$stmt = $pdo->query("SELECT * FROM customers ORDER BY id DESC");
$users = $stmt->fetchAll();
?>

<h2>Manage Users</h2>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
    <div class="alert alert-success">User role updated successfully.</div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="table-container" style="margin-top: 2rem;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td style="font-weight: 500;"><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['address']) ?></td>
                    <td>
                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                            <span style="background: var(--primary-color); color: white; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.85rem;">Admin (You)</span>
                        <?php else: ?>
                            <form method="POST" action="" style="display: flex; gap: 0.5rem; align-items: center;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="role" class="form-control" style="padding: 0.3rem; width: auto;">
                                    <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button type="submit" name="update_role" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Update</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../footer.php'; ?>
