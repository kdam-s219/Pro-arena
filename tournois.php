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
        WHERE 1=1"; 

$params = [];

if (!empty($ville_filtre)) {
    $sql .= " AND t.lieu = :ville";
    $params['ville'] = $ville_filtre;
}
if (!empty($sport_filtre)) {
    $sql .= " AND t.sport = :sport";
    $params['sport'] = $sport_filtre;
}

// RÉPARATION DU FILTRE NIVEAU : On cherche si la ceinture choisie existe dans la liste enregistrée
if (!empty($niveau_filtre) && $niveau_filtre !== 'Tous') {
    $sql .= " AND t.niveau_requis LIKE :niveau";
    $params['niveau'] = '%' . $niveau_filtre . '%';
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
        
        /* Barre de filtres */
        .filter-section { 
            background: white; padding: 20px; border-radius: 10px; 
            margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        select, button { padding: 10px; border-radius: 5px; border: 1px solid #ddd; min-width: 150px; }
        .btn-filter { background: #007bff; color: white; border: none; cursor: pointer; font-weight: bold; }
        
        /* Grille de Cartes */
        .tournois-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
            gap: 25px; 
        }
        .card { 
            background: white; border-radius: 12px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 6px solid #28a745;
            padding: 20px; transition: transform 0.2s;
        }
        .card:hover { transform: translateY(-5px); }
        .sport-badge { 
            background: #e7f1ff; color: #007bff; padding: 5px 12px; 
            border-radius: 20px; font-size: 0.8em; font-weight: bold; display: inline-block;
        }
        .card h3 { margin: 15px 0 10px 0; color: #333; font-size: 1.4em; }
        .card p { font-size: 0.9em; color: #666; margin: 8px 0; }
        
        /* Badges de ceintures sur la carte */
        .belt-badge {
            font-size: 0.75em; background: #f8f9fa; border: 1px solid #dee2e6;
            padding: 2px 8px; border-radius: 4px; margin-right: 4px; color: #495057; display: inline-block;
        }

        .btn-details { 
            display: block; text-align: center; background: #28a745; 
            color: white; padding: 12px; text-decoration: none; 
            border-radius: 6px; margin-top: 15px; font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" style="text-decoration: none; color: #007bff;">← Retour au Dashboard</a>
    <h1 style="margin-top: 15px;">🏆 Tournois Disponibles</h1>

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
            <select name="sport" id="filterSport" onchange="updateFilterBelts()">
                <option value="">Tous les sports</option>
                <option value="Judo" <?= $sport_filtre == 'Judo' ? 'selected' : '' ?>>Judo</option>
                <option value="Karate" <?= $sport_filtre == 'Karate' ? 'selected' : '' ?>>Karaté</option>
                <option value="Jujitsu" <?= $sport_filtre == 'Jujitsu' ? 'selected' : '' ?>>Jujitsu</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Ceinture requise</label>
            <select name="niveau" id="filterLevel">
                <option value="">Tous les niveaux</option>
                </select>
        </div>

        <button type="submit" class="btn-filter">Filtrer</button>
        <a href="tournois.php" style="font-size: 0.8em; color: #666; margin-left: 10px;">Réinitialiser</a>
    </form>

    <div class="tournois-grid">
        <?php if (count($tournois) > 0): ?>
            <?php foreach ($tournois as $t): ?>
                <div class="card">
                    <span class="sport-badge"><?= htmlspecialchars($t['sport']) ?></span>
                    <h3><?= htmlspecialchars($t['titre']) ?></h3>
                    
                    <p>📍 <strong>Lieu :</strong> <?= htmlspecialchars($t['lieu']) ?></p>
                    <p>📅 <strong>Date :</strong> <?= date('d/m/Y', strtotime($t['date_debut'])) ?></p>
                    <p>⏳ <strong>Limite :</strong> <?= ($t['date_limite']) ? date('d/m/Y', strtotime($t['date_limite'])) : 'Non définie' ?></p>
                    
                    <p style="margin-top: 10px;">🥋 <strong>Grades autorisés :</strong><br>
                        <?php 
                        $badges = explode(", ", $t['niveau_requis']);
                        foreach($badges as $b): ?>
                            <span class="belt-badge"><?= htmlspecialchars($b) ?></span>
                        <?php endforeach; ?>
                    </p>

                    <p style="margin-top: 15px; font-size: 0.8em; color: #999;">Organisé par <?= htmlspecialchars($t['club_nom']) ?></p>

                    <a href="details_tournoi.php?id=<?= $t['id'] ?>" class="btn-details">Voir les détails</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: white; border-radius: 10px;">
                <p>Aucun tournoi ne correspond à ces critères. Essayez d'autres filtres ! 🥋</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Configuration des ceintures réelles
const beltsBySport = {
    "Judo": ["Blanche", "Jaune", "Orange", "Verte", "Bleue", "Marron", "Noire"],
    "Karate": ["Blanche", "Jaune", "Orange", "Verte", "Bleue", "Marron", "Noire"],
    "Jujitsu": ["Blanche", "Bleue", "Violette", "Marron", "Noire"]
};

function updateFilterBelts() {
    const sport = document.getElementById('filterSport').value;
    const levelSelect = document.getElementById('filterLevel');
    const currentNiveau = "<?= htmlspecialchars($niveau_filtre) ?>";

    levelSelect.innerHTML = '<option value="">Tous les niveaux</option>';

    if (beltsBySport[sport]) {
        beltsBySport[sport].forEach(belt => {
            const option = document.createElement('option');
            option.value = belt;
            option.text = "Ceinture " + belt;
            // On garde le filtre actif si la page se recharge
            if(belt === currentNiveau) option.selected = true;
            levelSelect.appendChild(option);
        });
    }
}

// Lancement au chargement pour que le menu ne soit pas vide après filtrage
window.addEventListener('DOMContentLoaded', updateFilterBelts);
</script>

</body>
</html>