<?php
require_once 'config.php';

// Sécurité : Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_sport = $_POST['sport'];
    $update = $pdo->prepare("UPDATE utulisateurs SET sport_prefere = :sport WHERE id = :id");
    if ($update->execute(['sport' => $nouveau_sport, 'id' => $user_id])) {
        $message = "Profil mis à jour avec succès !";
    }
}

// Récupération des infos actuelles de l'utilisateur
$query = $pdo->prepare("SELECT nom, prenom, role, sport_prefere FROM utulisateurs WHERE id = :id");
$query->execute(['id' => $user_id]);
$user = $query->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - Pro-Arena</title>
</head>
<body>
    <a href="dashboard.php">Retour au Dashboard</a>
    <h1>Mon Profil</h1>

    <?php if ($message): ?>
        <p style="color: green;"><?= $message ?></p>
    <?php endif; ?>

    <div style="border: 1px solid #ccc; padding: 20px; width: 300px;">
        <img src="https://via.placeholder.com/150" alt="Photo de profil" style="border-radius: 50%;">
        
        <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
        <p><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></p>
        <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?></p>
        
        <hr>
        
        <form method="POST">
            <label for="sport"><strong>Mon Sport :</strong></label><br><br>
            <select name="sport" id="sport">
                <option value="">-- Choisir un sport --</option>
                <option value="Judo" <?= $user['sport_prefere'] == 'Judo' ? 'selected' : '' ?>>Judo</option>
                <option value="Karate" <?= $user['sport_prefere'] == 'Karate' ? 'selected' : '' ?>>Karaté</option>
                <option value="Echec" <?= $user['sport_prefere'] == 'Echec' ? 'selected' : '' ?>>Échec</option>
            </select>
            <br><br>
            <button type="submit">Enregistrer les modifications</button>
        </form>
    </div>
</body>
</html>