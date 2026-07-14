<?php
require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"]);
        $email = trim($_POST["email"]);
            $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

                $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                    $check->execute([$email]);

                        if ($check->rowCount() > 0) {

                                $message = "Ce compte existe déjà.";

                                    } else {

                                            $stmt = $pdo->prepare("
                                                        INSERT INTO users(full_name,email,password,role)
                                                                    VALUES(?,?,?,?)
                                                                            ");

                                                                                    $stmt->execute([
                                                                                                $full_name,
                                                                                                            $email,
                                                                                                                        $password,
                                                                                                                                    "admin"
                                                                                                                                            ]);

                                                                                                                                                    $message = "Administrateur créé avec succès.";
                                                                                                                                                        }
                                                                                                                                                        }
                                                                                                                                                        ?>

                                                                                                                                                        <!DOCTYPE html>
                                                                                                                                                        <html lang="fr">
                                                                                                                                                        <head>

                                                                                                                                                        <meta charset="UTF-8">
                                                                                                                                                        <meta name="viewport" content="width=device-width, initial-scale=1">

                                                                                                                                                        <title>Create Admin</title>

                                                                                                                                                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

                                                                                                                                                        </head>

                                                                                                                                                        <body class="bg-light">

                                                                                                                                                        <div class="container mt-5">

                                                                                                                                                        <div class="card shadow">

                                                                                                                                                        <div class="card-header bg-success text-white">

                                                                                                                                                        Créer le premier administrateur

                                                                                                                                                        </div>

                                                                                                                                                        <div class="card-body">

                                                                                                                                                        <?php if($message!=""){ ?>

                                                                                                                                                        <div class="alert alert-info">

                                                                                                                                                        <?= $message ?>

                                                                                                                                                        </div>

                                                                                                                                                        <?php } ?>

                                                                                                                                                        <form method="POST">

                                                                                                                                                        <div class="mb-3">

                                                                                                                                                        <label>Nom complet</label>

                                                                                                                                                        <input
                                                                                                                                                        type="text"
                                                                                                                                                        name="full_name"
                                                                                                                                                        class="form-control"
                                                                                                                                                        required>

                                                                                                                                                        </div>

                                                                                                                                                        <div class="mb-3">

                                                                                                                                                        <label>Email</label>

                                                                                                                                                        <input
                                                                                                                                                        type="email"
                                                                                                                                                        name="email"
                                                                                                                                                        class="form-control"
                                                                                                                                                        required>

                                                                                                                                                        </div>

                                                                                                                                                        <div class="mb-3">

                                                                                                                                                        <label>Mot de passe</label>

                                                                                                                                                        <input
                                                                                                                                                        type="password"
                                                                                                                                                        name="password"
                                                                                                                                                        class="form-control"
                                                                                                                                                        required>

                                                                                                                                                        </div>

                                                                                                                                                        <button class="btn btn-success">

                                                                                                                                                        Créer l'administrateur

                                                                                                                                                        </button>

                                                                                                                                                        </form>

                                                                                                                                                        </div>

                                                                                                                                                        </div>

                                                                                                                                                        </div>

                                                                                                                                                        </body>
                                                                                                                                                        </html>