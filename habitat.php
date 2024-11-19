<?php
// Connexion à la base de données
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ecf';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Requête pour récupérer les habitats
$habitats_sql = "SELECT * FROM habitats";
$result = $conn->query($habitats_sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Habitats</title>
    <link rel="stylesheet" href="css/newhabitat.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
<header class="menu">
    <div class="logo">
        <a href="index.php">Arcadia</a>
    </div>
    <nav>
        <ul>
            <li><a href="habitat.php">Habitat</a></li>
            <li><a href="contacts.php">Contacts</a></li>
            <li><a href="avis.php   ">Avis</a></li>
            <li><a href="login/login.php">Connexion</a></li>
            <li><a href="service.php">services</a></li>
        </ul>
    </nav>
</header>


    <main>
        <header class="page-header"> <!-- Un autre header pour le titre de la page, qui sera stylisé différemment -->
            <h1>Liste des Habitats</h1>
        </header>
        <section>
            <h2>Choisissez un habitat :</h2>
            <ul>
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
                                        <img src='admin/uploads/{$image_url}' alt='Image de l'habitat {$name}'>
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
    </main>
</body>
<footer>
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
</footer>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
