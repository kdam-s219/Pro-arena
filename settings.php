<?php
require_once 'config.php';

// Sécurité
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$error = "";

// 1. TRAITEMENT : Modifier les infos (Nom, Prénom, Email)
if (isset($_POST['update_info'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    $sql = "UPDATE utulisateurs SET nom = :nom, prenom = :prenom, email = :email WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute(['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'id' => $user_id])) {
        // Mise à jour de la session pour l'affichage immédiat
        $_SESSION['user_name'] = $nom;
        $_SESSION['user_prenom'] = $prenom;
        $message = "✅ Informations mises à jour avec succès.";
    } else {
        $error = "❌ Erreur lors de la mise à jour.";
    }
}

// 2. TRAITEMENT : Changer le mot de passe
if (isset($_POST['update_password'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // On récupère le mot de passe actuel crypté en base
    $stmt = $pdo->prepare("SELECT mot_de_passe FROM utulisateurs WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();

    // Vérification
    if (!password_verify($old_pass, $user['mot_de_passe'])) {
        $error = "❌ L'ancien mot de passe est incorrect.";
    } elseif ($new_pass !== $confirm_pass) {
        $error = "❌ Les nouveaux mots de passe ne correspondent pas.";
    } elseif (strlen($new_pass) < 4) { // Règle simple
        $error = "❌ Le mot de passe est trop court.";
    } else {
        // Hashage et Update
        $new_hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
        $update_pass = $pdo->prepare("UPDATE utulisateurs SET mot_de_passe = :pass WHERE id = :id");
        $update_pass->execute(['pass' => $new_hashed_password, 'id' => $user_id]);
        $message = "🔒 Mot de passe modifié avec succès !";
    }
}

// Récupération des infos actuelles pour pré-remplir le formulaire
$stmt = $pdo->prepare("SELECT * FROM utulisateurs WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$currentUser = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paramètres - ProArena</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css"> </head>
<body style="background-color: #0b0e14; color: white;">

    <div class="container my-5">
        <a href="dashboard.php" class="btn btn-outline-light mb-4">← Retour au Dashboard</a>
        
        <h2 class="mb-4">⚙️ Paramètres du compte</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="p-4" style="background: rgba(255,255,255,0.05); border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                    <h4 class="mb-3">Mes informations</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($currentUser['nom']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($currentUser['prenom']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($currentUser['email']) ?>" required>
                        </div>
                        <button type="submit" name="update_info" class="btn btn-primary w-100">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="p-4" style="background: rgba(255,255,255,0.05); border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                    <h4 class="mb-3">Sécurité</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Ancien mot de passe</label>
                            <input type="password" name="old_password" class="form-control" required>
                        </div>
                        <hr class="border-secondary">
                        <div class="mb-3">
                            <label>Nouveau mot de passe</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Confirmer le nouveau mot de passe</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" name="update_password" class="btn btn-warning w-100">Changer le mot de passe</button>
                    </form>
                </div>
            </div>
            
             <div class="col-12 mt-2">
                 <div class="p-3 border border-danger rounded">
                    <h5 class="text-danger">Zone de danger</h5>
                    <p class="text-danger">La suppression de votre compte est irréversible.</p>
                    <a href="supprimer_compte.php" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?')" class="btn btn-outline-danger btn-sm">Supprimer mon compte</a>
                 </div>
            </div>

        </div>
    </div>

</body>
</html>