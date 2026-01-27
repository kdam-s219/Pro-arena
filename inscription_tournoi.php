<?php
require_once 'config.php';

// 1. SÉCURITÉ : Seul un athlète peut s'inscrire
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'athlete') {
    header("Location: dashboard.php");
    exit();
}

// 2. RÉCUPÉRATION DE L'ID DU TOURNOI (via l'URL ?id=...)
if (isset($_GET['id'])) {
    $id_competition = $_GET['id']; // On garde ta notation
    $id_utulisateur = $_SESSION['user_id']; // On garde ta notation

    try {
        // 3. INSERTION DANS LA TABLE
       
        $sql = "INSERT INTO inscription (id_utulisateur, id_competition) 
                VALUES (:id_utulisateur, :id_competition)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_utulisateur' => $id_utulisateur,
            'id_competition' => $id_competition
        ]);

        // 4. RETOUR AU DASHBOARD
        header("Location: dashboard.php?success=inscrit");
        exit();

    } catch (PDOException $e) {
        // Erreur (ex: déjà inscrit si tu as mis une contrainte UNIQUE)
        header("Location: dashboard.php?error=echec");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}