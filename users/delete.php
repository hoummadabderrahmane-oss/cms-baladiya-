<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

if (!hasRole('admin')) {
    header('Location: index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id == $_SESSION['user_id']) {
    setFlash('danger', 'Cannot delete yourself!');
} else {
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    setFlash('success', 'User deleted successfully');
}

header('Location: index.php');
exit;
?>