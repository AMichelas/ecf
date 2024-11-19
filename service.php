<?php
include 'login/db.php';

// Récupérer tous les services
$sql = "SELECT * FROM services";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services du zoo</title>
    <link rel="stylesheet" href="css/service.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<header>
    <?php include 'php/header.php'; ?>
</header>
<body>
    
    <h1>Services du zoo</h1>

    <?php foreach ($services as $service): ?>
        <div class="service-container">
            <h3><?php echo $service['titre']; ?></h3>
            <p><?php echo $service['description']; ?></p>
        </div>
        <br>
    <?php endforeach; ?>
</body>
<?php
include 'login/db.php';

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
