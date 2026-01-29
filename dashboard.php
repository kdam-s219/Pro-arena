<?php

require_once 'config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Récupérer les inscriptions de l'utilisateur connecté
$sql_mes_inscr = "SELECT t.* FROM tournois t 
                  JOIN inscription i ON t.id = i.id_competition 
                  WHERE i.id_utulisateur = :user_id";
$stmt = $pdo->prepare($sql_mes_inscr);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$mes_inscriptions = $stmt->fetchAll();


// --- AJOUT DE LA LOGIQUE SQL ICI ---
// On récupère les tournois et le nom du club créateur via une jointure
$sql = "SELECT t.*, u.nom
        FROM tournois t 
        JOIN utulisateurs u ON t.club_id = u.id 
        ORDER BY t.date_debut ASC";

$requete = $pdo->query($sql);
$tous_les_tournois = $requete->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - ProArena</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-custom px-4">
    <span class="navbar-brand text-white fw-bold">ProArena</span>

    <div class="ms-auto d-flex align-items-center gap-3">
        <span class="text-muted-custom">
            <?= $_SESSION['user_prenom'] . " " . $_SESSION['user_name']; ?>
        </span>
        <a href="logout.php" class="btn btn-outline-custom btn-sm">Déconnexion</a>
    </div>
</nav>

<div class="container my-4">

    <!-- Welcome card -->
    <div class="glass-card mb-4">
        <h3>Bienvenue 👋</h3>
        <p class="text-muted-custom mb-0">
            Vous êtes connecté en tant que : <strong><?= $_SESSION['user_role']; ?></strong>
        </p>
    </div>

    <!-- Club menu -->
    <?php if ($_SESSION['user_role'] === 'club'): ?>
        <div class="glass-card mb-4">
            <h4>Menu Club</h4>
            <a href="creer_tournoi.php" class="btn btn-primary-custom mt-2">
                ➕ Créer un nouveau tournoi
            </a>
        </div>
    <?php endif; ?>

    <!-- Alerts -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 'inscrit'): ?>
        <div class="alert alert-success">
            ✅ Félicitations ! Votre inscription au tournoi a été enregistrée avec succès.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'echec'): ?>
        <div class="alert alert-danger">
            ❌ Une erreur est survenue lors de l'inscription. Veuillez réessayer.
        </div>
    <?php endif; ?>

    <!-- Tournois -->
    <div class="glass-card mb-4">
        <h4 class="mb-3">Tournois disponibles</h4>

        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Lieu</th>
                        <th>Date</th>
                        <th>Organisateur</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tous_les_tournois as $tournoi): ?>
                    <tr>
                        <td><?= $tournoi['titre']; ?></td>
                        <td><?= $tournoi['lieu']; ?></td>
                        <td><?= $tournoi['date_debut']; ?></td>
                        <td><?= $tournoi['nom']; ?></td>
                        <td>
                            <?php if ($_SESSION['user_role'] === 'athlete'): ?>
                                <a class="btn btn-primary-custom btn-sm"
                                   href="inscription_tournoi.php?id=<?= $tournoi['id'] ?>">
                                   S'inscrire
                                </a>

                            <?php elseif ($_SESSION['user_role'] === 'club' && $tournoi['club_id'] == $_SESSION['user_id']): ?>
                                <a class="btn btn-outline-custom btn-sm"
                                   href="gestion_tournoi.php?id=<?= $tournoi['id'] ?>">
                                   Gérer
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Athlete registrations -->
    <?php if ($_SESSION['user_role'] === 'athlete'): ?>
        <div class="glass-card mb-4">
            <h4 class="mb-3">Mes inscriptions</h4>

            <?php if (count($mes_inscriptions) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tournoi</th>
                                <th>Lieu</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($mes_inscriptions as $insc): ?>
                            <tr>
                                <td><?= htmlspecialchars($insc['titre']) ?></td>
                                <td><?= htmlspecialchars($insc['lieu']) ?></td>
                                <td><?= htmlspecialchars($insc['date_debut']) ?></td>
                                <td>
                                    <a class="btn btn-outline-danger btn-sm"
                                       href="annuler_inscription.php?id=<?= $insc['id'] ?>"
                                       onclick="return confirm('Voulez-vous vraiment vous désinscrire ?')">
                                       Se désinscrire
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted-custom">Vous n'êtes inscrit à aucun tournoi pour le moment.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
