<?php
session_start();
include 'login/db.php';  // Inclure la connexion à la base de données

// Récupérer les avis vérifiés (status = 1)
$sql = "SELECT * FROM avis WHERE status = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$avisList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/habitat.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/avis.css">
    <link rel="stylesheet" href="css/animals.css">

</head>
    <header>
        <?php include 'php/header.php'; ?>
    </header>
<body>
<section style="background-image: url('img/background.jpg'); background-size: cover; background-position: center; height: 400px;">
</section>
<section class="animal-section">
    <h2>Nos Animaux</h2>
    <?php include 'php/animals.php'; ?>
</section>
<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'ecf');

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Récupération des habitats depuis la base de données
$sql = "SELECT * FROM habitats";
$result = $conn->query($sql);
?>

<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'ecf');

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Récupération des habitats depuis la base de données
$sql = "SELECT * FROM habitats";
$result = $conn->query($sql);
?>

<section class="habitat-selection">
    <h2 class="habitat-title">Choisissez un habitat :</h2>
    <ul class="habitat-list">
        <?php
        // Vérifier si des habitats sont présents dans la base de données
        if ($result->num_rows > 0) {
            // Parcours des résultats et affichage de chaque habitat
            while ($habitat = $result->fetch_assoc()) {
                $page_url = $habitat['page_url']; // URL de la page de l'habitat
                $name = $habitat['name']; // Nom de l'habitat
                $image_url = $habitat['image_url']; // URL de l'image de l'habitat
                // Ajouter un bouton avec une image
                echo "<li class='habitat-item'>
                        <a href='habitats/{$page_url}.php?page_url={$page_url}'>
                            <button class='habitat-button'>
                                <img src='admin/uploads/{$image_url}' alt='Image de l'habitat {$name}' class='habitat-image'>
                            </button>
                        </a>
                        <p class='habitat-name'>{$name}</p> <!-- Affichage du nom sous l'image -->
                      </li>";
            }
        } else {
            echo "<p>Aucun habitat trouvé.</p>";
        }
        ?>
    </ul>
</section>

<?php
$conn->close();
?>
<section class="avis">
<h2>Avis Vérifiés</h2>
    <?php if (!empty($avisList)): ?>
        <div class="avis-container">
            <div class="avis-wrapper">
                <?php foreach ($avisList as $avis): ?>
                    <div class="avis">
                        <h3><?php echo htmlspecialchars($avis['pseudo']); ?></h3>
                        <p><?php echo htmlspecialchars($avis['avis']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <p>Aucun avis vérifié pour le moment.</p>
    <?php endif; ?>
</section>
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