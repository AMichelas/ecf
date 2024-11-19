<?php
session_start();
include '../login/db.php';

// Vérifier si l'utilisateur est administrateur ou employé (rang 2 ou 3)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../error/off.php');
    exit();
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Vérifier si l'utilisateur a le rang 2 (employé) ou rang 3 (administrateur)
if ($user['rank'] != 2 && $user['rank'] != 3) {
    header('Location: ../error/norank.php');
    exit();
}

$sql = "SELECT * FROM horaires";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$horaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Création d'un service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $titre = $_POST['titre'];
    $description = $_POST['description'];

    $sql = "INSERT INTO services (titre, description) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titre, $description]);

    echo "<p class='success-message'>Service créé avec succès!</p>";
}

// Modification d'un service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['service_id'];
    $titre = $_POST['titre'];
    $description = $_POST['description'];

    $sql = "UPDATE services SET titre = ?, description = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titre, $description, $id]);

    echo "<p class='success-message'>Service modifié avec succès!</p>";
}

// Suppression d'un service
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql = "DELETE FROM services WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    echo "<p class='success-message'>Service supprimé avec succès!</p>";
}

// Récupérer les services pour afficher
$sql = "SELECT * FROM services";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Services du Zoo</title>
    <link rel="stylesheet" href="../admin/serviceedit.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/header.css">

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
<body>
    
    <main>
        <h1>Gestion des Services du Zoo</h1>

        <!-- Formulaire de création de service -->
        <h2>Créer un Service</h2>
        <form method="POST" action="">
            <input type="text" name="titre" placeholder="Titre du service" required><br><br>
            <textarea name="description" placeholder="Description du service" required></textarea><br><br>
            <button type="submit" name="create">Créer le Service</button>
        </form>

        <hr>

        <!-- Liste des services -->
        <h2>Services Existants</h2>
        <?php foreach ($services as $service): ?>
            <div>
                <h3><?php echo $service['titre']; ?></h3>
                <p><?php echo $service['description']; ?></p>

                <!-- Formulaire de modification -->
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                    <input type="text" name="titre" value="<?php echo $service['titre']; ?>" required><br><br>
                    <textarea name="description" required><?php echo $service['description']; ?></textarea><br><br>
                    <button type="submit" name="update">Modifier</button>
                </form>

                <!-- Lien de suppression -->
                <a href="?delete=<?php echo $service['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?');">
                    <button>Supprimer</button>
                </a>
            </div>
            <br>
        <?php endforeach; ?>
        <a href="javascript:window.history.back();">
    <button class="return-btn">Revenir sur mon Panel</button>
</a>
    </main>

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
</body>
</html>
