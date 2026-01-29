<?php
require_once 'config.php';

// 1. Sécurité : Seul le club peut faire ça
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'club') {
    header("Location: dashboard.php");
    exit();
}

// 2. Vérifier si on a bien reçu les IDs
if (isset($_GET['athlete_id']) && isset($_GET['tournoi_id'])) {
    $athlete_id = $_GET['athlete_id'];
    $tournoi_id = $_GET['tournoi_id'];

    // 3. Suppression de la ligne d'inscription
    $sql = "DELETE FROM inscription WHERE id_utulisateur = :uid AND id_competition = :cid";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute(['uid' => $athlete_id, 'cid' => $tournoi_id])) {
        // Redirection vers la page de gestion avec un message de succès
        header("Location: gestion_tournoi.php?id=" . $tournoi_id . "&status=retire");
    } else {
        header("Location: gestion_tournoi.php?id=" . $tournoi_id . "&status=erreur");
    }
    exit();
}