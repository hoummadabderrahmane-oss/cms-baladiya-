<?php

session_start();

require "../config/database.php";

$error = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];


    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE email = ?"
    );

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

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>SGC - Connexion</title>



<!-- Bootstrap 5 -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">



<!-- Font Awesome -->

<link rel="stylesheet" 
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">



<!-- Custom CSS -->

<link rel="stylesheet" href="../assets/css/login.css">


</head>



<body>



<div class="circle c1"></div>

<div class="circle c2"></div>




<div class="card shadow-lg login-card">


<div class="card-body p-5 text-center">



<img src="../assets/images/logo.png"
class="logo mb-3"
alt="SGC Logo">



<h2 class="fw-bold">
SGC
</h2>



<p class="text-muted">
Système de Gestion de Commune
</p>




<?php if($error): ?>

<div class="alert alert-danger">

<i class="fa-solid fa-circle-exclamation"></i>

<?= $error ?>

</div>

<?php endif; ?>





<form method="POST">



<div class="mb-3 text-start">


<label class="form-label">

<i class="fa-solid fa-envelope"></i>
Email

</label>


<input 
type="email"
name="email"
class="form-control"
placeholder="admin@sgc.com"
required>


</div>





<div class="mb-3 text-start">


<label class="form-label">

<i class="fa-solid fa-lock"></i>
Mot de passe

</label>



<div class="input-group">


<input
id="password"
type="password"
name="password"
class="form-control"
placeholder="****"
required>


<button 
type="button"
class="btn btn-outline-secondary"
onclick="togglePassword()">


<i id="eye" class="fa-solid fa-eye"></i>


</button>


</div>



</div>





<button 
type="submit"
name="login"
class="btn btn-success w-100 btn-login">


<i class="fa-solid fa-right-to-bracket"></i>

Connexion


</button>




</form>




<hr>


<small class="text-muted">

© SGC 2026 - Administration Communale

</small>




</div>

</div>





<script src="../assets/js/login.js"></script>


</body>

</html>