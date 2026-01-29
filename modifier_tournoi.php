<?php
require_once 'config.php';

// Sécurité : Seul le club peut modifier
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] == 'athlete') {
    header("Location: dashboard.php");
    exit();
}

$id_tournoi = $_GET['id'];

// 1. CHARGEMENT DES DONNÉES ACTUELLES
$sql_get = "SELECT * FROM tournois WHERE id = :id";
$stmt_get = $pdo->prepare($sql_get);
$stmt_get->execute(['id' => $id_tournoi]);
$tournoi = $stmt_get->fetch();

// 2. TRAITEMENT DE LA MISE À JOUR (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $sport = $_POST['sport'];
    $lieu = $_POST['lieu'];
    $date_debut = $_POST['date_debut'];
    $date_limite = $_POST['date_limite'];
    
    // Gestion des niveaux (ceintures) cochés
    $niveaux = isset($_POST['niveaux']) ? implode(", ", $_POST['niveaux']) : $tournoi['niveau_requis'];

    try {
        $sql_update = "UPDATE tournois SET 
                        titre = :titre, 
                        description = :description, 
                        sport = :sport, 
                        lieu = :lieu, 
                        date_debut = :date_debut, 
                        date_limite = :date_limite, 
                        niveau_requis = :niveau 
                      WHERE id = :id";
        
        $stmt_up = $pdo->prepare($sql_update);
        $stmt_up->execute([
            'titre' => $titre,
            'description' => $description,
            'sport' => $sport,
            'lieu' => $lieu,
            'date_debut' => $date_debut,
            'date_limite' => $date_limite,
            'niveau' => $niveaux,
            'id' => $id_tournoi
        ]);
        
        header("Location: gestion_tournoi.php?id=" . $id_tournoi . "&msg=success");
        exit();
    } catch (PDOException $e) {
        $erreur = "Erreur lors de la modification : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier - <?= htmlspecialchars($tournoi['titre']) ?></title>
</head>
<body>
    <a href="gestion_tournoi.php?id=<?= $id_tournoi ?>">Annuler et retour</a>
    <h1>Modifier le tournoi</h1>

    <?php if(isset($erreur)) echo "<p style='color:red;'>$erreur</p>"; ?>

    <form method="POST">
        <label>Titre :</label><br>
        <input type="text" name="titre" value="<?= htmlspecialchars($tournoi['titre']) ?>" required><br><br>

        <label>Sport :</label><br>
        <select name="sport" required>
            <option value="Judo" <?= $tournoi['sport'] == 'Judo' ? 'selected' : '' ?>>Judo</option>
            <option value="Karate" <?= $tournoi['sport'] == 'Karate' ? 'selected' : '' ?>>Karaté</option>
            <option value="Jujitsu" <?= $tournoi['sport'] == 'Jujitsu' ? 'selected' : '' ?>>Jujitsu</option>
        </select><br><br>

        <label>Description :</label><br>
        <textarea name="description" rows="4" cols="50"><?= htmlspecialchars($tournoi['description']) ?></textarea><br><br>

        <label>Lieu :</label><br>
        <input type="text" name="lieu" value="<?= htmlspecialchars($tournoi['lieu']) ?>" required><br><br>

        <label>Date de début :</label><br>
        <input type="datetime-local" name="date_debut" value="<?= date('Y-m-d\TH:i', strtotime($tournoi['date_debut'])) ?>" required><br><br>

        <label>Date limite d'inscription :</label><br>
        <input type="datetime-local" name="date_limite" value="<?= date('Y-m-d\TH:i', strtotime($tournoi['date_limite'])) ?>" required><br><br>

        <label>Niveau requis actuel :</label> <strong><?= htmlspecialchars($tournoi['niveau_requis']) ?></strong><br>
        <p style="font-size: 0.9em; color: gray;">(Cochez de nouvelles cases pour changer les grades autorisés)</p>
        
        <input type="checkbox" name="niveaux[]" value="Blanche"> Blanche
        <input type="checkbox" name="niveaux[]" value="Bleue"> Bleue
        <input type="checkbox" name="niveaux[]" value="Marron"> Marron
        <input type="checkbox" name="niveaux[]" value="Noire"> Noire
        <br><br>

        <button type="submit" style="background-color: green; color: white; padding: 10px;">Enregistrer les modifications</button>
    </form>
</body>
</html>