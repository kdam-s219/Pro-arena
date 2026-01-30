<?php
require_once 'config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tournois WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: admin_tournois.php?msg=deleted");
    exit();
}

$sql = "SELECT t.*, u.nom as club_nom FROM tournois t JOIN utulisateurs u ON t.club_id = u.id ORDER BY t.date_debut ASC";
$tournois = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Tournois | ProArena</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
        body { background: radial-gradient(circle at top right, #111827, #020617); font-family: 'Outfit', sans-serif; color: white; min-height: 100vh; }
        
        /* --- NOUVEAU STYLE DARK GLASS --- */
        .glass-card { 
            background: rgba(20, 20, 20, 0.4); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 24px; 
            padding: 30px; 
        }

        .table-container {
            background: rgba(10, 10, 10, 0.5); 
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table { color: #e2e8f0; margin-bottom: 0; }
        .table thead th { background: rgba(20, 20, 20, 0.8); border: none; padding: 20px; color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; }
        .table tbody td { padding: 20px; border-color: rgba(255, 255, 255, 0.1); vertical-align: middle; }
        
        /* Effet de survol sombre (vert nuit) */
        .table tbody tr:hover { background: rgba(6, 78, 59, 0.3); }

        .text-gradient { background: linear-gradient(to right, #10b981, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .badge-sport { background: rgba(13, 110, 253, 0.1); color: #60a5fa; border: 1px solid rgba(96, 165, 250, 0.2); }
        .btn-delete { color: #f87171; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 50px; padding: 6px 16px; transition: 0.3s; }
        .btn-delete:hover { background: #ef4444; color: white; box-shadow: 0 0 20px rgba(239, 68, 68, 0.4); }
    </style>
</head>
<body>

<nav class="navbar navbar-dark px-4 py-4">
    <a href="admin_dashboard.php" class="navbar-brand fw-bold fs-3">Pro<span class="text-primary">Arena</span></a>
    <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-4" style="border-color: rgba(255,255,255,0.1);">← Retour</a>
</nav>

<div class="container py-4">
    <div class="mb-5">
        <h1 class="fw-bold display-5">Modération <span class="text-gradient">Tournois</span></h1>
        <p class="text-muted">Surveillez tous les événements sportifs créés.</p>
    </div>

    <div class="glass-card">
        <div class="table-container">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tournoi</th>
                            <th>Organisateur</th>
                            <th>Sport</th>
                            <th>Lieu</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($tournois) > 0): ?>
                            <?php foreach ($tournois as $t): ?>
                            <tr>
                                <td class="fw-bold text-black"><?= htmlspecialchars($t['titre']) ?></td>
                                <td><?= htmlspecialchars($t['club_nom']) ?></td>
                                <td><span class="badge badge-sport rounded-pill px-3 py-2"><?= htmlspecialchars($t['sport']) ?></span></td>
                                <td class="text-muted small"><?= htmlspecialchars($t['lieu']) ?></td>
                                <td class="text-end">
                                    <a href="admin_tournois.php?delete=<?= $t['id'] ?>" class="btn btn-delete" onclick="return confirm('Supprimer définitivement ?')">Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted italic">Aucun tournoi n'a été créé pour le moment.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>