<?php

require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard - Pro-Arena</title>
    </head>

    <body>
        <h1>Bienvenue sur votre dashboard, <?php echo $_SESSION['user_prenom'] . " " . $_SESSION['user_name']; ?>!</h1>
        <p>Vous êtes connecté en tant que : <?php echo $_SESSION['user_role']; ?></strong></p>


        <br><br><br>

        

        <p><a href="logout.php">Se déconnecter</a></p>