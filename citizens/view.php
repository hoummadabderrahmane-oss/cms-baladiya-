<?php
$pageTitle = 'View Citizen';
require_once __DIR__ . '/../includes/header.php';
requireAuth();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT c.*, u.full_name as created_by_name FROM citizens c LEFT JOIN users u ON c.created_by = u.id WHERE c.id = ?");
$stmt->execute([$id]);
$citizen = $stmt->fetch();

if (!$citizen) {
    setFlash('danger', 'Citizen not found');
    header('Location: index.php');
    exit;
}

// Get related documents
$docs = $pdo->prepare("SELECT * FROM documents WHERE citizen_id = ? ORDER BY created_at DESC");
$docs->execute([$id]);
$documents = $docs->fetchAll();

// Get related requests
$reqs = $pdo->prepare("SELECT * FROM requests WHERE citizen_id = ? ORDER BY created_at DESC");
$reqs->execute([$id]);
$requests = $reqs->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person"></i> Citizen Details</h2>
    <div>
        <a href="edit.php?id=<?= $id ?>" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:100px;height:100px;">
                    <span class="text-white display-4"><?= strtoupper(substr($citizen['full_name'], 0, 1)) ?></span>
                </div>
                <h4><?= htmlspecialchars($citizen['full_name']) ?></h4>
                <span class="badge bg-<?= $citizen['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($citizen['status']) ?></span>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-transparent d-flex justify-content-between">
                    <span class="text-muted">ID Number</span>
                    <span><?= htmlspecialchars($citizen['id_number']) ?></span>
                </li>
                <li class="list-group-item bg-transparent d-flex justify-content-between">
                    <span class="text-muted">Date of Birth</span>
                    <span><?= $citizen['date_of_birth'] ? formatDate($citizen['date_of_birth']) : 'N/A' ?></span>
                </li>
                <li class="list-group-item bg-transparent d-flex justify-content-between">
                    <span class="text-muted">Gender</span>
                    <span><?= ucfirst($citizen['gender']) ?></span>
                </li>
                <li class="list-group-item bg-transparent d-flex justify-content-between">
                    <span class="text-muted">Phone</span>
                    <span><?= htmlspecialchars($citizen['phone'] ?? 'N/A') ?></span>
                </li>
                <li class="list-group-item bg-transparent d-flex justify-content-between">
                    <span class="text-muted">Email</span>
                    <span><?= htmlspecialchars($citizen['email'] ?? 'N/A') ?></span>
                </li>
                <li class="list-group-item bg-transparent d-flex justify-content-between">
                    <span class="text-muted">Address</span>
                    <span><?= htmlspecialchars($citizen['address'] ?? 'N/A') ?></span>
                </li>
                <li class="list-group-item bg-transparent d-flex justify-content-between">
                    <span class="text-muted">City</span>
                    <span><?= htmlspecialchars($citizen['city'] ?? 'N/A') ?></span>
                </li>
                <li class="list-group-item bg-transparent d-flex justify-content-between">
                    <span class="text-muted">Registered</span>
                    <span><?= formatDate($citizen['created_at']) ?></span>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Documents -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Documents</h5>
                <a href="/documents/create.php?citizen_id=<?= $id ?>" class="btn btn-sm btn-primary">Add Document</a>
            </div>
            <div class="card-body p-0">
                <?php if ($documents): ?>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Title</th><th>Type</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($documents as $d): ?>
                            <tr>
                                <td><a href="/documents/view.php?id=<?= $d['id'] ?>"><?= htmlspecialchars($d['title']) ?></a></td>
                                <td><?= htmlspecialchars($d['doc_type']) ?></td>
                                <td><?= formatDate($d['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center py-3">No documents found</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Requests -->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0"><i class="bi bi-inbox"></i> Requests</h5>
                <a href="/requests/create.php?citizen_id=<?= $id ?>" class="btn btn-sm btn-primary">New Request</a>
            </div>
            <div class="card-body p-0">
                <?php if ($requests): ?>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Type</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['request_type']) ?></td>
                                <td><span class="badge bg-<?= $r['status'] === 'approved' ? 'success' : ($r['status'] === 'pending' ? 'warning' : 'danger') ?>"><?= ucfirst($r['status']) ?></span></td>
                                <td><?= formatDate($r['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center py-3">No requests found</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>