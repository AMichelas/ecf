<?php
// Page générée automatiquement pour l'habitat marais

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'ecf');
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Récupérer les animaux pour cet habitat
$habitat_name = 'marais';
$sql = "SELECT animals.id, animals.name, animals.breed, animals.image_url, 
               animals.food_type, animals.food_amount, animals.feeding_time, 
               animals.vet_visit_date, animals.details, animals.click_count 
        FROM animals 
        JOIN habitats ON animals.habitat_id = habitats.id 
        WHERE habitats.name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $habitat_name);
$stmt->execute();
$result = $stmt->get_result();

// Construction de la liste des animaux
$animals_list = "";
if ($result->num_rows > 0) {
    while ($animal = $result->fetch_assoc()) {
        $imagePath = "../admin/" . $animal['image_url'];
        if (!empty($animal['image_url']) && file_exists($imagePath)) {
            $animals_list .= "
                <li>
                    <img src='$imagePath' alt='$animal[name]'>
                    <p>$animal[name] ($animal[breed])</p>
                    <button class='details-button' onclick='showDetails(" . json_encode($animal) . ")'>Voir détails</button>
                    <p>Nombre de clics : <span id='click-counter-{$animal['id']}'>{$animal['click_count']}</span></p>
                </li>";
        } else {
            $animals_list .= "
                <li>
                    <p>Aucune image disponible pour cet animal.</p>
                    <p>$animal[name] ($animal[breed])</p>
                    <button class='details-button' onclick='showDetails(" . json_encode($animal) . ")'>Voir détails</button>
                    <p>Nombre de clics : <span id='click-counter-{$animal['id']}'>{$animal['click_count']}</span></p>
                </li>";
        }
    }
} else {
    $animals_list = "<p>Aucun animal dans cet habitat.</p>";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $habitat_name; ?></title>
    <link rel="stylesheet" href="../css/createhabitat.css">
</head>
<body>
    <header>
        <h1><?php echo $habitat_name; ?></h1>
    </header>
    <main>
        <img src="../uploads/<?php echo $image_url; ?>" alt="<?php echo $habitat_name; ?>">
        <h2>Animaux dans cet habitat :</h2>
        <ul>
            <?php echo $animals_list; ?>
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