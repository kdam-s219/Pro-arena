<?php
require_once 'config.php'; 

// Sécurité : Seul un club peut créer un tournoi
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] == 'athlete' ) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $sport = $_POST['sport'];
    $date_debut = $_POST['date_debut'];
    $date_limite = $_POST['date_limite'];
    $lieu = $_POST['lieu'];
    $club_id = $_SESSION['user_id'];

    // Gestion de la sélection multiple des niveaux (ceintures)
    $niveaux_selectionnes = isset($_POST['niveaux']) ? implode(", ", $_POST['niveaux']) : "Tous";

    try {
        // Insertion dans la table tournois avec les nouvelles colonnes
        $sql = "INSERT INTO tournois (titre, sport, description, date_debut, date_limite, niveau_requis, lieu, club_id) 
                VALUES (:titre, :sport, :description, :date_debut, :date_limite, :niveau, :lieu, :club_id)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titre' => $titre,
            'sport' => $sport,
            'description' => $description,
            'date_debut' => $date_debut,
            'date_limite' => $date_limite,
            'niveau' => $niveaux_selectionnes,
            'lieu' => $lieu,
            'club_id' => $club_id
        ]);
        $message = "✅ Tournoi créé avec succès !";
    }
    catch (PDOException $e) {
        $message = "❌ Erreur : " . $e->getMessage();
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
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endif; ?>

                <form action="creer_tournoi.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Titre du tournoi</label>
                        <input type="text" name="titre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Discipline (Sport)</label>
                        <select name="sport" id="sportSelect" class="form-select" onchange="updateBelts()" required>
                            <option value="">-- Choisir un sport --</option>
                            <option value="Judo">Judo</option>
                            <option value="Karate">Karaté</option>
                            <option value="Jujitsu">Jujitsu</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="2" class="form-control"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de début</label>
                            <input type="datetime-local" name="date_debut" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date limite d'inscription</label>
                            <input type="datetime-local" name="date_limite" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ville</label>
                        <select name="lieu" class="form-select" required>
                            <option value="Casablanca">Casablanca</option>
                            <option value="Rabat">Rabat</option>
                            <option value="Tanger">Tanger</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Niveaux autorisés (Sélectionnez les ceintures)</label>
                        <div id="beltContainer" class="p-3 border rounded bg-light" style="max-height: 180px; overflow-y: auto;">
                            <p class="text-muted small m-0">Sélectionnez d'abord une discipline...</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="dashboard.php" class="btn btn-outline-custom">Annuler</a>
                        <button type="submit" class="btn btn-primary-custom">Enregistrer le tournoi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Configuration des grades par sport
const beltData = {
    "Judo": ["Blanche", "Jaune", "Orange", "Verte", "Bleue", "Marron", "Noire"],
    "Karate": ["Blanche", "Jaune", "Orange", "Verte", "Bleue", "Marron", "Noire"],
    "Jujitsu": ["Blanche", "Bleue", "Violette", "Marron", "Noire"]
};

function updateBelts() {
    const sport = document.getElementById('sportSelect').value;
    const container = document.getElementById('beltContainer');
    container.innerHTML = ''; 

    if (beltData[sport]) {
        beltData[sport].forEach(belt => {
            const div = document.createElement('div');
            div.className = 'form-check mb-1';
            // J'ai ajouté 'text-black' dans la classe du label ci-dessous
            div.innerHTML = `
                <input class="form-check-input" type="checkbox" name="niveaux[]" value="${belt}" id="belt_${belt}">
                <label class="form-check-label text-black" for="belt_${belt}">Ceinture ${belt}</label>
            `;
            container.appendChild(div);
        });
    } else {
        // Ici aussi, on met le texte en noir pour le message par défaut
        container.innerHTML = '<p class="text-black small m-0">Sélectionnez d\'abord une discipline...</p>';
    }
}
</script>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>