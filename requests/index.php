<?php
require_once __DIR__ . '/../includes/header.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

$where = [];
$params = [];

if ($search) {
    $where[] = "(r.type LIKE ? OR c.fullname LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($statusFilter) {
    $where[] = "r.status = ?";
    $params[] = $statusFilter;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$sql = "SELECT r.*, c.fullname as citizen_name FROM requests r 
        LEFT JOIN citizens c ON r.citizen_id = c.id $whereClause 
        ORDER BY r.created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

$countSql = "SELECT COUNT(*) FROM requests r LEFT JOIN citizens c ON r.citizen_id = c.id $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$pages = ceil($total / $perPage);

// Update status
if (isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['id']]);
    flashMessage('success', 'Status updated!');
    redirect('index.php');
}

// Delete
if (isset($_GET['delete']) && isAdmin()) {
    $stmt = $pdo->prepare("DELETE FROM requests WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    flashMessage('success', 'Request deleted!');
    redirect('index.php');
}

$citizens = $pdo->query("SELECT id, fullname FROM citizens ORDER BY fullname")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Requests</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg"></i> New Request
    </button>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo sanitize($search); ?>">
            <select name="status" class="form-select" style="width: auto;">
                <option value="">All Status</option>
                <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="in_progress" <?php echo $statusFilter == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="completed" <?php echo $statusFilter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="rejected" <?php echo $statusFilter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Citizen</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><?php echo sanitize($r['citizen_name'] ?? 'N/A'); ?></td>
                    <td><?php echo str_replace('_', ' ', $r['type']); ?></td>
                    <td><?php echo sanitize(substr($r['description'], 0, 50)) . '...'; ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                            <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="pending" <?php echo $r['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo $r['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo $r['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="rejected" <?php echo $r['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                            <input type="hidden" name="update_status">
                        </form>
                    </td>
                    <td><?php echo date('Y-m-d', strtotime($r['created_at'])); ?></td>
                    <td>
                        <a href="view.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <?php if (isAdmin()): ?>
                        <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger btn-delete"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
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
            <form method="POST" action="create.php">
                <div class="modal-header">
                    <h5>New Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Citizen</label>
                        <select name="citizen_id" class="form-select" required>
                            <?php foreach ($citizens as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo sanitize($c['fullname']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Type</label>
                        <select name="type" class="form-select">
                            <option value="document_request">Document Request</option>
                            <option value="complaint">Complaint</option>
                            <option value="suggestion">Suggestion</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>