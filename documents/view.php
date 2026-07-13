<?php
$pageTitle = 'View Document';
require_once __DIR__ . '/../includes/header.php';
requireAuth();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT d.*, c.full_name as citizen_name, u.full_name as created_by_name 
                       FROM documents d 
                       LEFT JOIN citizens c ON d.citizen_id = c.id 
                       LEFT JOIN users u ON d.created_by = u.id 
                       WHERE d.id = ?");
$stmt->execute([$id]);
$doc = $stmt->fetch();

if (!$doc) {
    setFlash('danger', 'Document not found');
    header('Location: index.php');
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-earmark-text"></i> Document Details</h2>
    <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4><?= htmlspecialchars($doc['title']) ?></h4>
                <span class="badge bg-info mb-3"><?= htmlspecialchars($doc['doc_type']) ?></span>
                
                <div class="mb-3">
                    <h6 class="text-muted">Description</h6>
                    <p><?= nl2br(htmlspecialchars($doc['description'] ?? 'No description')) ?></p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Related Citizen</h6>
                        <p><?= $doc['citizen_name'] ? '<a href="/citizens/view.php?id=' . $doc['citizen_id'] . '">' . htmlspecialchars($doc['citizen_name']) . '</a>' : 'N/A' ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Created By</h6>
                        <p><?= htmlspecialchars($doc['created_by_name'] ?? 'Unknown') ?></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Created At</h6>
                        <p><?= formatDate($doc['created_at'], 'd/m/Y H:i') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">File</div>
            <div class="card-body text-center">
                <?php if ($doc['file_path']): ?>
                <i class="bi bi-file-earmark display-1 text-primary"></i>
                <p class="mt-2"><?= htmlspecialchars($doc['file_name']) ?></p>
                <a href="/<?= $doc['file_path'] ?>" target="_blank" class="btn btn-primary w-100"><i class="bi bi-download"></i> Download</a>
                <?php else: ?>
                <i class="bi bi-file-earmark-x display-1 text-muted"></i>
                <p class="mt-2 text-muted">No file attached</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>