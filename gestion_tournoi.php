<?php
require_once 'config.php';

// Sécurité : Seul le club peut voir cette page
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'club') {
    header("Location: dashboard.php");
    exit();
}

$id_tournoi = $_GET['id'];

// Requête pour récupérer les informations des athlètes inscrits
$sql = "SELECT u.id, u.nom, u.prenom, u.email 
        FROM inscription i
        JOIN utulisateurs u ON i.id_utulisateur = u.id
        WHERE i.id_competition = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id_tournoi]);
$inscrits = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des inscrits - Pro-Arena</title>
</head>
<body>
    <h1>Liste des participants inscrits</h1>
    <a href="dashboard.php">Retour au Dashboard</a>
    <br><br>
    
    <h3>Nombre de participants inscrits : <?php echo count($inscrits); ?></h3>
    <br>

    <table border="1">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Action</th> </tr>
    </thead>
    <tbody>
        <?php if (count($inscrits) > 0): ?>
            <?php foreach ($inscrits as $athlete): ?>
                <tr>
                    <td><?= htmlspecialchars($athlete['nom']) ?></td>
                    <td><?= htmlspecialchars($athlete['prenom']) ?></td>
                    <td><?= htmlspecialchars($athlete['email']) ?></td>
                    <td>
                        <a href="retirer_athlete.php?athlete_id=<?= $athlete['id'] ?>&tournoi_id=<?= $id_tournoi ?>" 
                           onclick="return confirm('Voulez-vous vraiment retirer cet athlète ?')" 
                           style="color: red; font-weight: bold;">
                           Retirer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">Aucun inscrit pour le moment.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</body>
</html>