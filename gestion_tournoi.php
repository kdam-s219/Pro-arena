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
</head>
<body>
    <a href="dashboard.php">Retour au Dashboard</a>
    
    <h1>Gestion du tournoi : <?= htmlspecialchars($tournoi['titre']) ?></h1>

    <div style="background-color: #f0f0f0; padding: 15px; border: 1px solid #ccc;">
        <h3>Options du tournoi</h3>
        <a href="modifier_tournoi.php?id=<?= $id_tournoi ?>" style="font-weight: bold; color: blue;">
            Modifier les infos
        </a>
        <br><br>
        <a href="supprimer_tournoi.php?id=<?= $id_tournoi ?>" 
           style="color: red; font-weight: bold;"
           onclick="return confirm('Voulez-vous vraiment supprimer ce tournoi ?')">
            Supprimer définitivement
        </a>
    </div>

    <hr>

    <h3>Liste des participants inscrits</h3>
    
    <form method="GET" style="margin-bottom: 20px;">
        <input type="hidden" name="id" value="<?= $id_tournoi ?>">
        
        <input type="text" name="q" placeholder="Rechercher un nom ou email..." value="<?= htmlspecialchars($recherche) ?>" style="padding: 5px; width: 250px;">
        <button type="submit" style="padding: 5px;">Rechercher</button>
        
        <?php if (!empty($recherche)): ?>
            <a href="gestion_tournoi.php?id=<?= $id_tournoi ?>" style="margin-left: 10px; color: red;">Annuler la recherche</a>
        <?php endif; ?>
    </form>

    <p>Nombre de résultats : <strong><?php echo count($inscrits); ?></strong></p>

    <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #eee;">
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Action</th> 
        </tr>
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
                           onclick="return confirm('Retirer cet athlète ?')" 
                           style="color: red;">
                            Retirer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" align="center">Aucun participant trouvé.</td></tr>
        <?php endif; ?>
    </tbody>
    </table>
</body>
</html>