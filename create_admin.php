<?php
/**
 * ============================================
 * SGC - Création d'un administrateur (XAMPP)
 * ============================================
 * 
 * Utilisation:
 * 1. Mettez ce fichier dans le dossier sgc/
 * 2. Accédez à: http://localhost/sgc/create_admin.php
 * 3. Remplissez le formulaire
 * 4. SUPPRIMEZ ce fichier après utilisation!
 */

define('SGC_ACCESS', true);

// Vérifier si le formulaire est soumis
$message = '';
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $commune = trim($_POST['commune'] ?? '');
    $role = $_POST['role'] ?? 'admin';
    
    // Validation
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom est requis";
    if (empty($prenom)) $errors[] = "Le prénom est requis";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
    if (strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    if ($password !== $password_confirm) $errors[] = "Les mots de passe ne correspondent pas";
    if (empty($commune)) $errors[] = "Le nom de la commune est requis";
    
    if (empty($errors)) {
        try {
            $db = getDB();
            
            // Vérifier si l'email existe déjà
            $stmt = $db->prepare("SELECT id FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $message = "Cet email existe déjà!";
                $type = "danger";
            } else {
                // Hasher le mot de passe
                $hash = password_hash($password, PASSWORD_BCRYPT);
                
                $stmt = $db->prepare("
                    INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, commune, statut)
                    VALUES (?, ?, ?, ?, ?, ?, 1)
                ");
                $stmt->execute([$nom, $prenom, $email, $hash, $role, $commune]);
                
                $message = "Administrateur créé avec succès! Vous pouvez maintenant vous connecter.";
                $type = "success";
            }
        } catch (PDOException $e) {
            $message = "Erreur: " . $e->getMessage();
            $type = "danger";
        }
    } else {
        $message = implode("<br>", $errors);
        $type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGC - Créer un Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a5f2a 0%, #2d8a3e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #1a5f2a, #2d8a3e);
            color: white;
            border-radius: 20px 20px 0 0 !important;
            padding: 2rem;
            text-align: center;
        }
        .form-control { border-radius: 10px; padding: 0.75rem 1rem; }
        .btn-create {
            background: linear-gradient(135deg, #1a5f2a, #2d8a3e);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            color: white;
            font-weight: 600;
            width: 100%;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user-shield fa-3x mb-3"></i>
                        <h3>Créer un Administrateur</h3>
                        <p class="mb-0">SGC - Système de Gestion des Citoyens</p>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="warning-box">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            <strong>Attention!</strong> Supprimez ce fichier après avoir créé l'administrateur.
                        </div>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
                                <?= $message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="nom" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" class="form-control" name="prenom" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" name="password" minlength="6" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirmer</label>
                                    <input type="password" class="form-control" name="password_confirm" minlength="6" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Commune</label>
                                <input type="text" class="form-control" name="commune" placeholder="Ex: Commune de Casablanca" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Rôle</label>
                                <select class="form-select" name="role">
                                    <option value="super_admin">Super Administrateur</option>
                                    <option value="admin">Administrateur</option>
                                    <option value="agent">Agent</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-create">
                                <i class="fas fa-user-plus me-2"></i>Créer l'administrateur
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="index.php" class="text-decoration-none text-success">
                                <i class="fas fa-arrow-left me-1"></i>Retour à la connexion
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>