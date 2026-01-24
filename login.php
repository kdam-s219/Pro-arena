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
    <title>Connexion - Pro-Arena</title>
</head>
<body>
    <h1>Se connecter</h1>

    <?php if(!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>

    <form action="login.php" method="POST">
        <label for="email_id">Email :</label>
        <input type="email" name="email" id="email_id" required>
        <br><br>

        <label for="mdp_id">Mot de passe :</label>
        <input type="password" name="mot_de_passe" id="mdp_id" required>
        <br><br>

        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore inscrit ? <a href="inscription.php">S'inscrire</a></p>
</body>
</html>