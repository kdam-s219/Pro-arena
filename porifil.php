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

    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Design System -->
    <link rel="stylesheet" href="css/design-system.css">
</head>

<body class="bg-light">

<div class="container py-5">

    <!-- Retour -->
    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-outline-custom btn-sm">
            ← Retour au Dashboard
        </a>
    </div>

    <!-- Profil Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <div class="glass-card shadow-soft">
                <div class="card-body p-4">

                    <h3 class="mb-3">🏅 Mon Profil Sportif</h3>

                    <div class="mb-3">
                        <strong>Utilisateur :</strong>
                        <?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?>
                    </div>

                    <div class="mb-3">
                        <strong>Statut :</strong>
                        <span class="badge bg-secondary">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <?= $message ?>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <form method="POST">

                        <h5 class="mb-4">🥋 Disciplines & niveaux</h5>

                        <?php foreach ($grades as $nom_sport => $liste_ceintures):

                            $est_pratique = false;
                            $niveau_actuel = "Blanche";

                            foreach ($sports_deja_inscrits as $ligne) {
                                if (strpos($ligne, $nom_sport) !== false) {
                                    $est_pratique = true;
                                    preg_match('/\((.*?)\)/', $ligne, $match);
                                    $niveau_actuel = $match[1] ?? "Blanche";
                                }
                            }
                        ?>

                            <div class="border rounded p-3 mb-3 shadow-soft">

                                <div class="form-check mb-3">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="sports[]"
                                        value="<?= $nom_sport ?>"
                                        id="sport_<?= $nom_sport ?>"
                                        <?= $est_pratique ? 'checked' : '' ?>
                                    >

                                    <label class="form-check-label fw-bold" for="sport_<?= $nom_sport ?>">
                                        <?= $nom_sport ?>
                                    </label>
                                </div>

                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label mb-0">
                                            Niveau
                                        </label>
                                    </div>

                                    <div class="col-md-8">
                                        <select
                                            name="niveau_<?= $nom_sport ?>"
                                            class="form-select"
                                        >
                                            <?php foreach ($liste_ceintures as $c): ?>
                                                <option
                                                    value="<?= $c ?>"
                                                    <?= ($niveau_actuel === $c) ? 'selected' : '' ?>
                                                >
                                                    <?= $c ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                            </div>

                        <?php endforeach; ?>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary-custom btn-lg">
                                💾 Enregistrer mon profil
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

</body>
</html>
