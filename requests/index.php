<?php
$pageTitle = 'Requests';
require_once __DIR__ . '/../includes/header.php';
requireAuth();

$status = $_GET['status'] ?? '';
$where = $status ? "WHERE r.status = ?" : "";
$params = $status ? [$status] : [];

$stmt = $pdo->prepare("SELECT r.*, c.full_name as citizen_name FROM requests r LEFT JOIN citizens c ON r.citizen_id = c.id $where ORDER BY r.created_at DESC");
$stmt->execute($params);
$requests = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-inbox"></i> Requests</h2>
    <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Request</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
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
                        <td>#<?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['citizen_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($r['request_type']) ?></td>
                        <td><?= htmlspecialchars(substr($r['description'], 0, 50)) ?>...</td>
                        <td>
                            <span class="badge bg-<?= $r['status'] === 'approved' ? 'success' : ($r['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($r['status']) ?>
                            </span>
                        </td>
                        <td><?= formatDate($r['created_at']) ?></td>
                        <td>
                            <?php if ($r['status'] === 'pending'): ?>
                            <a href="approve.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')"><i class="bi bi-check-lg"></i></a>
                            <a href="reject.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject this request?')"><i class="bi bi-x-lg"></i></a>
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