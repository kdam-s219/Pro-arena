<?php
require_once 'config.php'; // Inclure le fichier de configuration pour la connexion à la base de données

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] == 'athlete' ) {
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
    <title>Créer un tournoi - ProArena</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-custom px-4">
    <a href="dashboard.php" class="navbar-brand text-white fw-bold">← ProArena</a>

    <div class="ms-auto">
        <a href="dashboard.php" class="btn btn-outline-custom btn-sm">Retour dashboard</a>
    </div>
</nav>

<div class="container my-5">

    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="glass-card fade-up">

                <h3 class="mb-4">Créer un nouveau tournoi</h3>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-info">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <form action="creer_tournoi.php" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Titre du tournoi</label>
                        <input type="text" name="titre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4" class="form-control"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date et heure de début</label>
                        <input type="datetime-local" name="date_debut" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Lieu</label>
                        <input type="text" name="lieu" class="form-control" placeholder="Ex: Casablanca" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="dashboard.php" class="btn btn-outline-custom">
                            Annuler
                        </a>

                        <button type="submit" class="btn btn-primary-custom">
                            Enregistrer le tournoi
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
