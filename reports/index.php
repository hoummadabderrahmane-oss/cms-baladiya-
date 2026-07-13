<?php
$pageTitle = 'Reports';
require_once __DIR__ . '/../includes/header.php';
requireAuth();

// Statistics
$totalCitizens = $pdo->query("SELECT COUNT(*) FROM citizens")->fetchColumn();
$activeCitizens = $pdo->query("SELECT COUNT(*) FROM citizens WHERE status = 'active'")->fetchColumn();
$totalDocuments = $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
$pendingRequests = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'pending'")->fetchColumn();
$approvedRequests = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'approved'")->fetchColumn();
$rejectedRequests = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'rejected'")->fetchColumn();

// Citizens by city
$cities = $pdo->query("SELECT city, COUNT(*) as count FROM citizens WHERE city IS NOT NULL AND city != '' GROUP BY city ORDER BY count DESC LIMIT 10")->fetchAll();

// Requests by type
$reqTypes = $pdo->query("SELECT request_type, COUNT(*) as count FROM requests GROUP BY request_type")->fetchAll();

// Monthly registrations (last 6 months)
$monthly = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM citizens WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-graph-up"></i> Reports & Analytics</h2>
    <button onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer"></i> Print Report</button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card primary text-center">
            <div class="card-body">
                <h3><?= $totalCitizens ?></h3>
                <p class="text-muted mb-0">Total Citizens</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card success text-center">
            <div class="card-body">
                <h3><?= $activeCitizens ?></h3>
                <p class="text-muted mb-0">Active Citizens</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card warning text-center">
            <div class="card-body">
                <h3><?= $totalDocuments ?></h3>
                <p class="text-muted mb-0">Total Documents</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card danger text-center">
            <div class="card-body">
                <h3><?= $pendingRequests ?></h3>
                <p class="text-muted mb-0">Pending Requests</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Request Status -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">Request Status</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-circle-fill text-success"></i> Approved</span>
                    <span class="fw-bold"><?= $approvedRequests ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-circle-fill text-warning"></i> Pending</span>
                    <span class="fw-bold"><?= $pendingRequests ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-circle-fill text-danger"></i> Rejected</span>
                    <span class="fw-bold"><?= $rejectedRequests ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total</span>
                    <span class="fw-bold"><?= $approvedRequests + $pendingRequests + $rejectedRequests ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Citizens by City -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">Citizens by City</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>City</th><th class="text-end">Count</th></tr></thead>
                        <tbody>
                            <?php foreach ($cities as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['city']) ?></td>
                                <td class="text-end fw-bold"><?= $c['count'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests by Type -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">Requests by Type</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Type</th><th class="text-end">Count</th></tr></thead>
                        <tbody>
                            <?php foreach ($reqTypes as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['request_type']) ?></td>
                                <td class="text-end fw-bold"><?= $r['count'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Registrations -->
<div class="card">
    <div class="card-header">Monthly Registrations (Last 6 Months)</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table mb-0" id="monthlyTable">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>New Citizens</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthly as $m): ?>
                    <tr>
                        <td><?= date('F Y', strtotime($m['month'] . '-01')) ?></td>
                        <td><?= $m['count'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button onclick="exportTableToCSV('monthlyTable', 'monthly_registrations.csv')" class="btn btn-sm btn-outline-success mt-3">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
        </button>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>