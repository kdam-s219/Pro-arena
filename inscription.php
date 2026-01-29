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
    <title>Inscription - ProArena</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">

    <style>
        /* Role cards */
        .role-card {
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 14px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s ease;
            background: rgba(255,255,255,0.04);
        }

        .role-card:hover {
            transform: translateY(-3px);
            border-color: var(--accent);
        }

        .role-card.active {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(34,197,94,0.4);
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="full-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="glass-card fade-up">

                    <h2 class="text-center mb-4">Créer un compte</h2>

                    <?php if(!empty($message)): ?>
                        <div class="alert alert-danger"><?= $message ?></div>
                    <?php endif; ?>

                    <form action="inscription.php" method="POST">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <!-- Password with toggle -->
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <div class="password-wrapper">
                                <input type="password" name="mot_de_passe" id="passwordInput" class="form-control" required>
                                <span class="toggle-password" onclick="togglePassword()">Afficher</span>
                            </div>
                        </div>

                        <!-- Role cards -->
                        <div class="mb-4">
                            <label class="form-label mb-2">Vous êtes</label>

                            <input type="hidden" name="role" id="roleInput" value="athlete">

                            <div class="row g-3">
                                <div class="col-4">
                                    <div class="role-card active" onclick="selectRole('athlete', this)">
                                        🥋<br>Athlète
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div class="role-card" onclick="selectRole('organisateur', this)">
                                        🏆<br>Organisateur
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div class="role-card" onclick="selectRole('club', this)">
                                        🏟️<br>Club
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100">
                            Créer mon compte
                        </button>

                    </form>

                    <p class="text-center mt-3 text-muted-custom">
                        Déjà inscrit ? <a href="login.php">Se connecter</a>
                    </p>

                </div>

            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>

<script>
    function togglePassword() {
        const input = document.getElementById("passwordInput");
        const toggle = document.querySelector(".toggle-password");

        if (input.type === "password") {
            input.type = "text";
            toggle.innerText = "Masquer";
        } else {
            input.type = "password";
            toggle.innerText = "Afficher";
        }
    }

    function selectRole(role, element) {
        document.getElementById("roleInput").value = role;

        document.querySelectorAll(".role-card").forEach(card => {
            card.classList.remove("active");
        });

        element.classList.add("active");
    }
</script>

</body>
</html>
