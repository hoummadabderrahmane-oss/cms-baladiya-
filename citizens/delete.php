<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$id = (int)($_GET['id'] ?? 0);

try {
    $pdo->prepare("DELETE FROM citizens WHERE id = ?")->execute([$id]);
    setFlash('success', 'Citizen deleted successfully');
} catch (PDOException $e) {
    setFlash('danger', 'Cannot delete: citizen has related records');
}

header('Location: index.php');
exit;
?>