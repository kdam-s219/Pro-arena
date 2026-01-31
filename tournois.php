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
    <title>Explorer les Tournois - ProArena</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">

    <style>
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            background: rgba(34,197,94,0.15);
            color: var(--accent);
            font-weight: 600;
        }

        .belt-badge {
            font-size: 0.75em;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            padding: 3px 8px;
            border-radius: 6px;
            margin: 3px 4px 0 0;
            display: inline-block;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-custom px-4">
    <a href="dashboard.php" class="navbar-brand text-white fw-bold">← ProArena</a>
</nav>

<div class="container my-4">

    <!-- Title -->
    <div class="glass-card mb-4">
        <h3 class="mb-1">🏆 Tournois disponibles</h3>
        <p class="text-muted-custom mb-0">
            Trouvez et filtrez les compétitions selon votre sport et votre niveau
        </p>
    </div>

    <!-- Filters -->
    <form method="GET" class="glass-card mb-4">
        <div class="row g-3 align-items-end">

            <div class="col-md-3">
                <label class="form-label">Ville</label>
                <select name="ville" class="form-control">
                    <option class="text-black small m-0" value="">Toutes les villes</option>
                    <option class="text-black small m-0" value="Casablanca" <?= $ville_filtre == 'Casablanca' ? 'selected' : '' ?>>Casablanca</option>
                    <option class="text-black small m-0" value="Rabat" <?= $ville_filtre == 'Rabat' ? 'selected' : '' ?>>Rabat</option>
                    <option class="text-black small m-0" value="Tanger" <?= $ville_filtre == 'Tanger' ? 'selected' : '' ?>>Tanger</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Sport</label>
                <select name="sport" id="filterSport" class="form-control" onchange="updateFilterBelts()">
                    <option class="text-black small m-0" value="">Tous les sports</option>
                    <option class="text-black small m-0" value="Judo" <?= $sport_filtre == 'Judo' ? 'selected' : '' ?>>Judo</option>
                    <option class="text-black small m-0 "value="Karate" <?= $sport_filtre == 'Karate' ? 'selected' : '' ?>>Karaté</option>
                    <option class="text-black small m-0 "value="Jujitsu" <?= $sport_filtre == 'Jujitsu' ? 'selected' : '' ?>>Jujitsu</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Niveau requis</label>
                <select name="niveau" id="filterLevel" class="form-control">
                    <option class="text-black small m-0" value="">Tous les niveaux</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom w-100">
                    Filtrer
                </button>
                <a href="tournois.php" class="btn btn-outline-custom w-100">
                    Reset
                </a>
            </div>

        </div>
    </form>

    <!-- Grid -->
    <div class="row g-4">
        <?php if (count($tournois) > 0): ?>
            <?php foreach ($tournois as $t): ?>
                <div class="col-lg-4 col-md-6">

                    <div class="glass-card h-100 fade-up">

                        <span class="role-badge"><?= htmlspecialchars($t['sport']) ?></span>

                        <h5 class="mt-3"><?= htmlspecialchars($t['titre']) ?></h5>

                        <p class="text-muted-custom mb-1">📍 <?= htmlspecialchars($t['lieu']) ?></p>
                        <p class="text-muted-custom mb-1">
                            📅 <?= date('d/m/Y', strtotime($t['date_debut'])) ?>
                        </p>
                        <p class="text-muted-custom mb-2">
                            ⏳ Limite :
                            <?= ($t['date_limite']) ? date('d/m/Y', strtotime($t['date_limite'])) : 'Non définie' ?>
                        </p>

                        <div class="mb-2">
                            <strong class="text-muted-custom">Grades autorisés</strong><br>
                            <?php
                            $badges = explode(", ", $t['niveau_requis']);
                            foreach ($badges as $b): ?>
                                <span class="belt-badge"><?= htmlspecialchars($b) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <p class="text-muted-custom mt-3 mb-3" style="font-size: 0.8rem;">
                            Organisé par <?= htmlspecialchars($t['club_nom']) ?>
                        </p>

                        <a href="details_tournoi.php?id=<?= $t['id'] ?>"
                           class="btn btn-primary-custom w-100">
                            Voir les détails
                        </a>

                    </div>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="glass-card text-center py-5">
                    <p class="text-muted-custom mb-0">
                        Aucun tournoi ne correspond à ces critères 🥋  
                        Essayez d'autres filtres.
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<script src="js/bootstrap.bundle.min.js"></script>

<script>
const beltsBySport = {
    "Judo": ["Blanche", "Jaune", "Orange", "Verte", "Bleue", "Marron", "Noire"],
    "Karate": ["Blanche", "Jaune", "Orange", "Verte", "Bleue", "Marron", "Noire"],
    "Jujitsu": ["Blanche", "Bleue", "Violette", "Marron", "Noire"]
};

function updateFilterBelts() {
    const sport = document.getElementById('filterSport').value;
    const levelSelect = document.getElementById('filterLevel');
    const currentNiveau = "<?= htmlspecialchars($niveau_filtre) ?>";

    // On force le texte en noir aussi pour l'option par défaut
    levelSelect.innerHTML = '<option value="" class="text-black">Tous les niveaux</option>';

    if (beltsBySport[sport]) {
        beltsBySport[sport].forEach(belt => {
            const option = document.createElement('option');
            option.value = belt;
            option.text = "Ceinture " + belt;
            
            // AJOUT ICI : On force la couleur noire sur l'élément option
            option.classList.add('text-black'); 
            
            if (belt === currentNiveau) option.selected = true;
            levelSelect.appendChild(option);
        });
    }
}

window.addEventListener('DOMContentLoaded', updateFilterBelts);
</script>

</body>
</html>
