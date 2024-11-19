<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Si non connecté, redirige vers la page de connexion
    exit();
}

// Récupère les informations de l'utilisateur
include '../login/db.php';
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Si l'utilisateur n'a pas le bon rank, affiche cette page
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<header>
        <?php include '../php/header.php'; ?>
</header>
<body>
    <h1>Accès Refusé</h1>
    <p>Vous n'avez pas les droits nécessaires pour accéder à cette page.</p>
    <p>Votre niveau d'utilisateur est <?php echo $user['rank']; ?>, mais cette page est réservée aux utilisateurs ayant un autre niveau.</p>
    <p><a href="index.php">Retour à la page d'accueil</a></p>
</body>
</html>
