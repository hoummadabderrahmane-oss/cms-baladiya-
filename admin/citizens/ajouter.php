<?php
/**
 * ============================================
 * SGC - Ajouter un Citoyen
 * ============================================
 */
define('SGC_ACCESS', true);
require_once '../auth/auth_check.php';
require_once '../config/database.php';

$pageTitle = 'Ajouter un Citoyen';
$pageIcon = 'fa-user-plus';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation
    $cin = trim($_POST['cin'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $nom_ar = trim($_POST['nom_ar'] ?? '');
    $prenom_ar = trim($_POST['prenom_ar'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?: null;
    $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
    $sexe = $_POST['sexe'] ?? '';
    $etat_civil = $_POST['etat_civil'] ?? 'celibataire';
    $adresse = trim($_POST['adresse'] ?? '');
    $quartier = trim($_POST['quartier'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $profession = trim($_POST['profession'] ?? '');
    $niveau_etude = trim($_POST['niveau_etude'] ?? '');
    $situation_sociale = $_POST['situation_sociale'] ?? 'normal';
    $nombre_enfants = (int)($_POST['nombre_enfants'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    
    // Validation
    if (empty($cin)) $errors[] = "Le CIN est obligatoire";
    if (empty($nom)) $errors[] = "Le nom est obligatoire";
    if (empty($prenom)) $errors[] = "Le prénom est obligatoire";
    if (empty($sexe)) $errors[] = "Le sexe est obligatoire";
    
    if (empty($errors)) {
        try {
            $db = getDB();
            
            // Vérifier CIN unique
            $stmt = $db->prepare("SELECT id FROM citoyens WHERE cin = ?");
            $stmt->execute([$cin]);
            if ($stmt->fetch()) {
                $errors[] = "Ce CIN existe déjà dans le système";
            } else {
                $stmt = $db->prepare("
                    INSERT INTO citoyens (
                        cin, nom, prenom, nom_ar, prenom_ar, date_naissance, lieu_naissance,
                        sexe, etat_civil, adresse, quartier, telephone, email, profession,
                        niveau_etude, situation_sociale, nombre_enfants, notes, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $cin, $nom, $prenom, $nom_ar, $prenom_ar, $date_naissance, $lieu_naissance,
                    $sexe, $etat_civil, $adresse, $quartier, $telephone, $email, $profession,
                    $niveau_etude, $situation_sociale, $nombre_enfants, $notes, $currentUser['id']
                ]);
                
                $newId = $db->lastInsertId();
                logActivity('ajout_citoyen', 'citoyens', $newId, "Citoyen: $prenom $nom");
                
                $_SESSION['success'] = "Citoyen ajouté avec succès!";
                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'ajout: " . $e->getMessage();
            error_log("Erreur ajout citoyen: " . $e->getMessage());
        }
    }
}

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Ajouter un Citoyen</h4>
            <p class="text-muted mb-0">Remplissez le formulaire ci-dessous</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Erreurs:</strong>
            <ul class="mb-0 mt-1">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="data-table-card">
        <div class="data-table-header">
            <h5><i class="fas fa-user-plus me-2 text-success"></i>Informations du citoyen</h5>
        </div>
        
        <form method="POST" action="" class="p-4">
            <div class="row">
                <!-- CIN -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">CIN <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="cin" required 
                           placeholder="Ex: AB123456" maxlength="20"
                           value="<?= htmlspecialchars($_POST['cin'] ?? '') ?>">
                </div>
                
                <!-- Sexe -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Sexe <span class="text-danger">*</span></label>
                    <select class="form-select" name="sexe" required>
                        <option value="">Choisir...</option>
                        <option value="M" <?= ($_POST['sexe'] ?? '') == 'M' ? 'selected' : '' ?>>Homme</option>
                        <option value="F" <?= ($_POST['sexe'] ?? '') == 'F' ? 'selected' : '' ?>>Femme</option>
                    </select>
                </div>
                
                <!-- État civil -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">État civil</label>
                    <select class="form-select" name="etat_civil">
                        <option value="celibataire" <?= ($_POST['etat_civil'] ?? '') == 'celibataire' ? 'selected' : '' ?>>Célibataire</option>
                        <option value="marie" <?= ($_POST['etat_civil'] ?? '') == 'marie' ? 'selected' : '' ?>>Marié(e)</option>
                        <option value="divorce" <?= ($_POST['etat_civil'] ?? '') == 'divorce' ? 'selected' : '' ?>>Divorcé(e)</option>
                        <option value="veuf" <?= ($_POST['etat_civil'] ?? '') == 'veuf' ? 'selected' : '' ?>>Veuf/Veuve</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <!-- Nom -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nom" required 
                           placeholder="Nom en français"
                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                </div>
                
                <!-- Prénom -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Prénom <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="prenom" required 
                           placeholder="Prénom en français"
                           value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                </div>
            </div>
            
            <div class="row">
                <!-- Nom AR -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nom (Arabe)</label>
                    <input type="text" class="form-control" name="nom_ar" dir="rtl" 
                           placeholder="الاسم بالعربية"
                           value="<?= htmlspecialchars($_POST['nom_ar'] ?? '') ?>">
                </div>
                
                <!-- Prénom AR -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Prénom (Arabe)</label>
                    <input type="text" class="form-control" name="prenom_ar" dir="rtl" 
                           placeholder="النسب بالعربية"
                           value="<?= htmlspecialchars($_POST['prenom_ar'] ?? '') ?>">
                </div>
            </div>
            
            <div class="row">
                <!-- Date naissance -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Date de naissance</label>
                    <input type="date" class="form-control" name="date_naissance"
                           value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>">
                </div>
                
                <!-- Lieu naissance -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Lieu de naissance</label>
                    <input type="text" class="form-control" name="lieu_naissance" 
                           placeholder="Ville de naissance"
                           value="<?= htmlspecialchars($_POST['lieu_naissance'] ?? '') ?>">
                </div>
            </div>
            
            <div class="row">
                <!-- Téléphone -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Téléphone</label>
                    <input type="tel" class="form-control" name="telephone" 
                           placeholder="06XXXXXXXX"
                           value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                </div>
                
                <!-- Email -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" class="form-control" name="email" 
                           placeholder="email@exemple.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <!-- Profession -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Profession</label>
                    <input type="text" class="form-control" name="profession" 
                           placeholder="Profession"
                           value="<?= htmlspecialchars($_POST['profession'] ?? '') ?>">
                </div>
            </div>
            
            <div class="row">
                <!-- Quartier -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Quartier</label>
                    <input type="text" class="form-control" name="quartier" 
                           placeholder="Nom du quartier"
                           value="<?= htmlspecialchars($_POST['quartier'] ?? '') ?>">
                </div>
                
                <!-- Niveau d'étude -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Niveau d'études</label>
                    <select class="form-select" name="niveau_etude">
                        <option value="">Choisir...</option>
                        <option value="Aucun" <?= ($_POST['niveau_etude'] ?? '') == 'Aucun' ? 'selected' : '' ?>>Aucun</option>
                        <option value="Primaire" <?= ($_POST['niveau_etude'] ?? '') == 'Primaire' ? 'selected' : '' ?>>Primaire</option>
                        <option value="Collège" <?= ($_POST['niveau_etude'] ?? '') == 'Collège' ? 'selected' : '' ?>>Collège</option>
                        <option value="Lycée" <?= ($_POST['niveau_etude'] ?? '') == 'Lycée' ? 'selected' : '' ?>>Lycée</option>
                        <option value="Bac" <?= ($_POST['niveau_etude'] ?? '') == 'Bac' ? 'selected' : '' ?>>Bac</option>
                        <option value="Bac+2" <?= ($_POST['niveau_etude'] ?? '') == 'Bac+2' ? 'selected' : '' ?>>Bac+2</option>
                        <option value="Bac+3" <?= ($_POST['niveau_etude'] ?? '') == 'Bac+3' ? 'selected' : '' ?>>Bac+3</option>
                        <option value="Bac+5" <?= ($_POST['niveau_etude'] ?? '') == 'Bac+5' ? 'selected' : '' ?>>Bac+5</option>
                        <option value="Doctorat" <?= ($_POST['niveau_etude'] ?? '') == 'Doctorat' ? 'selected' : '' ?>>Doctorat</option>
                    </select>
                </div>
                
                <!-- Situation sociale -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Situation sociale</label>
                    <select class="form-select" name="situation_sociale">
                        <option value="normal" <?= ($_POST['situation_sociale'] ?? '') == 'normal' ? 'selected' : '' ?>>Normal</option>
                        <option value="handicap" <?= ($_POST['situation_sociale'] ?? '') == 'handicap' ? 'selected' : '' ?>>Handicap</option>
                        <option value="veuf" <?= ($_POST['situation_sociale'] ?? '') == 'veuf' ? 'selected' : '' ?>>Veuf/Veuve</option>
                        <option value="orphelin" <?= ($_POST['situation_sociale'] ?? '') == 'orphelin' ? 'selected' : '' ?>>Orphelin</option>
                        <option value="demuni" <?= ($_POST['situation_sociale'] ?? '') == 'demuni' ? 'selected' : '' ?>>Défavorisé</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <!-- Nombre d'enfants -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Nombre d'enfants</label>
                    <input type="number" class="form-control" name="nombre_enfants" min="0" 
                           value="<?= htmlspecialchars($_POST['nombre_enfants'] ?? '0') ?>">
                </div>
            </div>
            
            <!-- Adresse -->
            <div class="mb-3">
                <label class="form-label fw-bold">Adresse complète</label>
                <textarea class="form-control" name="adresse" rows="2" 
                          placeholder="Adresse détaillée du citoyen"><?= htmlspecialchars($_POST['adresse'] ?? '') ?></textarea>
            </div>
            
            <!-- Notes -->
            <div class="mb-4">
                <label class="form-label fw-bold">Notes / Observations</label>
                <textarea class="form-control" name="notes" rows="3" 
                          placeholder="Informations complémentaires..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-add">
                    <i class="fas fa-save me-2"></i>Enregistrer le citoyen
                </button>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>