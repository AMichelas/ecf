<?php
// Récupérer l'ID de l'animal envoyé via AJAX
$animalId = $_POST['animalId'];

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'ecf');
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Mettre à jour le compteur de clics
$sql = "UPDATE animals SET click_count = click_count + 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $animalId);
$stmt->execute();
$stmt->close();

// Récupérer la nouvelle valeur du compteur
$sql = "SELECT click_count FROM animals WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $animalId);
$stmt->execute();
$stmt->bind_result($newClickCount);
$stmt->fetch();
$stmt->close();

// Retourner le nouveau compteur de clics
echo $newClickCount;
$conn->close();
?>
