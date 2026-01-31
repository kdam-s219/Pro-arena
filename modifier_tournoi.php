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

    <!-- CSS LOCAL -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">
</head>

<body>

<div class="container py-5" style="max-width: 900px;">

    <a href="gestion_tournoi.php?id=<?= $id_tournoi ?>" class="text-decoration-none text-muted">
        ← Annuler et retour
    </a>

    <h1 class="mt-3 mb-4">Modifier le tournoi</h1>

    <?php if (isset($erreur)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($erreur) ?>
        </div>
    <?php endif; ?>

    <div class="glass-card">

        <form method="POST">

            <!-- Titre -->
            <div class="mb-3">
                <label class="form-label">Titre du tournoi</label>
                <input type="text"
                       name="titre"
                       class="form-control"
                       value="<?= htmlspecialchars($tournoi['titre']) ?>"
                       required>
            </div>

            <!-- Sport -->
            <div class="mb-3">
                <label class="form-label">Sport</label>
                <select name="sport" class="form-select" required>
                    <option value="Judo" <?= $tournoi['sport'] === 'Judo' ? 'selected' : '' ?>>Judo</option>
                    <option value="Karate" <?= $tournoi['sport'] === 'Karate' ? 'selected' : '' ?>>Karaté</option>
                    <option value="Jujitsu" <?= $tournoi['sport'] === 'Jujitsu' ? 'selected' : '' ?>>Jujitsu</option>
                </select>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description"
                          class="form-control"
                          rows="4"><?= htmlspecialchars($tournoi['description']) ?></textarea>
            </div>

            <!-- Lieu -->
            <div class="mb-3">
                <label class="form-label">Lieu</label>
                <input type="text"
                       name="lieu"
                       class="form-control"
                       value="<?= htmlspecialchars($tournoi['lieu']) ?>"
                       required>
            </div>

            <!-- Dates -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date de début</label>
                    <input type="datetime-local"
                           name="date_debut"
                           class="form-control"
                           value="<?= date('Y-m-d\TH:i', strtotime($tournoi['date_debut'])) ?>"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Date limite d'inscription</label>
                    <input type="datetime-local"
                           name="date_limite"
                           class="form-control"
                           value="<?= date('Y-m-d\TH:i', strtotime($tournoi['date_limite'])) ?>"
                           required>
                </div>
            </div>

            <!-- Niveaux -->
            <div class="mb-4">
                <label class="form-label">
                    Niveaux requis actuels :
                    <strong><?= htmlspecialchars($tournoi['niveau_requis']) ?></strong>
                </label>

                <div class="d-flex flex-wrap gap-3 mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="niveaux[]" value="Blanche" id="b1">
                        <label class="form-check-label" for="b1">Blanche</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="niveaux[]" value="Bleue" id="b2">
                        <label class="form-check-label" for="b2">Bleue</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="niveaux[]" value="Marron" id="b3">
                        <label class="form-check-label" for="b3">Marron</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="niveaux[]" value="Noire" id="b4">
                        <label class="form-check-label" for="b4">Noire</label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end gap-2">
                <a href="gestion_tournoi.php?id=<?= $id_tournoi ?>"
                   class="btn btn-outline-secondary">
                    Annuler
                </a>

                <button type="submit" class="btn btn-primary-custom">
                    Enregistrer les modifications
                </button>
            </div>

        </form>

    </div>

</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
