<?php
session_start();
include '../login/db.php';

// Vérification de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: ../error/off.php');
    exit();
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Vérification du rang de l'utilisateur
if ($user['rank'] != 2 && $user['rank'] != 3) {
    header('Location: ../error/norank.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'ecf');

// Vérification de la connexion à la base de données
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

function createHabitatPage($name, $page_url, $image_url) {
    global $conn;  // Utilise la connexion à la base de données

    // Récupération des animaux associés à l'habitat
    $animals_sql = "SELECT * FROM animals WHERE habitat_id = (SELECT id FROM habitats WHERE page_url = ?)";
    $stmt = $conn->prepare($animals_sql);
    $stmt->bind_param("s", $page_url);
    $stmt->execute();
    $animals_result = $stmt->get_result();

    // Vérification si des animaux existent pour cet habitat
    if ($animals_result->num_rows > 0) {
        $animals_list = '';
        while ($animal = $animals_result->fetch_assoc()) {
            $animals_list .= "
                <li>
                    <img src='../uploads/{$animal['image_url']}' alt='{$animal['name']}'>
                    <p>{$animal['name']} ({$animal['breed']})</p>
                    <button class='details-button' onclick='showDetails(" . json_encode($animal) . ")'>Voir détails</button>
                    <p>Nombre de clics : <span id='click-counter-{$animal['id']}'>{$animal['click_count']}</span></p>
                </li>";
        }
    } else {
        $animals_list = "<p>Aucun animal dans cet habitat.</p>";
    }

    // Construction du contenu HTML de la page de l'habitat
    $habitatPageContent = <<<HTML
<?php
// Page générée automatiquement pour l'habitat $name

// Connexion à la base de données
\$conn = new mysqli('localhost', 'root', '', 'ecf');
if (\$conn->connect_error) {
    die("Connexion échouée : " . \$conn->connect_error);
}

// Récupérer les animaux pour cet habitat
\$habitat_name = '$name';
\$sql = "SELECT animals.id, animals.name, animals.breed, animals.image_url, 
               animals.food_type, animals.food_amount, animals.feeding_time, 
               animals.vet_visit_date, animals.details, animals.click_count 
        FROM animals 
        JOIN habitats ON animals.habitat_id = habitats.id 
        WHERE habitats.name = ?";
\$stmt = \$conn->prepare(\$sql);
\$stmt->bind_param("s", \$habitat_name);
\$stmt->execute();
\$result = \$stmt->get_result();

// Construction de la liste des animaux
\$animals_list = "";
if (\$result->num_rows > 0) {
    while (\$animal = \$result->fetch_assoc()) {
        \$imagePath = "../admin/" . \$animal['image_url'];
        if (!empty(\$animal['image_url']) && file_exists(\$imagePath)) {
            \$animals_list .= "
                <li>
                    <img src='\$imagePath' alt='\$animal[name]'>
                    <p>\$animal[name] (\$animal[breed])</p>
                    <button class='details-button' onclick='showDetails(" . json_encode(\$animal) . ")'>Voir détails</button>
                    <p>Nombre de clics : <span id='click-counter-{\$animal['id']}'>{\$animal['click_count']}</span></p>
                </li>";
        } else {
            \$animals_list .= "
                <li>
                    <p>Aucune image disponible pour cet animal.</p>
                    <p>\$animal[name] (\$animal[breed])</p>
                    <button class='details-button' onclick='showDetails(" . json_encode(\$animal) . ")'>Voir détails</button>
                    <p>Nombre de clics : <span id='click-counter-{\$animal['id']}'>{\$animal['click_count']}</span></p>
                </li>";
        }
    }
} else {
    \$animals_list = "<p>Aucun animal dans cet habitat.</p>";
}

\$stmt->close();
\$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo \$habitat_name; ?></title>
    <link rel="stylesheet" href="../css/createhabitat.css">
</head>
<body>
    <header>
        <h1><?php echo \$habitat_name; ?></h1>
    </header>
    <main>
        <img src="../uploads/<?php echo \$image_url; ?>" alt="<?php echo \$habitat_name; ?>">
        <h2>Animaux dans cet habitat :</h2>
        <ul>
            <?php echo \$animals_list; ?>
        </ul>
    </main>

    <!-- Popup pour afficher les détails de l'animal -->
    <div id="animal-popup" style="display: none;">
        <div id="popup-content">
            <h3 id="animal-name"></h3>
            <p id="animal-breed"></p>
            <p id="animal-food-type"></p>
            <p id="animal-food-amount"></p>
            <p id="animal-feeding-time"></p>
            <p id="animal-vet-visit-date"></p>
            <p id="animal-details"></p>
            <button onclick="closePopup()">Fermer</button>
        </div>
    </div>

    <script>
        // Afficher les détails de l'animal dans une popup
        function showDetails(animal) {
            // Remplir les informations dans la popup
            document.getElementById('animal-name').innerText = animal.name;
            document.getElementById('animal-breed').innerText = "Race: " + animal.breed;
            document.getElementById('animal-food-type').innerText = "Type de nourriture: " + (animal.food_type || "Non défini");
            document.getElementById('animal-food-amount').innerText = "Quantité de nourriture: " + (animal.food_amount || "Non défini");
            document.getElementById('animal-feeding-time').innerText = "Horaire de nourrissage: " + (animal.feeding_time || "Non défini");
            document.getElementById('animal-vet-visit-date').innerText = "Date de visite vétérinaire: " + (animal.vet_visit_date || "Non défini");
            document.getElementById('animal-details').innerText = "Détails supplémentaires: " + (animal.details || "Non défini");

            // Afficher la popup
            document.getElementById('animal-popup').style.display = 'flex';

            // Incrémenter le compteur de clics et sauvegarder en base de données
            updateClickCount(animal.id);
        }

        // Fonction pour envoyer la requête AJAX et mettre à jour le compteur de clics
        function updateClickCount(animalId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_click_count.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Mettre à jour l'affichage du compteur de clics
                    const counter = document.getElementById('click-counter-' + animalId);
                    counter.innerText = xhr.responseText;
                }
            };
            xhr.send("animalId=" + animalId);
        }

        // Fermer la popup
        function closePopup() {
            document.getElementById('animal-popup').style.display = 'none';
        }
    </script>
</body>
</html>
HTML;

    // Enregistrement du fichier PHP pour l'habitat
    $fileName = "../habitats/$page_url.php";
    file_put_contents($fileName, $habitatPageContent);
}

// Fonction pour gérer l'upload d'images
function uploadImage($file) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $uploadOk = 1;

    // Vérification si le fichier est une image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        echo "Le fichier n'est pas une image.";
        $uploadOk = 0;
    }

    // Vérification de la taille du fichier
    if ($file["size"] > 5000000) {
        echo "Désolé, le fichier est trop volumineux.";
        $uploadOk = 0;
    }

    // Vérification du format de l'image
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "Désolé, seuls les formats JPG, JPEG, PNG & GIF sont autorisés.";
        $uploadOk = 0;
    }

    // Téléchargement du fichier si tout est valide
    if ($uploadOk == 0) {
        echo "Désolé, le fichier n'a pas été téléchargé.";
        return false;
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return basename($file["name"]);
        } else {
            echo "Désolé, une erreur est survenue lors du téléchargement de votre fichier.";
            return false;
        }
    }
}

// Ajout d'un nouvel habitat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = $_POST['new_name'];
    $page_url = $_POST['new_page_url'];
    $image_url = '';

    // Gestion de l'upload de l'image
    if (!empty($_FILES['new_image']['name'])) {
        $uploadedImage = uploadImage($_FILES['new_image']);
        if ($uploadedImage) {
            $image_url = $uploadedImage;
        }
    }

    // Insertion de l'habitat dans la base de données
    $insert_sql = "INSERT INTO habitats (name, image_url, page_url) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sss", $name, $image_url, $page_url);
    $stmt->execute();
    $stmt->close();

    // Générer la page pour l'habitat
    createHabitatPage($name, $page_url, $image_url);
}

// Modification d'un habitat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $page_url = $_POST['page_url'];
    $image_url = $_POST['image_url'];

    // Gestion de l'upload de l'image
    if (!empty($_FILES['update_image']['name'])) {
        $uploadedImage = uploadImage($_FILES['update_image']);
        if ($uploadedImage) {
            $image_url = $uploadedImage;
        }
    }

    // Mise à jour de l'habitat
    $update_sql = "UPDATE habitats SET name = ?, image_url = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $name, $image_url, $id);
    $stmt->execute();
    $stmt->close();

    // Mise à jour de la page PHP de l'habitat
    $fileName = "../habitats/$page_url.php";
    $updatedPageContent = <<<HTML
<?php
// Page mise à jour pour l'habitat $name
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$name</title>
    <link rel="stylesheet" href="../css/createhabitat.css">
</head>
<body>
    <header>
        <h1>$name</h1>
    </header>
    <main>
        <img src="../admin/uploads/$image_url" alt="$name">
        <p>Création new ici.</p>
    </main>
</body>
</html>
HTML;

    // Mettre à jour le fichier PHP existant
    file_put_contents($fileName, $updatedPageContent);
}

// Suppression d'un habitat
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Récupération de l'URL de l'image et de la page avant la suppression
    $delete_sql = "SELECT image_url, page_url FROM habitats WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($image_url, $page_url);
    $stmt->fetch();

    // Suppression de l'image du dossier
    if ($image_url && file_exists("../uploads/" . $image_url)) {
        unlink("../uploads/" . $image_url);
    }

    // Suppression de la page PHP de l'habitat
    if (file_exists("../habitats/$page_url.php")) {
        unlink("../habitats/$page_url.php");
    }

    // Suppression de l'habitat de la base de données
    $delete_sql = "DELETE FROM habitats WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: habitatpanel.php");
    exit();
}

// Affichage des habitats existants
$sql = "SELECT * FROM habitats";
$result = $conn->query($sql);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des habitats</title>
    <link rel="stylesheet" href="../css/panelhabitat.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
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

<h2 class="admin-title">Panneau d'administration - Gérer les Habitats</h2>

<h3 class="section-title">Ajouter un nouvel habitat</h3>
<form class="admin-form" method="POST" action="habitatpanel.php" enctype="multipart/form-data">
    <label for="new_name">Nom :</label>
    <input type="text" name="new_name" id="new_name" class="input-field" required>
    
    <label for="new_image">Image :</label>
    <input type="file" name="new_image" id="new_image" class="input-field" accept="image/*">
    
    <label for="new_page_url">URL de la page :</label>
    <input type="text" name="new_page_url" id="new_page_url" class="input-field" required>
    
    <button type="submit" name="add" class="submit-button">Ajouter</button>
</form>

<h3 class="section-title">Modifier les habitats existants</h3>
<form class="admin-form" method="POST" action="habitatpanel.php" enctype="multipart/form-data">
    <label for="id">ID de l'habitat à modifier :</label>
    <input type="number" name="id" id="id" class="input-field" required>

    <label for="name">Nom :</label>
    <input type="text" name="name" id="name" class="input-field" required>
    
    <label for="update_image">Image :</label>
    <input type="file" name="update_image" id="update_image" class="input-field" accept="image/*">
    
    <label for="page_url">URL de la page :</label>
    <input type="text" name="page_url" id="page_url" class="input-field" required>
    
    <button type="submit" name="update" class="submit-button">Mettre à jour</button>
</form>

<h3 class="section-title">Supprimer un habitat</h3>
<ul class="habitat-list">
    <?php while ($row = $result->fetch_assoc()): ?>
        <li class="habitat-item">
            <?= $row['name'] ?> 
            <a href="habitatpanel.php?delete=<?= $row['id'] ?>" class="delete-link">Supprimer</a>
        </li>
    <?php endwhile; ?>
</ul>
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
