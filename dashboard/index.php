<?php
require_once __DIR__ . '/../includes/header.php';

// Stats
$stats = [
    'citizens' => $pdo->query("SELECT COUNT(*) FROM citizens")->fetchColumn(),
    'documents' => $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn(),
    'requests' => $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'pending'")->fetchColumn(),
    'reports' => $pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn(),
];
?>

<h2 class="mb-4">Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-stats citizens">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Total Citizens</h6>
                        <h3><?php echo $stats['citizens']; ?></h3>
                    </div>
                    <i class="bi bi-people fs-1 text-primary"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats documents">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Documents</h6>
                        <h3><?php echo $stats['documents']; ?></h3>
                    </div>
                    <i class="bi bi-file-earmark-text fs-1 text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats requests">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Pending Requests</h6>
                        <h3><?php echo $stats['requests']; ?></h3>
                    </div>
                    <i class="bi bi-inbox fs-1 text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats reports">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Reports</h6>
                        <h3><?php echo $stats['reports']; ?></h3>
                    </div>
                    <i class="bi bi-graph-up fs-1 text-danger"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Recent Citizens</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>CIN</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent = $pdo->query("SELECT * FROM citizens ORDER BY created_at DESC LIMIT 5")->fetchAll();
                        foreach ($recent as $c): ?>
                        <tr>
                            <td><?php echo sanitize($c['fullname']); ?></td>
                            <td><?php echo sanitize($c['cin']); ?></td>
                            <td><?php echo sanitize($c['phone']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Recent Requests</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recentReq = $pdo->query("SELECT r.*, c.fullname as citizen_name 
                            FROM requests r 
                            JOIN citizens c ON r.citizen_id = c.id 
                            ORDER BY r.created_at DESC LIMIT 5")->fetchAll();
                        foreach ($recentReq as $r): ?>
                        <tr>
                            <td><?php echo sanitize($r['type']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $r['status'] == 'pending' ? 'warning' : 
                                         ($r['status'] == 'completed' ? 'success' : 'info'); 
                                ?>"><?php echo $r['status']; ?></span>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($r['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>