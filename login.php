<?php


require_once 'config.php';

$message = ""; // juste pour stocker les messages d'erreur ou de succès

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $querry = $pdo->prepare("SELECT * FROM utulisateurs WHERE email = :email");

    $querry->execute(['email' => $email]);
    $user = $querry->fetch();

    if ($user) {
        if (password_verify($mot_de_passe, $user['mot_de_passe'])) {

          //sa veut dire que ca marche
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['user_name'] = $user['nom'];
          $_SESSION['user_prenom'] = $user['prenom'];
          $_SESSION['user_role'] = $user['role'];       

          header("Location: dashboard.php");
          exit();


        }
        else {
            $message = "❌ Mot de passe incorrect.";
        }
    } else {
        $message = "❌ Aucun compte trouvé avec cet email.";    
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - ProArena</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">
</head>
<body>

<div class="full-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">

                <div class="glass-card fade-up">

                    <h2 class="text-center mb-4">Connexion</h2>

                    <?php if(!empty($message)): ?>
                        <div class="alert alert-danger">
                            <?= $message ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">

                        <div class="mb-3">
                            <label for="email_id" class="form-label">Email</label>
                            <input type="email" name="email" id="email_id" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label for="mdp_id" class="form-label">Mot de passe</label>
                            <input type="password" name="mot_de_passe" id="mdp_id" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100">
                            Se connecter
                        </button>

                    </form>

                    <p class="text-center mt-3 text-muted-custom">
                        Pas encore inscrit ?
                        <a href="inscription.php">Créer un compte</a>
                    </p>

                </div>

            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
