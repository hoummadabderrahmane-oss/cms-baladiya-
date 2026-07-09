<?php
require_once __DIR__ . '/../includes/header.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM citizens WHERE id = ?");
$stmt->execute([$id]);
$c = $stmt->fetch();

if (!$c) redirect('index.php');

// Documents
$docs = $pdo->prepare("SELECT * FROM documents WHERE citizen_id = ?");
$docs->execute([$id]);
$documents = $docs->fetchAll();

// Requests
$reqs = $pdo->prepare("SELECT * FROM requests WHERE citizen_id = ?");
$reqs->execute([$id]);
$requests = $reqs->fetchAll();
?>

<h2>Citizen Details</h2>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-person-circle fs-1 text-primary"></i>
                <h4 class="mt-2"><?php echo sanitize($c['fullname']); ?></h4>
                <p class="text-muted"><?php echo sanitize($c['cin']); ?></p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><i class="bi bi-telephone me-2"></i> <?php echo sanitize($c['phone']); ?></li>
                <li class="list-group-item"><i class="bi bi-envelope me-2"></i> <?php echo sanitize($c['email']); ?></li>
                <li class="list-group-item"><i class="bi bi-calendar me-2"></i> <?php echo $c['birth_date']; ?></li>
                <li class="list-group-item"><i class="bi bi-gender-ambiguous me-2"></i> <?php echo $c['gender']; ?></li>
                <li class="list-group-item"><i class="bi bi-geo-alt me-2"></i> <?php echo sanitize($c['address']); ?></li>
            </ul>
            <div class="card-body">
                <a href="edit.php?id=<?php echo $c['id']; ?>" class="btn btn-warning w-100"><i class="bi bi-pencil"></i> Edit</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Documents (<?php echo count($documents); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($documents)): ?>
                <p class="text-muted">No documents</p>
                <?php else: ?>
                <table class="table table-sm">
                    <thead><tr><th>Title</th><th>Type</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($documents as $d): ?>
                        <tr>
                            <td><?php echo sanitize($d['title']); ?></td>
                            <td><?php echo $d['type']; ?></td>
                            <td><span class="badge bg-<?php echo $d['status'] == 'approved' ? 'success' : 'warning'; ?>"><?php echo $d['status']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>Requests (<?php echo count($requests); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($requests)): ?>
                <p class="text-muted">No requests</p>
                <?php else: ?>
                <table class="table table-sm">
                    <thead><tr><th>Type</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($requests as $r): ?>
                        <tr>
                            <td><?php echo $r['type']; ?></td>
                            <td><span class="badge bg-<?php echo $r['status'] == 'completed' ? 'success' : 'warning'; ?>"><?php echo $r['status']; ?></span></td>
                            <td><?php echo date('Y-m-d', strtotime($r['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>