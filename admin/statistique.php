<?php
session_start();
include '../login/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../error/off.php');  // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!in_array($user['rank'], [1, 3])) {
    header('Location: ../error/norank.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Animaux</title>
    <link rel="stylesheet" href="../admin/addanimal.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/header.css">

</head>
<h1>Situation Animal</h1>
<!-- Formulaire de modification d'animal -->
    <h3>Modifier un animal</h3>
    <form method="POST" action="">
        <label for="id">ID de l'animal :</label>
        <input type="number" id="id" name="id" required><br>
        
        <label for="name">Nom :</label>
        <input type="text" id="name" name="name" required><br>
        
        <label for="breed">Race :</label>
        <input type="text" id="breed" name="breed" required><br>
        
        <label for="food_type">Type de nourriture :</label>
        <input type="text" id="food_type" name="food_type" required><br>
        
        <label for="food_amount">Quantité de nourriture :</label>
        <input type="number" id="food_amount" name="food_amount" required><br>
        
        <label for="feeding_time">Heure d'alimentation :</label>
        <input type="datetime-local" id="feeding_time" name="feeding_time" required><br>
        
        <label for="vet_visit_date">Date de visite vétérinaire :</label>
        <input type="datetime-local" id="vet_visit_date" name="vet_visit_date" required><br>
        
        <label for="details">Détails (facultatif) :</label>
        <textarea id="details" name="details"></textarea><br>
        
        <label for="habitat_id">Habitat :</label>
        <select id="habitat_id" name="habitat_id" required>
            <?php
                // Récupérer les habitats disponibles depuis la base de données
                $conn = new mysqli("localhost", "root", "", "ecf");
                $result = $conn->query("SELECT id, name FROM habitats");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                }
                $conn->close();
            ?>
        </select><br>
        
        <label for="image">Télécharger une image :</label>
        <input type="file" id="image" name="image" accept="image/*"><br>
        
        <button type="submit" name="edit_animal">Modifier l'animal</button>
    </form>

    <?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "ecf");

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer tous les animaux
$query = "SELECT * FROM animals";
$result = $conn->query($query);
?>

<body>


    <!-- Affichage de tous les animaux -->
    <h3>Tous les animaux</h3>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Race</th>
                <th>Type de nourriture</th>
                <th>Quantité de nourriture</th>
                <th>Heure d'alimentation</th>
                <th>Date de visite vétérinaire</th>
                <th>Détails</th>
                <th>Habitat</th>
                <th>Vues</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Vérifier s'il y a des résultats
            if ($result->num_rows > 0) {
                // Afficher chaque animal dans un tableau
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row['id'] . "</td>
                            <td>" . $row['name'] . "</td>
                            <td>" . $row['breed'] . "</td>
                            <td>" . $row['food_type'] . "</td>
                            <td>" . $row['food_amount'] . "</td>
                            <td>" . $row['feeding_time'] . "</td>
                            <td>" . $row['vet_visit_date'] . "</td>
                            <td>" . $row['details'] . "</td>
                            <td>" . $row['habitat_id'] . "</td>
                            <td>" . $row['click_count'] . "</td>
                            <td><img src='" . $row['image_url'] . "' alt='Image de l'animal' width='100'></td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='11'>Aucun animal trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>

<?php
// Fermer la connexion
$conn->close();
?>

</body>
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