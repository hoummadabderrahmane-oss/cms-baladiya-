<?php
require_once __DIR__ . '/../includes/header.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';

$where = [];
$params = [];

if ($search) {
    $where[] = "(r.title LIKE ? OR r.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($typeFilter) {
    $where[] = "r.type = ?";
    $params[] = $typeFilter;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$sql = "SELECT r.*, u.fullname as author FROM reports r 
        LEFT JOIN users u ON r.created_by = u.id $whereClause 
        ORDER BY r.created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll();

$countSql = "SELECT COUNT(*) FROM reports r $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$pages = ceil($total / $perPage);

if (isset($_GET['delete']) && isAdmin()) {
    $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    flashMessage('success', 'Report deleted!');
    redirect('index.php');
}

if (isset($_POST['save'])) {
    $stmt = $pdo->prepare("INSERT INTO reports (title, content, type, created_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['title'],
        $_POST['content'],
        $_POST['type'],
        $_SESSION['user_id'] ?? 1
    ]);
    flashMessage('success', 'Report added!');
    redirect('index.php');
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Reports</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg"></i> New Report
    </button>
</div>

<form method="GET" class="mb-4 d-flex gap-2">
    <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo sanitize($search); ?>">
    <select name="type" class="form-select" style="width: auto;">
        <option value="">All Types</option>
        <option value="monthly" <?php echo $typeFilter == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
        <option value="annual" <?php echo $typeFilter == 'annual' ? 'selected' : ''; ?>>Annual</option>
        <option value="incident" <?php echo $typeFilter == 'incident' ? 'selected' : ''; ?>>Incident</option>
    </select>
    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
</form>

<div class="row">
    <?php foreach ($reports as $r): ?>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <span class="badge bg-<?php 
                    echo $r['type'] == 'monthly' ? 'primary' : 
                         ($r['type'] == 'annual' ? 'success' : 'warning'); 
                ?>"><?php echo $r['type']; ?></span>
                <small class="text-muted"><?php echo date('Y-m-d', strtotime($r['created_at'])); ?></small>
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo sanitize($r['title']); ?></h5>
                <p class="card-text"><?php echo nl2br(sanitize(substr($r['content'], 0, 200))); ?>...</p>
                <small class="text-muted">By: <?php echo sanitize($r['author'] ?? 'Unknown'); ?></small>
            </div>
            <div class="card-footer">
                <a href="view.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-info">View</a>
                <?php if (isAdmin()): ?>
                <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger btn-delete">Delete</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5>New Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Type</label>
                        <select name="type" class="form-select">
                            <option value="monthly">Monthly</option>
                            <option value="annual">Annual</option>
                            <option value="incident">Incident</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Content</label>
                        <textarea name="content" class="form-control" rows="10" required></textarea>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>