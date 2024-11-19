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

if ($user['rank'] != 3) {
    header('Location: ../error/norank.php');  // Redirige si l'utilisateur n'a pas le bon rank
    exit();
}
?>

<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "ecf");

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fonction pour gérer l'upload de l'image
function handle_image_upload($image) {
    // Vérification si l'image a été téléchargée
    if ($image['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";  // Dossier où l'image sera enregistrée
        $target_file = $target_dir . basename($image["name"]);
        $image_url = $target_file;  // Chemin relatif de l'image

        // Vérification du type de fichier (optionnel)
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo "Seuls les fichiers JPG, JPEG, PNG, et GIF sont autorisés.";
            exit;
        }

        // Déplacer l'image vers le dossier de destination
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            return $image_url;
        } else {
            echo "Erreur lors du téléchargement de l'image.";
            exit;
        }
    }
    return null;  // Aucun fichier téléchargé
}

// Ajouter un animal
if (isset($_POST['add_animal'])) {
    // Récupération des données du formulaire
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $breed = mysqli_real_escape_string($conn, $_POST['breed']);
    $food_type = mysqli_real_escape_string($conn, $_POST['food_type']);
    $food_amount = $_POST['food_amount'];
    $feeding_time = $_POST['feeding_time'];
    $vet_visit_date = $_POST['vet_visit_date'];
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $habitat_id = $_POST['habitat_id']; // Utilisation de habitat_id

    // Gestion de l'image téléchargée
    $image_url = handle_image_upload($_FILES['image']);

    // Préparation de la requête SQL pour l'insertion
    $query = "INSERT INTO animals (name, breed, food_type, food_amount, feeding_time, vet_visit_date, details, habitat_id, image_url) 
              VALUES ('$name', '$breed', '$food_type', '$food_amount', '$feeding_time', '$vet_visit_date', '$details', '$habitat_id', '$image_url')";

    // Exécution de la requête
    if ($conn->query($query) === TRUE) {
        echo "Animal ajouté avec succès !";
    } else {
        echo "Erreur : " . $conn->error;
    }
}

// Modifier un animal
if (isset($_POST['edit_animal'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $breed = mysqli_real_escape_string($conn, $_POST['breed']);
    $food_type = mysqli_real_escape_string($conn, $_POST['food_type']);
    $food_amount = $_POST['food_amount'];
    $feeding_time = $_POST['feeding_time'];
    $vet_visit_date = $_POST['vet_visit_date'];
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $habitat_id = $_POST['habitat_id']; // Utilisation de habitat_id

    // Gestion de l'image téléchargée (si nouvelle image)
    $image_url = isset($_FILES['image']) ? handle_image_upload($_FILES['image']) : $_POST['existing_image'];

    // Requête SQL pour modifier un animal
    $query = "UPDATE animals 
              SET name='$name', breed='$breed', food_type='$food_type', food_amount='$food_amount', feeding_time='$feeding_time', vet_visit_date='$vet_visit_date', details='$details', habitat_id='$habitat_id', image_url='$image_url' 
              WHERE id='$id'";

    // Exécution de la requête
    if ($conn->query($query) === TRUE) {
        echo "Animal modifié avec succès !";
    } else {
        echo "Erreur : " . $conn->error;
    }
}

// Supprimer un animal
if (isset($_POST['delete_animal'])) {
    $id = $_POST['id'];

    // Requête SQL pour supprimer un animal
    $query = "DELETE FROM animals WHERE id='$id'";

    // Exécution de la requête
    if ($conn->query($query) === TRUE) {
        echo "Animal supprimé avec succès !";
    } else {
        echo "Erreur : " . $conn->error;
    }
}

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Animaux</title>
    <header class="menu">
    <div class="logo">
        <a href="index.php">Arcadia</a>
    </div>
    <nav>
        <ul>
            <li><a href="./habitat.php">Habitat</a></li>
            <li><a href="contacts.php">Contacts</a></li>
            <li><a href="avis.php   ">Avis</a></li>
            <li><a href="login/login.php">Connexion</a></li>
            <li><a href="service.php">services</a></li>
        </ul>
    </nav>
</header>
</head>
<body>
    <h1>Gestion des Animaux</h1>

    <!-- Formulaire d'ajout d'animal -->
    <h3>Ajouter un animal</h3>
        
    <?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "ecf");

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les habitats depuis la base de données
$habitats_query = "SELECT id, name FROM habitats"; // Adapté à la table des habitats
$habitats_result = $conn->query($habitats_query);

if ($habitats_result->num_rows > 0) {
    $habitats = $habitats_result->fetch_all(MYSQLI_ASSOC);
} else {
    $habitats = [];
}

$conn->close();
?>

<form method="POST" action="" enctype="multipart/form-data">
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

        <label for="image">Télécharger une image :</label>
        <input type="file" id="image" name="image" accept="image/*"><br>

        <label for="habitat_id">Habitat :</label>
        <select id="habitat_id" name="habitat_id" required>
            <option value="">-- Choisir un habitat --</option>
            <?php
            // Affichage des habitats disponibles
            foreach ($habitats as $habitat) {
                echo "<option value='" . $habitat['id'] . "'>" . $habitat['name'] . "</option>";
            }
            ?>
        </select><br>

        <button type="submit" name="add_animal">Ajouter l'animal</button>
    </form>

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

    <!-- Formulaire de suppression d'animal -->
    <h3>Supprimer un animal</h3>
    <form method="POST" action="">
        <label for="id">ID de l'animal :</label>
        <input type="number" id="id" name="id" required><br>
        <button type="submit" name="delete_animal">Supprimer l'animal</button>
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

<body>
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
