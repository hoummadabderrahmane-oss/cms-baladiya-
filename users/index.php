<?php
$pageTitle = 'Users';
require_once __DIR__ . '/../includes/header.php';
requireAuth();

if (!hasRole('admin')) {
    setFlash('danger', 'Access denied. Admin only.');
    header('Location: /dashboard/');
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-shield-lock"></i> User Management</h2>
    <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add User</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>#<?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['full_name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : 'info' ?>"><?= ucfirst($u['role']) ?></span></td>
                        <td><?= htmlspecialchars($u['department'] ?? 'N/A') ?></td>
                        <td>
                            <span class="badge bg-<?= $u['status'] === 'active' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($u['status']) ?>
                            </span>
                        </td>
                        <td><?= $u['last_login'] ? formatDate($u['last_login'], 'd/m/Y H:i') : 'Never' ?></td>
                        <td>
                            <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <a href="delete.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger btn-delete" onclick="return confirm('Delete this user?')"><i class="bi bi-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>