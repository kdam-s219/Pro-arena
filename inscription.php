<?php

require_once 'config.php';

$message = ""; // juste pour stocker les messages d'erreur ou de succès

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //RECUPERATION DES DONNEES 
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $role = $_POST['role']; 
    //Pour le mot de passe , on va utuliser les password hash , aui permet de hacher le mdp pour le securiser
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    /*
    REQUETE D'INSERTION , Tu ne peux pas écrire du SQL "brut" au milieu de ton code PHP comme si c'était le même langage.
     Tu dois utiliser PHP pour envoyer ta commande SQL à la base de données. C'est exactement ce qu'on fait avec 
     cette ligne : $sql = "INSERT INTO utulisateurs...". Ici, $sql est juste une chaîne de caractères (du texte) 
     qui contient ton ordre SQL. C'est l'objet $pdo (que nous avons créé dans config.php) qui va prendre ce texte 
     et l'envoyer à MySQL pour qu'il soit exécuté 
    */
    try {
    $sql = "INSERT INTO utulisateurs (nom, prenom , email, mot_de_passe, role) VALUES (:nom, :prenom, :email, :mot_de_passe, :role)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nom' => $nom,
        'prenom' => $prenom,    
        'email' => $email,
        'mot_de_passe' => $mot_de_passe,    
        'role' => $role     
    ]);

    $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";

    } catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        // Ici, on sait que c'est l'email qui pose problème car c'est la seule colonne UNIQUE (23000 correspond a erreur dans l email sur sql)
        $message = "❌ Cet email est déjà utilisé. Veuillez en choisir un autre ou vous connecter.";
    } else {
        $message = "❌ Erreur technique : " . $e->getMessage();
    }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Inscription - Pro-Arena</title>
    </head>
    
    <body>
    <div class="form-container">
        <h2>S'inscrire</h2>
        <?php if($message) echo "<p>$message</p>"; ?>
        
        <form action="inscription.php" method="POST">
           


            <label for="nom_id">Nom :</label>
            <input type="text" name="nom" placeholder="Nom " id="nom_id" required>
            <br><br>

            <label for="prenom_id">Prénom :</label>
            <input type="text" name="prenom" placeholder="Prénom" id="prenom_id" required>
            <br><br>

            

            <label for="email_id">Email :</label>
            <input type="email" name="email" placeholder="Email" id="email_id" required>
            <br><br>
           
            <label for="mdp_id">Mot de passe :</label>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" id="mdp_id" required>
            <br><br>

            <label for="role_id">Vous êtes :</label>
            <select name="role" id="role_id">
                <option value="athlete">Athlète</option>
                <option value="organisateur">Organisateur</option>
                <option value ="club">Club</option>
            </select>

            <br><br>

            <button type="submit">Créer mon compte</button>
        </form>
        <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
    </div>
    </body>
</html>