<?php
require_once __DIR__ . '/../includes/header.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];

if ($search) {
    $where = "d.title LIKE ? OR c.fullname LIKE ?";
    $params = ["%$search%", "%$search%"];
}

// Pagination m3a JOIN
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$sql = "SELECT d.*, c.fullname as citizen_name FROM documents d 
        LEFT JOIN citizens c ON d.citizen_id = c.id";
$countSql = "SELECT COUNT(*) FROM documents d LEFT JOIN citizens c ON d.citizen_id = c.id";

if ($where) {
    $sql .= " WHERE $where";
    $countSql .= " WHERE $where";
}

$sql .= " ORDER BY d.created_at DESC LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$documents = $stmt->fetchAll();

$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$pages = ceil($total / $perPage);

// Delete
if (isset($_GET['delete']) && isAdmin()) {
    $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    flashMessage('success', 'Document deleted!');
    redirect('index.php');
}

// Citizens list for dropdown
$citizens = $pdo->query("SELECT id, fullname FROM citizens ORDER BY fullname")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Documents</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg"></i> Add Document
    </button>
</div>

<form method="GET" class="mb-4">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo sanitize($search); ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
    </div>
</form>

<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Citizen</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $d): ?>
                <tr>
                    <td><?php echo $d['id']; ?></td>
                    <td><?php echo sanitize($d['title']); ?></td>
                    <td><?php echo sanitize($d['citizen_name'] ?? 'N/A'); ?></td>
                    <td><?php echo str_replace('_', ' ', $d['type']); ?></td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $d['status'] == 'approved' ? 'success' : 
                                 ($d['status'] == 'rejected' ? 'danger' : 'warning'); 
                        ?>"><?php echo $d['status']; ?></span>
                    </td>
                    <td><?php echo date('Y-m-d', strtotime($d['created_at'])); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $d['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <?php if (isAdmin()): ?>
                        <a href="?delete=<?php echo $d['id']; ?>" class="btn btn-sm btn-danger btn-delete"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="create.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Citizen</label>
                        <select name="citizen_id" class="form-select" required>
                            <option value="">Select Citizen</option>
                            <?php foreach ($citizens as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo sanitize($c['fullname']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Type</label>
                        <select name="type" class="form-select">
                            <option value="birth_certificate">Birth Certificate</option>
                            <option value="residence_certificate">Residence Certificate</option>
                            <option value="marriage_certificate">Marriage Certificate</option>
                            <option value="death_certificate">Death Certificate</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>File</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>