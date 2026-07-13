<?php
$pageTitle = 'Add User';
require_once __DIR__ . '/../includes/header.php';
requireAuth();

if (!hasRole('admin')) {
    setFlash('danger', 'Access denied.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email, role, department, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            sanitize($_POST['username']),
            $password,
            sanitize($_POST['full_name']),
            sanitize($_POST['email']),
            $_POST['role'],
            sanitize($_POST['department']),
            $_POST['status']
        ]);
        setFlash('success', 'User created successfully!');
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $error = 'Username or email already exists';
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-plus"></i> Add User</h2>
    <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="col-md-6">
                <label class="form-label">Full Name *</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control" placeholder="e.g. IT, HR, Legal">
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Create User</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>