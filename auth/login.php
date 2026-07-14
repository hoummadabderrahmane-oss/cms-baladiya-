<?php
session_start();
require_once "../config/database.php";

if (isset($_SESSION['user_id'])) {
    header("Location: ../admin/dashboard.php");
        exit;
        }

        $error = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $email = trim($_POST["email"]);
                $password = $_POST["password"];

                    if (empty($email) || empty($password)) {
                            $error = "Veuillez remplir tous les champs / يرجى ملء جميع الحقول";
                                } else {

                                        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                                                $stmt->execute([$email]);

                                                        if ($stmt->rowCount() == 1) {

                                                                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                                                                                if (password_verify($password, $user["password"])) {

                                                                                                $_SESSION["user_id"] = $user["id"];
                                                                                                                $_SESSION["full_name"] = $user["full_name"];
                                                                                                                                $_SESSION["role"] = $user["role"];

                                                                                                                                                header("Location: ../admin/dashboard.php");
                                                                                                                                                                exit;

                                                                                                                                                                            } else {
                                                                                                                                                                                            $error = "Mot de passe incorrect / كلمة المرور غير صحيحة";
                                                                                                                                                                                                        }

                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                            $error = "Utilisateur introuvable / المستخدم غير موجود";
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        ?>

                                                                                                                                                                                                                                        <!DOCTYPE html>
                                                                                                                                                                                                                                        <html lang="fr">
                                                                                                                                                                                                                                        <head>
                                                                                                                                                                                                                                        <meta charset="UTF-8">
                                                                                                                                                                                                                                        <meta name="viewport" content="width=device-width, initial-scale=1">

                                                                                                                                                                                                                                        <title>SGC - Login</title>

                                                                                                                                                                                                                                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

                                                                                                                                                                                                                                        <style>

                                                                                                                                                                                                                                        body{
                                                                                                                                                                                                                                            background:#f4f6f9;
                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                            .login-box{
                                                                                                                                                                                                                                                max-width:420px;
                                                                                                                                                                                                                                                    margin:auto;
                                                                                                                                                                                                                                                        margin-top:70px;
                                                                                                                                                                                                                                                            border-radius:15px;
                                                                                                                                                                                                                                                                padding:30px;
                                                                                                                                                                                                                                                                    background:white;
                                                                                                                                                                                                                                                                        box-shadow:0 0 20px rgba(0,0,0,.1);
                                                                                                                                                                                                                                                                        }

                                                                                                                                                                                                                                                                        .logo{
                                                                                                                                                                                                                                                                            width:120px;
                                                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                                            .btn-success{
                                                                                                                                                                                                                                                                                background:#0B6E4F;
                                                                                                                                                                                                                                                                                    border:none;
                                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                                    </style>

                                                                                                                                                                                                                                                                                    </head>

                                                                                                                                                                                                                                                                                    <body>

                                                                                                                                                                                                                                                                                    <div class="login-box text-center">

                                                                                                                                                                                                                                                                                    <img src="../assets/images/logo.png" class="logo mb-3">

                                                                                                                                                                                                                                                                                    <h3>Système de Gestion Communale</h3>
                                                                                                                                                                                                                                                                                    <h5>نظام إدارة الجماعة</h5>

                                                                                                                                                                                                                                                                                    <hr>

                                                                                                                                                                                                                                                                                    <?php if($error!=""){ ?>

                                                                                                                                                                                                                                                                                    <div class="alert alert-danger">

                                                                                                                                                                                                                                                                                    <?= $error ?>

                                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                    <?php } ?>

                                                                                                                                                                                                                                                                                    <form method="POST">

                                                                                                                                                                                                                                                                                    <div class="mb-3 text-start">

                                                                                                                                                                                                                                                                                    <label>Email</label>

                                                                                                                                                                                                                                                                                    <input
                                                                                                                                                                                                                                                                                    type="email"
                                                                                                                                                                                                                                                                                    name="email"
                                                                                                                                                                                                                                                                                    class="form-control"
                                                                                                                                                                                                                                                                                    required>

                                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                    <div class="mb-3 text-start">

                                                                                                                                                                                                                                                                                    <label>Mot de passe</label>

                                                                                                                                                                                                                                                                                    <input
                                                                                                                                                                                                                                                                                    type="password"
                                                                                                                                                                                                                                                                                    name="password"
                                                                                                                                                                                                                                                                                    class="form-control"
                                                                                                                                                                                                                                                                                    required>

                                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                    <button class="btn btn-success w-100">

                                                                                                                                                                                                                                                                                    Se connecter | تسجيل الدخول

                                                                                                                                                                                                                                                                                    </button>

                                                                                                                                                                                                                                                                                    </form>

                                                                                                                                                                                                                                                                                    <hr>

                                                                                                                                                                                                                                                                                    <small>

                                                                                                                                                                                                                                                                                    © 2026 Commune

                                                                                                                                                                                                                                                                                    </small>

                                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                    </body>
                                                                                                                                                                                                                                                                                    </html>