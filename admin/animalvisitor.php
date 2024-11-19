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
    <title>Animaux à découvrir</title>
    <link rel="stylesheet" href="animalvisitor.css">
</head>
<body>

<div class="container">
    <h1>Les Animaux à Découvrir</h1>

    <!-- Affichage des animaux -->
    <div class="animal-card">
        <?php
        // Vérifier s'il y a des résultats
        if ($result->num_rows > 0) {
            // Afficher chaque animal dans une carte
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card'>
                        <img src='" . $row['image_url'] . "' alt='Image de l'animal'>
                        <h3>" . $row['name'] . "</h3>
                        <p><strong>Race :</strong> " . $row['breed'] . "</p>
                        <p><strong>Type de nourriture :</strong> " . $row['food_type'] . "</p>
                        <p><strong>Quantité de nourriture :</strong> " . $row['food_amount'] . "g</p>
                        <p><strong>Heure d'alimentation :</strong> " . $row['feeding_time'] . "</p>
                        <p><strong>Visite vétérinaire :</strong> " . $row['vet_visit_date'] . "</p>
                        <p class='details'><strong>Détails :</strong> " . $row['details'] . "</p>
                      </div>";
            }
        } else {
            echo "<p>Aucun animal trouvé.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>

<?php
// Fermer la connexion
$conn->close();
?>
