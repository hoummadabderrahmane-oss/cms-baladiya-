<?php
require_once __DIR__ . '/../includes/header.php';

if (isset($_POST['save'])) {
    $filePath = null;
    
    if (!empty($_FILES['file']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/documents/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $fileName = time() . '_' . basename($_FILES['file']['name']);
        $filePath = 'uploads/documents/' . $fileName;
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $fileName);
    }
    
    $stmt = $pdo->prepare("INSERT INTO documents (citizen_id, title, type, file_path, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['citizen_id'],
        $_POST['title'],
        $_POST['type'],
        $filePath,
        $_POST['status'] ?? 'pending'
    ]);
    
    flashMessage('success', 'Document added!');
    redirect('index.php');
}
?>