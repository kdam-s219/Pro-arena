<?php
require_once 'config.php';

// Sécurité : Seul un utilisateur connecté peut accéder
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1. Récupération des filtres depuis l'URL (méthode GET)
$ville_filtre = $_GET['ville'] ?? '';
$sport_filtre = $_GET['sport'] ?? '';
$niveau_filtre = $_GET['niveau'] ?? '';

// 2. Construction de la requête SQL dynamique
$sql = "SELECT t.*, u.nom as club_nom 
        FROM tournois t 
        JOIN utulisateurs u ON t.club_id = u.id 
        WHERE 1=1"; // "1=1" permet d'ajouter des "AND" facilement

$params = [];

if (!empty($ville_filtre)) {
    $sql .= " AND t.lieu = :ville";
    $params['ville'] = $ville_filtre;
}
if (!empty($sport_filtre)) {
    $sql .= " AND t.sport = :sport";
    $params['sport'] = $sport_filtre;
}
if (!empty($niveau_filtre) && $niveau_filtre !== 'Tous') {
    $sql .= " AND t.niveau_requis = :niveau";
    $params['niveau'] = $niveau_filtre;
}

$sql .= " ORDER BY t.date_debut ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tournois = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Explorer les Tournois - Pro-Arena</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: auto; }
        
        /* Style de la barre de filtres */
        .filter-section { 
            background: white; padding: 20px; border-radius: 10px; 
            margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        select, button { padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
        .btn-filter { background: #007bff; color: white; border: none; cursor: pointer; font-weight: bold; }
        
        /* Style des Cartes */
        .tournois-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
            gap: 25px; 
        }
        .card { 
            background: white; border-radius: 12px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 6px solid #007bff;
            padding: 20px; transition: transform 0.2s;
        }
        .card:hover { transform: translateY(-5px); }
        .sport-badge { 
            background: #e7f1ff; color: #007bff; padding: 5px 12px; 
            border-radius: 20px; font-size: 0.8em; font-weight: bold; display: inline-block;
        }
        .card h3 { margin: 15px 0 10px 0; color: #333; }
        .card p { font-size: 0.9em; color: #666; margin: 5px 0; }
        .card .description { color: #888; font-style: italic; margin-bottom: 15px; }
        .btn-details { 
            display: block; text-align: center; background: #28a745; 
            color: white; padding: 10px; text-decoration: none; 
            border-radius: 6px; margin-top: 15px; font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" style="text-decoration: none; color: #007bff;">← Retour au Dashboard</a>
    <h1>🏆 Tournois Disponibles</h1>

    <form class="filter-section" method="GET">
        <div class="filter-group">
            <label>Ville</label>
            <select name="ville">
                <option value="">Toutes les villes</option>
                <option value="Casablanca" <?= $ville_filtre == 'Casablanca' ? 'selected' : '' ?>>Casablanca</option>
                <option value="Rabat" <?= $ville_filtre == 'Rabat' ? 'selected' : '' ?>>Rabat</option>
                <option value="Tanger" <?= $ville_filtre == 'Tanger' ? 'selected' : '' ?>>Tanger</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Sport</label>
            <select name="sport">
                <option value="">Tous les sports</option>
                <option value="Judo" <?= $sport_filtre == 'Judo' ? 'selected' : '' ?>>Judo</option>
                <option value="Karate" <?= $sport_filtre == 'Karate' ? 'selected' : '' ?>>Karaté</option>
                <option value="Jujitsu" <?= $sport_filtre == 'Jujitsu' ? 'selected' : '' ?>>Jujitsu</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Niveau</label>
            <select name="niveau">
                <option value="Tous">Tous les niveaux</option>
                <option value="Blanche" <?= $niveau_filtre == 'Blanche' ? 'selected' : '' ?>>Ceinture Blanche</option>
                <option value="Noire" <?= $niveau_filtre == 'Noire' ? 'selected' : '' ?>>Ceinture Noire</option>
            </select>
        </div>

        <button type="submit" class="btn-filter">Appliquer les filtres</button>
        <a href="tournois.php" style="font-size: 0.8em; color: #666;">Réinitialiser</a>
    </form>

    <div class="tournois-grid">
        <?php if (count($tournois) > 0): ?>
            <?php foreach ($tournois as $t): ?>
                <div class="card">
                    <span class="sport-badge"><?= htmlspecialchars($t['sport']) ?></span>
                    <h3><?= htmlspecialchars($t['titre']) ?></h3>
                    <p class="description"><?= htmlspecialchars(substr($t['description'], 0, 80)) ?>...</p>
                    
                    <p><strong>📍 Lieu :</strong> <?= htmlspecialchars($t['lieu']) ?></p>
                    <p><strong>📅 Date :</strong> <?= date('d/m/Y', strtotime($t['date_debut'])) ?></p>
                    <p><strong>⏳ Limite :</strong> <?= date('d/m/Y', strtotime($t['date_limite'])) ?></p>
                    <p><strong>🥋 Niveau :</strong> <?= htmlspecialchars($t['niveau_requis']) ?></p>
                    <p><strong>👤 Organisé par :</strong> <?= htmlspecialchars($t['club_nom']) ?></p>

                    <a href="details_tournoi.php?id=<?= $t['id'] ?>" class="btn-details">Voir les détails</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun tournoi ne correspond à vos critères.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>