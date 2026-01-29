<?php
require_once 'config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] == 'club' ) {
    header("Location: dashboard.php");
    exit();
}

// Sécurité : Seul un utilisateur connecté peut accéder à son profil
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Définition des sports et de leurs grades réels
$grades = [
    "Judo" => ["Blanche", "Jaune", "Orange", "Verte", "Bleue", "Marron", "Noire"],
    "Karate" => ["Blanche", "Jaune", "Orange", "Verte", "Bleue", "Marron", "Noire"],
    "Jujitsu" => ["Blanche", "Bleue", "Violette", "Marron", "Noire"] // Grades officiels JJB
];

// --- LOGIQUE DE SAUVEGARDE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $choix_final = [];

    if (isset($_POST['sports']) && is_array($_POST['sports'])) {
        foreach ($_POST['sports'] as $sport) {
            // On récupère la ceinture choisie pour ce sport spécifique
            $ceinture = $_POST['niveau_' . $sport] ?? "Blanche";
            $choix_final[] = "$sport ($ceinture)";
        }
    }

    // On transforme le tableau en chaîne de caractères pour la base de données
    $sports_to_save = implode(", ", $choix_final);

    $update = $pdo->prepare("UPDATE utulisateurs SET sport_prefere = :sport WHERE id = :id");
    if ($update->execute(['sport' => $sports_to_save, 'id' => $user_id])) {
        $message = "Profil sportif mis à jour avec succès !";
    }
}

// --- RÉCUPÉRATION DES DONNÉES ---
$query = $pdo->prepare("SELECT nom, prenom, role, sport_prefere FROM utulisateurs WHERE id = :id");
$query->execute(['id' => $user_id]);
$user = $query->fetch();

// Préparation des données pour l'affichage des cases cochées
$sports_deja_inscrits = explode(", ", $user['sport_prefere']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil Sportif - Pro-Arena</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
        .profil-card { border: 1px solid #ddd; padding: 20px; max-width: 500px; border-radius: 8px; box-shadow: 2px 2px 10px rgba(0,0,0,0.1); }
        .sport-item { background: #f9f9f9; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 5px solid #007bff; }
        .btn-save { background-color: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-save:hover { background-color: #218838; }
        select { margin-left: 10px; padding: 5px; }
    </style>
</head>
<body>

    <a href="dashboard.php">← Retour au Dashboard</a>
    
    <div class="profil-card">
        <h1>Mon Profil Sportif</h1>
        <p><strong>Utilisateur :</strong> <?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?></p>
        <p><strong>Statut :</strong> <?= htmlspecialchars($user['role']) ?></p>

        <?php if ($message): ?>
            <p style="color: green; background: #e9f7ef; padding: 10px; border-radius: 5px;"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST">
            <h3>Mes Disciplines & Niveaux</h3>

            <?php foreach ($grades as $nom_sport => $liste_ceintures): 
                // On vérifie si ce sport est déjà dans la base de données
                $est_pratique = false;
                $niveau_actuel = "Blanche";

                foreach ($sports_deja_inscrits as $ligne) {
                    if (strpos($ligne, $nom_sport) !== false) {
                        $est_pratique = true;
                        // On extrait le niveau entre parenthèses
                        preg_match('/\((.*?)\)/', $ligne, $match);
                        $niveau_actuel = $match[1] ?? "Blanche";
                    }
                }
            ?>
                <div class="sport-item">
                    <input type="checkbox" name="sports[]" value="<?= $nom_sport ?>" <?= $est_pratique ? 'checked' : '' ?>>
                    <strong><?= $nom_sport ?></strong>
                    <br><br>
                    <label>Ceinture :</label>
                    <select name="niveau_<?= $nom_sport ?>">
                        <?php foreach ($liste_ceintures as $c): ?>
                            <option value="<?= $c ?>" <?= ($niveau_actuel == $c) ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endforeach; ?>

            <br>
            <button type="submit" class="btn-save">Enregistrer mon profil</button>
        </form>
    </div>

</body>
</html>