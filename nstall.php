<?php
/**
 * ============================================
 * CMS Baladiya - Installation automatique
 * ============================================
 */

define('SGC_ACCESS', true);

// Paramètres
$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'sgc_db';

try {
    // Connexion sans base de données
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Créer la base de données
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✅ Base de données <b>$dbName</b> créée</p>";
    
    // Utiliser la base
    $pdo->exec("USE $dbName");
    
    // Lire et exécuter database.sql
    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        die("<p>❌ Fichier database.sql non trouvé</p>");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Supprimer CREATE DATABASE et USE
    $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE .*?;/i', '', $sql);
    
    // Exécuter les requêtes
    $pdo->exec($sql);
    
    echo "<p>✅ Tables créées avec succès</p>";
    
    // Vérifier l'admin
    $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE email = 'admin@commune.ma'");
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        echo "<p>✅ Administrateur par défaut créé</p>";
    }
    
    echo "<hr>";
    echo "<h2>🎉 Installation terminée!</h2>";
    echo "<p><a href='index.php' style='font-size:18px;color:#1a5f2a;'><b>Accéder au système →</b></a></p>";
    echo "<hr>";
    echo "<p><b>Login par défaut:</b><br>";
    echo "Email: admin@commune.ma<br>";
    echo "Password: password</p>";
    
} catch (PDOException $e) {
    die("<h1>❌ Erreur</h1><p>" . $e->getMessage() . "</p>");
}