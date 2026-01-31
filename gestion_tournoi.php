<?php
require_once 'config.php';

// Sécurité : Seul le club peut voir cette page
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] == 'athlete') {
    header("Location: dashboard.php");
    exit();
}

$id_tournoi = $_GET['id'];
$recherche = $_GET['q'] ?? ''; // On récupère le mot-clé de recherche s'il existe

// 1. Récupérer les infos du tournoi
$sql_info = "SELECT titre FROM tournois WHERE id = :id";
$stmt_info = $pdo->prepare($sql_info);
$stmt_info->execute(['id' => $id_tournoi]);
$tournoi = $stmt_info->fetch();

// 2. Requête pour les participants (AVEC PARAMÈTRES UNIQUES)
$sql = "SELECT u.id, u.nom, u.prenom, u.email 
        FROM inscription i
        JOIN utulisateurs u ON i.id_utulisateur = u.id
        WHERE i.id_competition = :id";

$params = ['id' => $id_tournoi];

if (!empty($recherche)) {
    // On utilise :q1, :q2, :q3 pour être sûr que PDO ne se trompe pas
    $sql .= " AND (u.nom LIKE :q1 OR u.prenom LIKE :q2 OR u.email LIKE :q3)";
    $params['q1'] = "%$recherche%";
    $params['q2'] = "%$recherche%";
    $params['q3'] = "%$recherche%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params); // C'est ici que l'erreur se produisait
$inscrits = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion - <?= htmlspecialchars($tournoi['titre']) ?></title>

    <!-- CSS LOCAL UNIQUEMENT -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">
</head>

<body>

<div class="container py-5" style="max-width: 1100px;">

    <!-- Retour -->
    <a href="dashboard.php" class="text-muted text-decoration-none">
        ← Retour au dashboard
    </a>

    <!-- Titre -->
    <h1 class="mt-3 mb-4">
        Gestion du tournoi : <?= htmlspecialchars($tournoi['titre']) ?>
    </h1>

    <!-- Options tournoi -->
    <div class="glass-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">

            <h5 class="mb-0">⚙️ Options du tournoi</h5>

            <div class="d-flex gap-2">
                <a href="modifier_tournoi.php?id=<?= $id_tournoi ?>"
                   class="btn btn-outline-primary">
                    ✏️ Modifier les infos
                </a>

                <a href="supprimer_tournoi.php?id=<?= $id_tournoi ?>"
                   class="btn btn-outline-danger"
                   onclick="return confirm('Voulez-vous vraiment supprimer ce tournoi ?')">
                    🗑️ Supprimer
                </a>
            </div>

        </div>
    </div>

    <!-- Participants -->
    <div class="glass-card">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">👥 Participants inscrits</h5>
            <span class="badge bg-primary">
                <?= count($inscrits) ?> inscrits
            </span>
        </div>

        <!-- Recherche -->
        <form method="GET" class="row g-2 mb-4">
            <input type="hidden" name="id" value="<?= $id_tournoi ?>">

            <div class="col-md-6">
                <input type="text"
                       name="q"
                       class="form-control"
                       placeholder="Rechercher un nom ou email..."
                       value="<?= htmlspecialchars($recherche) ?>">
            </div>

            <div class="col-md-auto">
                <button class="btn btn-primary-custom">
                    🔍 Rechercher
                </button>
            </div>

            <?php if (!empty($recherche)): ?>
                <div class="col-md-auto">
                    <a href="gestion_tournoi.php?id=<?= $id_tournoi ?>"
                       class="btn btn-outline-secondary">
                        Annuler
                    </a>
                </div>
            <?php endif; ?>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($inscrits) > 0): ?>
                    <?php foreach ($inscrits as $athlete): ?>
                        <tr>
                            <td><?= htmlspecialchars($athlete['nom']) ?></td>
                            <td><?= htmlspecialchars($athlete['prenom']) ?></td>
                            <td><?= htmlspecialchars($athlete['email']) ?></td>
                            <td class="text-center">
                                <a href="retirer_athlete.php?athlete_id=<?= $athlete['id'] ?>&tournoi_id=<?= $id_tournoi ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Retirer cet athlète ?')">
                                    ❌ Retirer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Aucun participant trouvé.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
