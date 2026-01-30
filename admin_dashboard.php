<?php
require_once 'config.php';

// Debugging : affiche les erreurs s'il y en a
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Sécurité : Vérification stricte du rôle
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

try {
    // On utilise bien 'utulisateurs' comme dans tes fichiers
    $total_users = $pdo->query("SELECT COUNT(*) FROM utulisateurs")->fetchColumn() ?: 0;
    $total_tournois = $pdo->query("SELECT COUNT(*) FROM tournois")->fetchColumn() ?: 0;
    $total_inscriptions = $pdo->query("SELECT COUNT(*) FROM inscription")->fetchColumn() ?: 0;
} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - ProArena</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">
</head>
<body style="background-color: #0b0e14; color: white;">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2>Panel Administrateur 🛡️</h2>
        <a href="dashboard.php" class="btn btn-outline-light">Retour au site</a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="glass-card text-center">
                <h3 class="text-muted-custom">Membres</h3>
                <h1 class="display-4 fw-bold text-primary"><?= $total_users ?></h1>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card text-center">
                <h3 class="text-muted-custom">Tournois</h3>
                <h1 class="display-4 fw-bold text-success"><?= $total_tournois ?></h1>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card text-center">
                <h3 class="text-muted-custom">Inscriptions</h3>
                <h1 class="display-4 fw-bold text-info"><?= $total_inscriptions ?></h1>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="glass-card">
            <h4>Actions Rapides</h4>
            <hr class="opacity-25">
            <div class="d-flex gap-3">
                <a href="admin_users.php" class="btn btn-primary-custom">Gérer les utilisateurs</a>
                <a href="admin_tournois.php" class="btn btn-outline-custom">Modérer les tournois</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>