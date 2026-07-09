<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute();
$users = $stmt->fetchAll();

$total = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pages = ceil($total / $perPage);

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    flashMessage('success', 'User deleted!');
    redirect('index.php');
}

if (isset($_POST['save'])) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['fullname'], $_POST['email'], $password, $_POST['role']]);
    flashMessage('success', 'User added!');
    redirect('index.php');
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Users Management</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg"></i> Add User
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo sanitize($u['fullname']); ?></td>
                    <td><?php echo sanitize($u['email']); ?></td>
                    <td><span class="badge bg-<?php echo $u['role'] == 'admin' ? 'danger' : 'primary'; ?>"><?php echo $u['role']; ?></span></td>
                    <td><?php echo date('Y-m-d', strtotime($u['created_at'])); ?></td>
                    <td>
                        <a href="?delete=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger btn-delete" onclick="return confirm('Delete this user?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5>Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-select">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="save" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>