<?php

session_start();

require "../config/database.php";

$error = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];


    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch();


    if($user && password_verify($password, $user['password'])){

        $_SESSION['user'] = [
            "id" => $user['id'],
            "name" => $user['name'],
            "email" => $user['email'],
            "role" => $user['role']
        ];

        header("Location: ../admin/dashboard.php");
        exit();

    }else{

        $error = "Email ou mot de passe incorrect";

    }

}

?>


<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<title>SGC - Connexion</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<style>

body{

    min-height:100vh;
    background:linear-gradient(135deg,#198754,#0d6efd);

}


.login-card{

    border:none;
    border-radius:20px;

}


.logo{

    width:100px;
    height:100px;
    object-fit:contain;

}


.btn-login{

    border-radius:30px;
    padding:12px;

}


</style>


</head>


<body>


<div class="container">


<div class="row justify-content-center align-items-center vh-100">


<div class="col-md-4">


<div class="card shadow-lg login-card">


<div class="card-body p-5 text-center">


<img src="../assets/images/logo.png"
class="logo mb-3"
alt="SGC Logo">


<h3 class="fw-bold">
SGC
</h3>

<p class="text-muted">
Système de Gestion de Commune
</p>



<?php if($error): ?>

<div class="alert alert-danger">
<?= $error ?>
</div>

<?php endif; ?>



<form method="POST">


<div class="mb-3 text-start">

<label class="form-label">
Email
</label>

<input type="email"
name="email"
class="form-control"
placeholder="admin@sgc.com"
required>

</div>



<div class="mb-3 text-start">

<label class="form-label">
Mot de passe
</label>

<input type="password"
name="password"
class="form-control"
placeholder="****"
required>

</div>



<button class="btn btn-success w-100 btn-login"
name="login">

<i class="bi bi-box-arrow-in-right"></i>
Connexion

</button>


</form>


<hr>


<small class="text-muted">
© SGC 2026 - Administration Communale
</small>


</div>

</div>


</div>


</div>


</div>


</body>

</html>