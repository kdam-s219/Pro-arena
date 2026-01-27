<?php
session_start(); // On récupère la session en cours
session_unset(); // On vide toutes les variables ($_SESSION['user_id'], etc.)
session_destroy(); // On détruit le fichier de session sur le serveur

// On redirige vers la page de connexion
header("Location: login.php");
exit();
?>