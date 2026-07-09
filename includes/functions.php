<?php
require_once __DIR__ . "/../config/database.php";

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAuth() {
    if (!isLoggedIn()) {
        redirect('../auth/login.php');
    }
}

function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        redirect('../dashboard/index.php');
    }
}

function flashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function showFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return "<div class='alert alert-{$flash['type']} alert-dismissible fade show'>
            {$flash['message']}
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    }
    return '';
}

function paginate($pdo, $table, $where = '', $params = [], $perPage = 10) {
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($page - 1) * $perPage;
    
    $sql = "SELECT * FROM $table";
    $countSql = "SELECT COUNT(*) FROM $table";
    
    if ($where) {
        $sql .= " WHERE $where";
        $countSql .= " WHERE $where";
    }
    
    $sql .= " LIMIT $perPage OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll();
    
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    
    return [
        'items' => $items,
        'page' => $page,
        'perPage' => $perPage,
        'total' => $total,
        'pages' => ceil($total / $perPage)
    ];
}