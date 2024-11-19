<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Redirige vers la page de connexion s'il est déjà connecté
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<header>
        <?php include '../php/header.php'; ?>
</header>
<body>
    <h1>Vous devez être connecté pour accéder à cette page</h1>
    <p>Veuillez vous <a href="login.php">connecter</a> pour continuer.</p>
</body>
</html>
