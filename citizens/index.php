<?php
$pageTitle = 'Citizens';
require_once __DIR__ . '/../includes/header.php';
requireAuth();

// Search & Filter
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

$page = (int)($_GET['page'] ?? 1);
$perPage = 10;

// Build query
$where = [];
$params = [];

if ($search) {
    $where[] = "(full_name LIKE ? OR id_number LIKE ? OR phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status) {
    $where[] = "status = ?";
    $params[] = $status;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM citizens $whereClause");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();

$pagination = paginate($page, $perPage, $total);

// Fetch citizens
$stmt = $pdo->prepare("SELECT * FROM citizens $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?");
foreach ($params as $i => $param) {
    $stmt->bindValue($i + 1, $param);
}
$stmt->bindValue(count($params) + 1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $pagination['offset'], PDO::PARAM_INT);
$stmt->execute();
$citizens = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> Citizens</h2>
    <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Citizen</a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, ID, or phone..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0" id="citizensTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>ID Number</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citizens as $c): ?>
                    <tr>
                        <td>#<?= $c['id'] ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;">
                                    <span class="text-white fw-bold small"><?= strtoupper(substr($c['full_name'], 0, 1)) ?></span>
                                </div>
                                <?= htmlspecialchars($c['full_name']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($c['id_number']) ?></td>
                        <td><?= htmlspecialchars($c['phone']) ?></td>
                        <td><?= htmlspecialchars($c['address']) ?></td>
                        <td>
                            <span class="badge bg-<?= $c['status'] === 'active' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($c['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="view.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                            <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                            <a href="delete.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-danger btn-delete" onclick="return confirm('Delete this citizen?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($pagination['totalPages'] > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
        <li class="page-item <?= $i === $pagination['page'] ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>