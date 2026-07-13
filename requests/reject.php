<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$id = (int)($_GET['id'] ?? 0);

try {
    $stmt = $pdo->prepare("UPDATE requests SET status = 'rejected', approved_by = ?, approved_at = NOW() WHERE id = ?");
    $stmt->execute([$_SESSION['user_id'], $id]);
    setFlash('success', 'Request rejected');
} catch (PDOException $e) {
    setFlash('danger', 'Error rejecting request');
}

header('Location: index.php');
exit;
?>