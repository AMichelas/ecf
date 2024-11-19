<?php
include '../login/db.php';
include '../php/verif.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mettre à jour les horaires
    foreach ($_POST['ouverture'] as $key => $value) {
        $jour = $_POST['jour'][$key];
        $ouverture = $_POST['ouverture'][$key];
        $fermeture = $_POST['fermeture'][$key];

        // Requête pour mettre à jour l'horaire
        $sql = "UPDATE horaires SET ouverture = ?, fermeture = ? WHERE jour = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$ouverture, $fermeture, $jour]);
    }

    echo "Horaires mis à jour !";
}

// Récupérer les horaires
$sql = "SELECT * FROM horaires";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$horaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier les horaires</title>
    <link rel="stylesheet" href="../admin/timeedit.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<header class="menu">
    <div class="logo">
        <a href="index.php">Arcadia</a>
    </div>
    <nav>
        <ul>
            <li><a href="../habitat.php">Habitat</a></li>
            <li><a href="../contacts.php">Contacts</a></li>
            <li><a href="../avis.php   ">Avis</a></li>
            <li><a href="../login/login.php">Connexion</a></li>
            <li><a href="../service.php">services</a></li>
        </ul>
    </nav>
</header>
<body>
    <h1>Modifier les horaires</h1>
    <form method="post" action="">
        <?php foreach ($horaires as $horaire): ?>
            <div>
                <label for="jour_<?php echo $horaire['jour']; ?>"><?php echo $horaire['jour']; ?> :</label><br>
                <input type="time" name="ouverture[]" value="<?php echo date('H:i', strtotime($horaire['ouverture'])); ?>" required>
                <input type="time" name="fermeture[]" value="<?php echo date('H:i', strtotime($horaire['fermeture'])); ?>" required>
                <input type="hidden" name="jour[]" value="<?php echo $horaire['jour']; ?>">
            </div><br>
        <?php endforeach; ?>
        <button type="submit">Sauvegarder les modifications</button>
    </form>
<a href="../rank/administrateur.php">
    <button class="return-btn">Revenir sur mon Panel</button>
</a>
</body>
<footer>
<?php
include '../login/db.php';

$sql = "SELECT * FROM horaires";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$horaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<footer class="footer">
    <div class="footer-content">
        <h1>Nos horaires :</h1>
        <div class="horaires-container">
            <?php foreach ($horaires as $horaire): ?>
                <div class="jour">
                    <p><?php echo $horaire['jour']; ?></p>
                    <p><?php echo date('H:i', strtotime($horaire['ouverture'])) . ' - ' . date('H:i', strtotime($horaire['fermeture'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</footer>
</html>
