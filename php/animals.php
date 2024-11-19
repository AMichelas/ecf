<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'ecf');
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Requête pour récupérer tous les animaux
$sql = "SELECT name, image_url FROM animals";
$result = $conn->query($sql);

// Vérifie s'il y a des animaux dans la base de données
if ($result->num_rows > 0) {
    echo '<div class="animal-grid">';
    
    // Boucle pour afficher chaque animal
    while ($animal = $result->fetch_assoc()) {
        echo '<div class="animal-card">';
        
        // Utilisation du chemin de l'image
        $imagePath = !empty($animal['image_url']) ? 'admin/' . $animal['image_url'] : 'images/placeholder.png';
        
        echo '<img src="' . $imagePath . '" alt="' . $animal['name'] . '">';
        echo '<p>' . $animal['name'] . '</p>';
        echo '</div>';
    }

    echo '</div>';
} else {
    echo '<p>Aucun animal à afficher.</p>';
}

$conn->close();
?>
