<?php
require_once 'config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Action : Supprimer
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM utulisateurs WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: admin_users.php?msg=deleted");
        exit();
    }
}

$users = $pdo->query("SELECT * FROM utulisateurs ORDER BY date_d_inscription DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Utilisateurs | ProArena</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
        body { background: radial-gradient(circle at top right, #111827, #020617); font-family: 'Outfit', sans-serif; color: grey; min-height: 100vh; }
        
        /* Conteneur principal avec effet Glass */
        .glass-card { 
            background: rgba(255, 255, 255, 0.02); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255, 255, 255, 0.05); 
            border-radius: 24px; 
            padding: 30px; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* --- TABLEAU SOMBRE --- */
        .table-container {
            background: rgba(0, 0, 0, 0.4); /* Fond bien sombre pour le tableau */
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .table { color: #e2e8f0; margin-bottom: 0; }
        .table thead th { background: rgba(255, 255, 255, 0.02); border: none; padding: 20px; color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .table tbody td { padding: 20px; border-color: rgba(255, 255, 255, 0.03); vertical-align: middle; }
        
        /* Effet au survol de la ligne */
        .table tbody tr:hover { background: rgba(13, 110, 253, 0.03); }
        .text-muted { background: linear-gradient(to right, #ffffff, #cde1ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        .text-gradient { background: linear-gradient(to right, #3b82f6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        /* Bouton Supprimer Stylisé */
        .btn-delete { color: #f87171; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 50px; padding: 6px 16px; font-size: 0.85rem; transition: 0.3s; }
        .btn-delete:hover { background: #ef4444; color: white; box-shadow: 0 0 20px rgba(239, 68, 68, 0.4); border-color: transparent; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark px-4 py-4">
    <a href="admin_dashboard.php" class="navbar-brand fw-bold fs-3">Pro<span class="text-primary">Arena</span></a>
    <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-4" style="border-color: rgba(255,255,255,0.1);">← Retour</a>
</nav>

<div class="container py-4">
    <div class="mb-5">
        <h1 class="fw-bold display-5">Gestion <span class="text-gradient">Membres</span></h1>
        <p class="text-muted">Contrôlez les accès et la sécurité de la plateforme.</p>
    </div>

    <div class="glass-card">
        <div class="table-container">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Membre</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=<?= $u['prenom'].'+'.$u['nom'] ?>&background=0D6EFD&color=fff" class="rounded-circle shadow-sm" width="38">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?></div>
                                        <div class="small text-muted">ID: #<?= $u['id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span class="badge rounded-pill <?= $u['role'] === 'admin' ? 'bg-danger' : ($u['role'] === 'club' ? 'bg-success' : 'bg-primary') ?> bg-opacity-10 <?= $u['role'] === 'admin' ? 'text-danger' : ($u['role'] === 'club' ? 'text-success' : 'text-primary') ?> px-3 py-2 border border-current" style="border-width: 1px; border-style: solid; border-color: inherit;">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="admin_users.php?delete=<?= $u['id'] ?>" class="btn btn-delete" onclick="return confirm('Bannir définitivement ?')">Supprimer</a>
                                <?php else: ?>
                                    <span class="small text-muted px-3">Propriétaire</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>