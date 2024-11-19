<?php
session_start();  // Démarre la session

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');  // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

// Connexion à la base de données
include '../login/db.php';

// Récupérer les informations de l'utilisateur
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Vérifier si l'utilisateur a le bon rang (par exemple, rang 3 pour un administrateur)
if ($user['rank'] != 3) {
    header('Location: ../error/norank.php');  // Redirige vers une page d'erreur si l'utilisateur n'a pas le bon rang
    exit();
}
?>