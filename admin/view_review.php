<?php
session_start();
include '../login/db.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: ../error/off.php');
    exit();
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();


if ($user['rank'] != 1 && $user['rank'] != 3) {
    header('Location: ../error/norank.php');
    exit();
}

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

// Filtrer les comptes rendus par animal ou par date
$filter_animal = isset($_GET['animal_id']) ? $_GET['animal_id'] : '';
$filter_date = isset($_GET['review_date']) ? $_GET['review_date'] : '';

// Préparer la requête avec les filtres
$query = "SELECT vet_reviews.*, animals.name AS animal_name FROM vet_reviews 
          JOIN animals ON vet_reviews.animal_id = animals.id WHERE 1=1";

if ($filter_animal != '') {
    $query .= " AND vet_reviews.animal_id = " . intval($filter_animal);
}

if ($filter_date != '') {
    $query .= " AND DATE(vet_reviews.review_date) = '" . $conn->real_escape_string($filter_date) . "'";
}

$result = $conn->query($query);

// Récupérer la liste des animaux pour le filtrage
$animals_query = "SELECT id, name FROM animals";
$animals_result = $conn->query($animals_query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comptes Rendus des Animaux</title>
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

<h1>Comptes Rendus des Animaux</h1>

<!-- Formulaire de filtre -->
<form method="GET" action="">
    <label for="animal_id">Filtrer par Animal :</label>
    <select id="animal_id" name="animal_id">
        <option value="">Tous les animaux</option>
        <?php
        // Afficher les animaux dans une liste déroulante pour filtrer
        if ($animals_result->num_rows > 0) {
            while ($row = $animals_result->fetch_assoc()) {
                $selected = ($filter_animal == $row['id']) ? 'selected' : '';
                echo "<option value='" . $row['id'] . "' $selected>" . $row['name'] . "</option>";
            }
        }
        ?>
    </select>

    <label for="review_date">Filtrer par Date :</label>
    <input type="date" id="review_date" name="review_date" value="<?php echo $filter_date; ?>">

    <button type="submit">Filtrer</button>
</form>

<!-- Tableau d'affichage des comptes rendus -->
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Animal</th>
            <th>Date du Compte Rendu</th>
            <th>Contenu</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Vérifier s'il y a des résultats
        if ($result->num_rows > 0) {
            // Afficher chaque compte rendu dans un tableau
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . $row['animal_name'] . "</td>
                        <td>" . $row['review_date'] . "</td>
                        <td>" . $row['content'] . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Aucun compte rendu trouvé</td></tr>";
        }
        ?>
    </tbody>
</table>

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

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
