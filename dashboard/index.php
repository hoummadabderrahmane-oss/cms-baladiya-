<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
requireAuth();

// Get statistics
$stats = [
    'citizens' => $pdo->query("SELECT COUNT(*) FROM citizens")->fetchColumn(),
    'documents' => $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn(),
    'requests' => $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'pending'")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn(),
];

// Recent activities
$recentCitizens = $pdo->query("SELECT * FROM citizens ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentRequests = $pdo->query("SELECT r.*, c.full_name as citizen_name FROM requests r JOIN citizens c ON r.citizen_id = c.id ORDER BY r.created_at DESC LIMIT 5")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
    <span class="text-muted"><?= date('l, F j, Y') ?></span>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase mb-1">Total Citizens</h6>
                        <h3 class="mb-0"><?= number_format($stats['citizens']) ?></h3>
                    </div>
                    <div class="text-primary"><i class="bi bi-people fs-1"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase mb-1">Documents</h6>
                        <h3 class="mb-0"><?= number_format($stats['documents']) ?></h3>
                    </div>
                    <div class="text-success"><i class="bi bi-file-earmark-text fs-1"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase mb-1">Pending Requests</h6>
                        <h3 class="mb-0"><?= number_format($stats['requests']) ?></h3>
                    </div>
                    <div class="text-warning"><i class="bi bi-inbox fs-1"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase mb-1">Active Users</h6>
                        <h3 class="mb-0"><?= number_format($stats['users']) ?></h3>
                    </div>
                    <div class="text-danger"><i class="bi bi-shield-check fs-1"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Citizens -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people"></i> Recent Citizens</h5>
                <a href="/citizens/" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>ID Number</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentCitizens as $c): ?>
                            <tr>
                                <td>
                                    <a href="/citizens/view.php?id=<?= $c['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($c['full_name']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($c['id_number']) ?></td>
                                <td><?= formatDate($c['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Requests -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-inbox"></i> Recent Requests</h5>
                <a href="/requests/" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Citizen</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentRequests as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['citizen_name']) ?></td>
                                <td><?= htmlspecialchars($r['request_type']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $r['status'] === 'approved' ? 'success' : ($r['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($r['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>