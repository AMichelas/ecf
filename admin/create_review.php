<?php
session_start();
include '../login/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../error/off.php');
    exit();
}

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "ecf");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer la liste des animaux pour lier les comptes rendus
$query = "SELECT id, name FROM animals";
$result = $conn->query($query);

// Vérification si le formulaire est soumis pour créer un compte rendu
if (isset($_POST['animal_id']) && isset($_POST['review_date']) && isset($_POST['content'])) {
    $animal_id = $_POST['animal_id'];
    $review_date = $_POST['review_date'];
    $content = $_POST['content'];

    // Insérer le compte rendu dans la table vet_reviews
    $insert_sql = "INSERT INTO vet_reviews (animal_id, review_date, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iss", $animal_id, $review_date, $content);
    $stmt->execute();
    $stmt->close();

    echo "<p>Compte rendu créé avec succès.</p>";
}

// Fermer la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Compte Rendu</title>
    <link rel="stylesheet" href="review.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>
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

<h1>Créer un Compte Rendu pour un Animal</h1>

<!-- Formulaire de création de compte rendu -->
<form method="POST" action="">
    <label for="animal_id">Animal :</label>
    <select id="animal_id" name="animal_id" required>
        <option value="">Sélectionner un animal</option>
        <?php
        // Afficher tous les animaux dans une liste déroulante
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
        }
        ?>
    </select><br>

    <label for="review_date">Date du compte rendu :</label>
    <input type="datetime-local" id="review_date" name="review_date" required><br>

    <label for="content">Contenu du compte rendu :</label>
    <textarea id="content" name="content" required></textarea><br>

    <button type="submit">Créer le compte rendu</button>
</form>

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
