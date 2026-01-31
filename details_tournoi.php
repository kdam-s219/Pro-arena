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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
        /* Styles spécifiques à cette page pour ajuster les détails */
        .info-label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 1rem;
            font-weight: 500;
            color: white;
        }
        .club-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .btn-disabled-custom {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-muted);
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: not-allowed;
            border-radius: 12px;
            padding: 10px 22px;
            width: 100%;
            display: inline-block;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container py-5">
    
    <div class="mb-4">
        <a href="tournois.php" class="text-decoration-none d-flex align-items-center gap-2 text-muted-custom hover-white">
            <span>&larr;</span> Retour aux tournois
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="glass-card fade-up">
                
                <div class="border-bottom border-secondary border-opacity-25 pb-4 mb-4">
                    <span class="badge mb-3" style="background-color: var(--accent); color: #052e16; font-size: 0.9rem;">
                        <?= htmlspecialchars($tournoi['sport']) ?>
                    </span>
                    <h1 class="display-5 fw-bold mb-2"><?= htmlspecialchars($tournoi['titre']) ?></h1>
                </div>

                <div class="row g-5">
                    <div class="col-md-7">
                        <h4 class="mb-3 text-white">À propos du tournoi</h4>
                        <div class="text-muted-custom" style="line-height: 1.7;">
                            <?= nl2br(htmlspecialchars($tournoi['description'])) ?>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="p-4 rounded-4" style="background: rgba(0,0,0,0.2);">
                            
                            <h5 class="mb-4 text-white">Informations Clés</h5>

                            <div class="mb-3">
                                <span class="info-label">📍 Lieu</span>
                                <span class="info-value"><?= htmlspecialchars($tournoi['lieu']) ?></span>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <span class="info-label">📅 Début</span>
                                    <span class="info-value"><?= date('d/m/Y', strtotime($tournoi['date_debut'])) ?></span>
                                    <small class="text-muted d-block"><?= date('H:i', strtotime($tournoi['date_debut'])) ?></small>
                                </div>
                                <div class="col-6">
                                    <span class="info-label">⏳ Limite d'inscription</span>
                                    <span class="info-value text-warning"><?= date('d/m/Y', strtotime($tournoi['date_limite'])) ?></span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <span class="info-label">🥋 Niveau requis</span>
                                <span class="info-value"><?= htmlspecialchars($tournoi['niveau_requis']) ?></span>
                            </div>

                            <div class="club-card mb-4 d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary">
                                    🏛️
                                </div>
                                <div>
                                    <span class="info-label mb-0" style="font-size: 0.7em;">Organisé par</span>
                                    <span class="fw-bold d-block text-white"><?= htmlspecialchars($tournoi['club_nom']) ?></span>
                                </div>
                            </div>

                            <div>
                                <?php if ($_SESSION['user_role'] === 'athlete'): ?>
                                    <?php if ($est_deja_inscrit): ?>
                                        <div class="btn-disabled-custom">
                                            ✅ Vous êtes inscrit
                                        </div>
                                    <?php else: ?>
                                        <a href="inscription_tournoi.php?id=<?= $tournoi['id'] ?>" class="btn-primary-custom w-100 d-block text-center text-decoration-none">
                                            S'inscrire maintenant
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-center p-3 border border-secondary border-opacity-25 rounded-3">
                                        <small class="text-muted fst-italic">Connectez-vous en tant qu'athlète pour participer.</small>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>