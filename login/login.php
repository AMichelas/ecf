<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Connexion réussie, stocker l'ID de l'utilisateur dans la session
        $_SESSION['user_id'] = $user['id'];

        // Rediriger vers la page appropriée selon le rank
        if ($user['rank'] == 1) {
            header('Location: ../rank/veterinaire.php');  // Redirige vers veterinaire.php
        } elseif ($user['rank'] == 2) {
            header('Location: ../rank/employer.php');  // Redirige vers employer.php
        } elseif ($user['rank'] == 3) {
            header('Location: ../rank/administrateur.php');  // Redirige vers administrateur.php
        }
        exit();  // Assure-toi que le script s'arrête ici après la redirection
    } else {
        echo "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<header>
    <div class="logo">
        <a href="../index.php">Arcadia</a>
    </div>
    <nav>
        <ul>
            <li><a href="../habitat.php">Habitat</a></li>
            <li><a href="../contacts.php">Contacts</a></li>
            <li><a href="../avis.php">Avis</a></li>
            <li><a href="login.php">Connexion</a></li>
            <li><a href="../service.php">services</a></li>
        </ul>
    </nav>
</header>

<body>
<form method="post" action="" class="login-form">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Mot de passe" required><br>
    <button type="submit">Se connecter</button>
</form>
<footer>
    <?php include '../php/footer.php'; ?>
</footer>
</body>
</html>