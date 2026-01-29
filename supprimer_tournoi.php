<?php
require_once 'config.php';

// Sécurité : Seul le club peut supprimer ses tournois
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] == 'athlete') {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_tournoi = $_GET['id'];

    try {
        // 1. On supprime d'abord les inscriptions liées au tournoi pour éviter les erreurs SQL
        $sql_inscriptions = "DELETE FROM inscription WHERE id_competition = :id";
        $stmt1 = $pdo->prepare($sql_inscriptions);
        $stmt1->execute(['id' => $id_tournoi]);

        // 2. On supprime le tournoi lui-même
        $sql_tournoi = "DELETE FROM tournois WHERE id = :id AND club_id = :club_id";
        $stmt2 = $pdo->prepare($sql_tournoi);
        $stmt2->execute([
            'id' => $id_tournoi,
            'club_id' => $_SESSION['user_id'] // Sécurité : on vérifie que c'est bien SON tournoi
        ]);

        header("Location: dashboard.php?msg=tournoi_supprime");
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
}
?>