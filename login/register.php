<?php
include 'db.php';
include '../php/verif.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rank = $_POST['rank'];

    $sql = "INSERT INTO users (first_name, last_name, email, password, rank) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$first_name, $last_name, $email, $password, $rank]);

    echo '<div class="success-message">Inscription réussie !</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<header>
    <div class="logo">
        <a href="index.php">Arcadia</a>
    </div>
    <nav>
        <ul>
            <li><a href="../habitat.php">Habitat</a></li>
            <li><a href="../contacts.php">Contacts</a></li>
            <li><a href="../avis.php">Avis</a></li>
            <li><a href="../login/login.php">Connexion</a></li>
            <li><a href="../service.php">services</a></li>
        </ul>
    </nav>
</header>
<body>
<form method="post" action="" class="signup-form">
    <input type="text" name="first_name" placeholder="Prénom" required><br>
    <input type="text" name="last_name" placeholder="Nom" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Mot de passe" required><br>
    <select name="rank" required>
        <option value="1">Veterinaire</option>
        <option value="2">Employer</option>
    </select><br>
    <button type="submit">S'inscrire</button>
</form>
<a href="../rank/administrateur.php">
    <button class="return-btn">Revenir sur mon Panel</button>
</a>
</body>
<footer>
    <?php include '../php/footer.php'; ?>
</footer>
</html>
