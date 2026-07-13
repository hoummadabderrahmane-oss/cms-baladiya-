<?php
$pageTitle = 'New Request';
require_once __DIR__ . '/../includes/header.php';
requireAuth();

$citizens = $pdo->query("SELECT id, full_name FROM citizens WHERE status = 'active' ORDER BY full_name")->fetchAll();
$preselected = $_GET['citizen_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO requests (citizen_id, request_type, description, status, created_by) VALUES (?, ?, ?, 'pending', ?)");
    $stmt->execute([
        $_POST['citizen_id'],
        sanitize($_POST['request_type']),
        sanitize($_POST['description']),
        $_SESSION['user_id']
    ]);
    setFlash('success', 'Request submitted successfully!');
    header('Location: index.php');
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> New Request</h2>
    <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Citizen *</label>
                <select name="citizen_id" class="form-select" required>
                    <option value="">-- Select Citizen --</option>
                    <?php foreach ($citizens as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $preselected == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Request Type *</label>
                <select name="request_type" class="form-select" required>
                    <option value="Residence Certificate">Residence Certificate</option>
                    <option value="Birth Certificate">Birth Certificate</option>
                    <option value="Marriage Certificate">Marriage Certificate</option>
                    <option value="Permit Request">Permit Request</option>
                    <option value="Complaint">Complaint</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-control" rows="5" required placeholder="Describe the request in detail..."></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Submit Request</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>