<?php
session_start();
include '../login/db.php';

// Vérifier si l'utilisateur est connecté et si le rank est correct
if (!isset($_SESSION['user_id'])) {
    header('Location: ../error/off.php');  // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['rank'] != 1) {
    header('Location: ../error/norank.php');  // Redirige si l'utilisateur n'a pas le bon rank
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Employer</title>
    <link rel="stylesheet" href="../admin/panel.css">
</head>
<body>
    <div class="panel-container">
        <header>
            <h1>Panel veto</h1>
        </header>
        <div class="admin-content">
            <p>Bienvenue, <?php echo $user['first_name'] . " " . $user['last_name']; ?> !</p>
            <p>Espace d'employer. Choisissez une action :</p>
            <div class="buttons-container">
                <a href="../login/logout.php">
                    <button class="action-off">Déconnexion</button>
                </a>
                <a href="../admin/statistique.php">
                    <button class="action-btn">Occupation par animal</button>
                </a>
                </a>                <a href="../admin/view_review.php">
                    <button class="action-btn">create Animal</button>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
