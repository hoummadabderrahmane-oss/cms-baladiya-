<?php
/**
 * ==========================================================
 * SGC v1.0
 * Configuration de la base de données
 * ==========================================================
 */

$host = "localhost";
$dbname = "sgc";
$username = "root";
$password = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // Gestion des erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Résultats sous forme de tableau associatif
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Désactiver l'émulation des requêtes préparées
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {

    die("Erreur de connexion : " . $e->getMessage());

}
?>