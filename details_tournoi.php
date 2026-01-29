<?php
require_once 'config.php';

// Sécurité : Vérification de la connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupération de l'ID du tournoi depuis l'URL
$tournoi_id = $_GET['id'] ?? null;

if (!$tournoi_id) {
    header("Location: tournois.php");
    exit();
}

// Requête pour récupérer toutes les infos du tournoi et du club organisateur
$sql = "SELECT t.*, u.nom as club_nom, u.email as club_email 
        FROM tournois t 
        JOIN utulisateurs u ON t.club_id = u.id 
        WHERE t.id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $tournoi_id]);
$tournoi = $stmt->fetch();

if (!$tournoi) {
    echo "Tournoi introuvable.";
    exit();
}

// Vérifier si l'athlète est déjà inscrit
$check_sql = "SELECT * FROM inscription WHERE id_utulisateur = :uid AND id_competition = :cid";
$check_stmt = $pdo->prepare($check_sql);
$check_stmt->execute(['uid' => $_SESSION['user_id'], 'cid' => $tournoi_id]);
$est_deja_inscrit = $check_stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du Tournoi - <?= htmlspecialchars($tournoi['titre']) ?></title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8f9fa; padding: 40px; }
        .details-container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .header { border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .sport-tag { display: inline-block; background: #007bff; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold; margin-bottom: 10px; }
        .section { margin-bottom: 20px; }
        .section-title { font-weight: bold; color: #333; display: block; margin-bottom: 5px; text-transform: uppercase; font-size: 0.8em; letter-spacing: 1px; }
        .btn-inscription { display: inline-block; width: 100%; text-align: center; padding: 15px; background: #28a745; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 1.1em; transition: 0.3s; }
        .btn-inscription:hover { background: #218838; }
        .btn-deja { background: #6c757d; cursor: not-allowed; }
        .club-info { background: #f1f3f5; padding: 20px; border-radius: 10px; margin-top: 30px; border-left: 5px solid #007bff; }
    </style>
</head>
<body>

<div class="details-container">
    <a href="tournois.php" style="text-decoration: none; color: #007bff;">← Retour aux tournois</a>

    <div class="header">
        <span class="sport-tag"><?= htmlspecialchars($tournoi['sport']) ?></span>
        <h1><?= htmlspecialchars($tournoi['titre']) ?></h1>
    </div>

    <div class="section">
        <span class="section-title">Description</span>
        <p><?= nl2br(htmlspecialchars($tournoi['description'])) ?></p>
    </div>

    <div class="section">
        <span class="section-title">Informations Logistiques</span>
        <p>📍 <strong>Lieu :</strong> <?= htmlspecialchars($tournoi['lieu']) ?></p>
        <p>📅 <strong>Date du tournoi :</strong> <?= date('d/m/Y H:i', strtotime($tournoi['date_debut'])) ?></p>
        <p>⏳ <strong>Date limite d'inscription :</strong> <?= date('d/m/Y H:i', strtotime($tournoi['date_limite'])) ?></p>
        <p>🥋 <strong>Grades acceptés :</strong> <?= htmlspecialchars($tournoi['niveau_requis']) ?></p>
    </div>

    <div class="club-info">
        <span class="section-title">Organisateur</span>
        <p>🏛️ <strong>Club :</strong> <?= htmlspecialchars($tournoi['club_nom']) ?></p>
        
    </div>

    <div style="margin-top: 30px;">
        <?php if ($_SESSION['user_role'] === 'athlete'): ?>
            <?php if ($est_deja_inscrit): ?>
                <span class="btn-inscription btn-deja">✅ Déjà inscrit à ce tournoi</span>
            <?php else: ?>
                <a href="inscription_tournoi.php?id=<?= $tournoi['id'] ?>" class="btn-inscription">S'inscrire maintenant</a>
            <?php endif; ?>
        <?php else: ?>
            <p style="text-align: center; color: #999; font-style: italic;">Seuls les athlètes peuvent s'inscrire.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>