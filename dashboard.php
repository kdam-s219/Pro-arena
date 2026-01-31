<?php
require_once 'config.php';

// Sécurité : Redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1. Récupération des données utilisateur pour la Sidebar
$sql_user = "SELECT * FROM utulisateurs WHERE id = :id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['id' => $_SESSION['user_id']]);
$user_data = $stmt_user->fetch();

// 2. LOGIQUE ATHLÈTE : Récupération des inscriptions
$mes_inscriptions = [];
if ($_SESSION['user_role'] === 'athlete') {
    $sql_mes_inscr = "SELECT t.* FROM tournois t 
                      JOIN inscription i ON t.id = i.id_competition 
                      WHERE i.id_utulisateur = :user_id";
    $stmt = $pdo->prepare($sql_mes_inscr);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $mes_inscriptions = $stmt->fetchAll();
}

// 3. LOGIQUE CLUB : Récupération des tournois créés par ce club
$mes_tournois_crees = [];
if ($_SESSION['user_role'] === 'club') {
    $sql_club_tournois = "SELECT * FROM tournois WHERE club_id = :club_id ORDER BY date_debut ASC";
    $stmt_club = $pdo->prepare($sql_club_tournois);
    $stmt_club->execute(['club_id' => $_SESSION['user_id']]);
    $mes_tournois_crees = $stmt_club->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - ProArena</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
        body { background-color: #0b0e14; color: white; min-height: 100vh; }
        .sidebar-profile { background: rgba(255, 255, 255, 0.05); border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.1); padding: 25px; }
        .glass-card { background: rgba(255, 255, 255, 0.03); border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.08); padding: 20px; transition: 0.3s; }
        .glass-card:hover { transform: translateY(-5px); border-color: #0d6efd; }
        .btn-primary-custom { background-color: #0d6efd; color: white; border: none; }

        /* --- CORRECTION ICI --- */
        /* On force le conteneur de l'avatar à être la référence de position */
        .user-dropdown {
            position: relative !important;
            display: inline-block;
        }

        /* Menu déroulant avec position absolue */
        .profile-menu {
            display: none;
            position: absolute; 
            top: 110%; /* Se place juste en dessous de l'avatar (plus fiable que 55px) */
            right: 0;
            background: rgba(30, 35, 45, 0.95); /* Fond légèrement plus foncé pour la lisibilité */
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.5);
            z-index: 9999; /* Très haut pour passer au-dessus de tout */
            min-width: 180px;
            overflow: hidden;
        }

        .profile-menu a {
            display: block;
            padding: 12px 20px;
            color: #e0e0e0;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.95rem;
        }
        .profile-menu a:hover {
            background-color: rgba(13, 110, 253, 0.15); /* Couleur primaire en hover */
            color: white;
            padding-left: 25px; /* Petit effet de glissement */
        }

        /* Avatar du profil */
        .navbar-custom {
            position: relative; 
            z-index: 1000;
        }

        .navbar-custom .profile-icon {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .navbar-custom .profile-icon:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-custom px-4 shadow-sm">
    <span class="navbar-brand text-white fw-bold">ProArena</span>
    <div class="ms-auto d-flex align-items-center gap-3">
        
        <div class="dropdown user-dropdown">
            <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['user_prenom'].'+'.$_SESSION['user_name'] ?>&background=0D6EFD&color=fff&size=128" 
                 class="rounded-circle border border-primary p-1 shadow-sm profile-icon" width="40" alt="Avatar" onclick="toggleProfileMenu()">
            
            <div id="profileMenu" class="profile-menu">
                <div class="px-3 py-2 border-bottom border-secondary border-opacity-25 text-muted small">
                    Compte
                </div>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="porifil.php">👤 Mon profil</a>
                <?php endif; ?>
                <a href="settings.php">⚙️ Paramètres</a>
                <div class="border-top border-secondary border-opacity-25 my-1"></div>
                <a href="logout.php" class="text-danger">🚪 Déconnexion</a>
            </div>
        </div>

    </div>
</nav>

<div class="container-fluid px-4 my-4">
    <div class="row">
        
        <div class="col-lg-3 mb-4">
            <div class="sidebar-profile text-center sticky-top" style="top: 20px; z-index: 1;">
                <div class="mb-3">
                    <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['user_prenom'].'+'.$_SESSION['user_name'] ?>&background=0D6EFD&color=fff&size=128" 
                         class="rounded-circle border border-primary p-1 shadow-sm" width="100" alt="Avatar">
                </div>
                <h4 class="mb-1"><?= htmlspecialchars($_SESSION['user_prenom'] . " " . $_SESSION['user_name']); ?></h4>
                <span class="badge bg-primary mb-3"><?= ucfirst($_SESSION['user_role']); ?></span>
                
                <hr class="opacity-25 text-white">
                
                <div class="text-start px-2 mt-3">
                    <p class="text-white-50 small m-0">🥋 Informations :</p>
                    <p class="small fw-bold">
                        <?= $_SESSION['user_role'] === 'athlete' ? 'Discipline : ' . htmlspecialchars($user_data['sport_prefere'] ?? 'Non défini') : 'Compétitions gérées' ?>
                    </p>
                </div>
               <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="porifil.php" class="btn btn-outline-light btn-sm w-100 mt-3">Gérer mon compte</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-9">
            
            <div class="glass-card mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Bienvenue 👋</h2>
                    <p class="text-muted-custom mb-0">Ravi de vous revoir sur ProArena.</p>
                </div>
                <?php if ($_SESSION['user_role'] === 'club'): ?>
                    <a href="creer_tournoi.php" class="btn btn-primary-custom">➕ Créer un tournoi</a>
                <?php endif; ?>
            </div>

            <?php if ($_SESSION['user_role'] === 'athlete'): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Mes inscriptions actives 🏆</h4>
                    <a href="tournois.php" class="btn btn-outline-custom btn-sm">Explorer les tournois</a>
                </div>
                <div class="row">
                    <?php if (count($mes_inscriptions) > 0): ?>
                        <?php foreach ($mes_inscriptions as $insc): ?>
                            <div class="col-md-6 col-xl-4 mb-4">
                                <div class="glass-card h-100 d-flex flex-column border-top border-primary border-4">
                                    <div class="p-2">
                                        <span class="badge bg-dark text-info mb-2"><?= htmlspecialchars($insc['sport']) ?></span>
                                        <h5 class="fw-bold mb-3"><?= htmlspecialchars($insc['titre']) ?></h5>
                                        <p class="small text-muted-custom mb-1">📍 <?= htmlspecialchars($insc['lieu']) ?></p>
                                        <p class="small text-muted-custom">📅 <?= date('d/m/Y', strtotime($insc['date_debut'])) ?></p>
                                    </div>
                                    <div class="mt-auto pt-3">
                                        <a href="annuler_inscription.php?id=<?= $insc['id'] ?>" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Annuler ?')">Se désinscrire</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12"><p class="glass-card text-center">Aucune inscription en cours.</p></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
<?php if ($_SESSION['user_role'] === 'club'): ?>
                <h4 class="mb-4">Mes tournois organisés 🏟️</h4>
                <div class="row">
                    <?php if (count($mes_tournois_crees) > 0): ?>
                        <?php foreach ($mes_tournois_crees as $tournoi): ?>
                            <div class="col-md-6 col-xl-4 mb-4">
                                <div class="glass-card h-100 d-flex flex-column border-top border-success border-4">
                                    <div class="p-2">
                                        <span class="badge bg-dark text-success mb-2"><?= htmlspecialchars($tournoi['sport']) ?></span>
                                        <h5 class="fw-bold mb-3"><?= htmlspecialchars($tournoi['titre']) ?></h5>
                                        <p class="small text-muted-custom mb-1">📍 <?= htmlspecialchars($tournoi['lieu']) ?></p>
                                        <p class="small text-muted-custom">📅 <?= date('d/m/Y', strtotime($tournoi['date_debut'])) ?></p>
                                    </div>
                                    <div class="mt-auto pt-3 d-flex gap-2">
                                        
                                        <a href="gestion_tournoi.php?id=<?= $tournoi['id'] ?>" class="btn btn-outline-info btn-sm flex-grow-1">gestion de tournoi</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="glass-card text-center py-5">
                                <p class="text-muted mb-3">Vous n'avez pas encore créé de tournoi.</p>
                                <a href="creer_tournoi.php" class="btn btn-primary-custom">Créer mon premier tournoi</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        // J'ai ajouté une fermeture au clic en dehors pour l'UX
        if (menu.style.display === 'block') {
            menu.style.display = 'none';
        } else {
            menu.style.display = 'block';
        }
    }

    // Fermer le menu si on clique ailleurs sur la page
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('profileMenu');
        const icon = document.querySelector('.profile-icon');
        // Si le clic n'est ni sur le menu ni sur l'icone
        if (!menu.contains(event.target) && !icon.contains(event.target)) {
            menu.style.display = 'none';
        }
    });
</script>
<?php if ($_SESSION['user_role'] === 'admin'): ?>
    <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center mb-4" style="background: rgba(13, 110, 253, 0.1); border-radius: 15px;">
        <div class="d-flex align-items-center gap-3">
            <span class="fs-3">🛡️</span>
            <div>
                <h5 class="mb-0 text-white">Mode Administrateur</h5>
                <small class="text-white-50">Vous avez accès aux fonctions de gestion globale.</small>
            </div>
        </div>
        <a href="admin_dashboard.php" class="btn btn-primary-custom btn-sm px-4">Accéder au Panel Admin</a>
    </div>
<?php endif; ?>

</body>

</html>