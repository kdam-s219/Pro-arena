<?php
require_once 'config.php'; // Inclure le fichier de configuration pour la connexion à la base de données

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'club') {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_debut = $_POST['date_debut'];
    $lieu = $_POST['lieu'];
    $club_id = $_SESSION['user_id'];

  try {
        $sql = "INSERT INTO tournois (titre, description, date_debut, lieu, club_id) 
                VALUES (:titre, :description, :date_debut, :lieu, :club_id)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titre' => $titre,
            'description' => $description,
            'date_debut' => $date_debut,
            'lieu' => $lieu,
            'club_id' => $club_id
        ]);
        $message = "✅ Tournoi créé avec succès !";
    }
    catch (PDOException $e) {
        $message = "❌ Erreur dans les informations fournies: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un tournoi - Pro-Arena</title>
</head>
<body>
    <h1>Créer un nouveau tournoi</h1>
    <p><?php echo $message; ?></p>

    <form action="creer_tournoi.php" method="POST">
        <label>Titre du tournoi :</label><br>
        <input type="text" name="titre" required><br><br>

        <label>Description :</label><br>
        <textarea name="description" rows="5"></textarea><br><br>

        <label>Date et heure de début :</label><br>
        <input type="datetime-local" name="date_debut" required><br><br>

        <label>Lieu :</label><br>
        <input type="text" name="lieu" placeholder="Ex: Casablanca" required><br><br>

        <button type="submit">Enregistrer le tournoi</button>
    </form>

    <br>
    <a href="dashboard.php">Retour au dashboard</a>
</body>
</html>