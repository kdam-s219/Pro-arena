<?php
require_once 'config.php';

// Sécurité : Vérifier si l'utilisateur est connecté et est un athlète
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'athlete') {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_tournoi = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Requête pour supprimer l'inscription spécifique
    $sql = "DELETE FROM inscription WHERE id_utulisateur = :uid AND id_competition = :cid";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute(['uid' => $user_id, 'cid' => $id_tournoi])) {
        header("Location: dashboard.php?success=annule");
    } else {
        header("Location: dashboard.php?error=erreur_annulation");
    }
    exit();
}