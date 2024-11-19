<?php
session_start();
include 'login/db.php';

// Vérifier si l'utilisateur est connecté
$isConnected = isset($_SESSION['user_id']);

// Récupérer les informations de l'utilisateur s'il est connecté
$isEmployee = false;
if ($isConnected) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Vérifier si l'utilisateur est employé (rang 2)
    $isEmployee = $user['rank'] == 2;
}

// Traitement de l'ajout d'un avis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_avis'])) {
    $pseudo = htmlspecialchars($_POST['pseudo']);
    $avis = htmlspecialchars($_POST['avis']);
    $status = 0; // L'avis est en attente de validation par défaut

    $sql = "INSERT INTO avis (pseudo, avis, status) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pseudo, $avis, $status]);

    echo "<p class='success-message'>Votre avis a été soumis et est en attente de validation.</p>";
}

// Traitement de l'approbation ou suppression d'un avis par un employé
if ($isEmployee && isset($_GET['action']) && isset($_GET['avis_id'])) {
    $avisId = intval($_GET['avis_id']);

    if ($_GET['action'] === 'approve') {
        // Approuver l'avis
        $sql = "UPDATE avis SET status = 1 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$avisId]);
    } elseif ($_GET['action'] === 'delete') {
        // Supprimer l'avis
        $sql = "DELETE FROM avis WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$avisId]);
    }
}

// Récupérer tous les avis approuvés (visibles par tout le monde)
$sql = "SELECT * FROM avis WHERE status = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$avisList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les avis en attente si l'utilisateur est employé
$pendingAvisList = [];
if ($isEmployee) {
    $sql = "SELECT * FROM avis WHERE status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $pendingAvisList = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Avis</title>
    <link rel="stylesheet" href="css/avispage.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<?php
include 'php/header.php';
?>
<body>
    <h1>Gestion des Avis</h1>

    <!-- Formulaire d'ajout d'un avis -->
    <h2>Ajouter un Avis</h2>
    <form method="POST" action="">
        <input type="text" name="pseudo" placeholder="Votre Pseudo" required><br><br>
        <textarea name="avis" placeholder="Votre avis" required></textarea><br><br>
        <button type="submit" name="submit_avis">Soumettre l'Avis</button>
    </form>

    <hr>

    <!-- Affichage des avis approuvés -->
    <h2>Avis Approuvés</h2>
    <?php foreach ($avisList as $avis): ?>
        <div class="avis">
            <h3><?php echo htmlspecialchars($avis['pseudo']); ?></h3>
            <p><?php echo htmlspecialchars($avis['avis']); ?></p>
        </div>
    <?php endforeach; ?>

    <?php if ($isEmployee): ?>
        <!-- Section pour les employés : Avis en attente de validation -->
        <hr>
        <h2>Avis en Attente de Validation</h2>
        <?php foreach ($pendingAvisList as $pendingAvis): ?>
            <div class="avis-pending">
                <h3><?php echo htmlspecialchars($pendingAvis['pseudo']); ?></h3>
                <p><?php echo htmlspecialchars($pendingAvis['avis']); ?></p>
                <a href="?action=approve&avis_id=<?php echo $pendingAvis['id']; ?>">Approuver</a> |
                <a href="?action=delete&avis_id=<?php echo $pendingAvis['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?');">Supprimer</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
<?php
include 'login/db.php';

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
