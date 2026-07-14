```php
<?php
session_start();
require_once "../config/database.php";

// إذا كان المستخدم مسجل الدخول
if (isset($_SESSION['user_id'])) {
    header("Location: ../admin/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (!empty($email) && !empty($password)) {

        $stmt = $pdo->prepare("
            SELECT *
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if ($user) {

            if (
                password_verify($password, $user["password"]) &&
                $user["status"] === "active"
            ) {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["fullname"] = $user["fullname"];
                $_SESSION["role"] = $user["role"];

                header("Location: ../admin/dashboard.php");
                exit();

            } else {

                $error = "Mot de passe incorrect.";

            }

        } else {

            $error = "Adresse email introuvable.";

        }

    } else {

        $error = "Veuillez remplir tous les champs.";

    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Connexion | SGC</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<link
rel="stylesheet"
href="../assets/css/login.css">

</head>

<body>

<div class="login-container">

<div class="login-card">

<div class="text-center mb-4">

<h2 class="fw-bold text-success">
SGC
</h2>

<p class="text-muted">
Système de Gestion Communale
</p>

</div>

<form method="POST">

<div class="mb-3">

<label>Email</label>

<div class="input-group">

<span class="input-group-text">

<i class="fa-solid fa-envelope"></i>

</span>

<input
type="email"
name="email"
class="form-control"
required>

</div>

</div>

<div class="mb-4">

<label>Mot de passe</label>

<div class="input-group">

<span class="input-group-text">

<i class="fa-solid fa-lock"></i>

</span>

<input
type="password"
name="password"
id="password"
class="form-control"
required>

<button
class="btn btn-outline-secondary"
type="button"
id="togglePassword">

<i class="fa-solid fa-eye"></i>

</button>

</div>

</div>

<div class="d-grid">

<button
class="btn btn-success btn-lg">

<i class="fa-solid fa-right-to-bracket"></i>

Connexion

</button>

</div>

</form>

</div>

</div>

<script>

const error =
<?= json_encode($error) ?>;

</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="../assets/js/login.js"></script>

</body>

</html>
```
