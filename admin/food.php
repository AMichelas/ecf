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

if (!in_array($user['rank'], [2, 3])) {
    header('Location: ../error/norank.php');
    exit();
}

// Connexion à la base de données pour récupérer tous les animaux
$conn = new mysqli("localhost", "root", "", "ecf");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer la liste des animaux
$query = "SELECT id, name FROM animals";  // Sélectionner uniquement l'ID et le nom des animaux
$result = $conn->query($query);

// Vérification si le formulaire est soumis pour modifier un animal
if (isset($_POST['id']) && isset($_POST['food_type']) && isset($_POST['food_amount']) && isset($_POST['feeding_time'])) {
    $animal_id = $_POST['id'];
    $food_type = $_POST['food_type'];
    $food_amount = $_POST['food_amount'];
    $feeding_time = $_POST['feeding_time'];

    // Mettre à jour l'animal dans la base de données
    $update_sql = "UPDATE animals SET food_type = ?, food_amount = ?, feeding_time = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssss", $food_type, $food_amount, $feeding_time, $animal_id);
    $stmt->execute();
    $stmt->close();

    echo "<p>Animal modifié avec succès.</p>";
}

// Fermer la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Animal</title>
    <link rel="stylesheet" href="food.css">
</head>
<body>

<h1>Modifier un Animal</h1>

<!-- Formulaire de modification d'animal -->
<form method="POST" action="">
    <label for="id">ID de l'animal :</label>
    <select id="id" name="id" required>
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

    <label for="food_type">Type de nourriture :</label>
    <input type="text" id="food_type" name="food_type" required><br>

    <label for="food_amount">Quantité de nourriture :</label>
    <input type="number" id="food_amount" name="food_amount" required><br>

    <label for="feeding_time">Heure d'alimentation :</label>
    <input type="datetime-local" id="feeding_time" name="feeding_time" required><br>

    <button type="submit">Modifier l'animal</button>
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

    <h1>Gestion des Animaux</h1>

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
