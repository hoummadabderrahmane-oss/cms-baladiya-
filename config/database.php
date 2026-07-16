<?php
/**
 * ============================================
 * CMS Baladiya - Configuration de la base de données
 * ============================================
 */

// Empêcher l'accès direct
if (!defined('SGC_ACCESS')) {
    define('SGC_ACCESS', true);
}

// Paramètres de connexion
define('DB_HOST', 'localhost');
define('DB_NAME', 'sgc_db');
define('DB_USER', 'root');
define('DB_PASS', '');          // ← Modifier si vous avez un mot de passe
define('DB_CHARSET', 'utf8mb4');

// Options PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci"
];

// Afficher les erreurs en développement
error_reporting(E_ALL);
ini_set('display_errors', '1');

/**
 * Obtenir la connexion PDO
 * @return PDO
 */
function getDB(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $GLOBALS['options']);
        } catch (PDOException $e) {
            error_log("Erreur connexion DB: " . $e->getMessage());
            die("
                <!DOCTYPE html>
                <html lang='fr'>
                <head>
                    <meta charset='UTF-8'>
                    <title>Erreur - CMS Baladiya</title>
                    <link href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css' rel='stylesheet'>
                    <style>
                        body { 
                            background: linear-gradient(135deg, #1a5f2a 0%, #2d8a3e 100%); 
                            min-height: 100vh; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center;
                            font-family: 'Poppins', sans-serif;
                        }
                        .error-card {
                            background: white;
                            border-radius: 20px;
                            padding: 3rem;
                            max-width: 500px;
                            text-align: center;
                            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                        }
                        .error-icon {
                            font-size: 4rem;
                            color: #dc3545;
                            margin-bottom: 1rem;
                        }
                    </style>
                </head>
                <body>
                    <div class='error-card'>
                        <div class='error-icon'><i class='fas fa-database'></i></div>
                        <h2 class='text-danger mb-3'>Erreur de connexion</h2>
                        <p class='text-muted mb-4'>
                            Impossible de se connecter à la base de données.<br><br>
                            <strong>Vérifiez que:</strong><br>
                            1. XAMPP est démarré (Apache + MySQL)<br>
                            2. La base <b>sgc_db</b> existe dans PHPMyAdmin<br>
                            3. Les identifiants sont corrects<br><br>
                            <a href='http://localhost/phpmyadmin' target='_blank' class='btn btn-success'>
                                Ouvrir PHPMyAdmin
                            </a>
                        </p>
                        <hr>
                        <small class='text-muted'>Erreur: " . htmlspecialchars($e->getMessage()) . "</small>
                    </div>
                </body>
                </html>
            ");
        }
    }
    
    return $pdo;
}

/**
 * Logger une activité dans le journal
 */
function logActivity(string $action, string $table = null, int $recordId = null, string $details = null): void {
    try {
        $db = getDB();
        $userId = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $stmt = $db->prepare("
            INSERT INTO journal_activites (utilisateur_id, action, table_concernee, enregistrement_id, details, adresse_ip)
            VALUES (:user_id, :action, :table, :record_id, :details, :ip)
        ");
        
        $stmt->execute([
            ':user_id'    => $userId,
            ':action'     => $action,
            ':table'      => $table,
            ':record_id'  => $recordId,
            ':details'    => $details,
            ':ip'         => $ip
        ]);
    } catch (PDOException $e) {
        error_log("Erreur log: " . $e->getMessage());
    }
}