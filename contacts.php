<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Contact</title>
    <link rel="stylesheet" href="css/formcontact.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <?php include 'php/header.php'; ?>
    <div class="contact-container">
        <h1>Contactez-nous</h1>
        <p>Nous sommes à votre écoute ! N'hésitez pas à nous contacter pour toute question, suggestion ou demande particulière.</p>
        <form
  action="https://formspree.io/f/xldelrer"
  method="POST"
>
  <label>
    Votre email:
    <input type="email" name="email">
  </label>
  <label>
    Votre message:
    <textarea name="message"></textarea>
  </label>
  <!-- your other form fields go here -->
  <button type="submit">Envoyez</button>
</form>
    </div>
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
