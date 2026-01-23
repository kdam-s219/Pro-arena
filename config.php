<?php
// --- PARTIE 1 : GESTION DES SESSIONS ---
// Cette fonction doit être appelée AVANT tout code HTML.
// Elle permet au serveur de créer un cookie de session sur le navigateur de l'utilisateur.
// C'est ce qui permet de savoir si l'utilisateur est un "Athlète" ou un "Organisateur" sur toutes les pages.
session_start();

// --- PARTIE 2 : CONFIGURATION DE LA BASE ---
$host = 'localhost';          // Ton serveur local (XAMPP)
$dbname = 'proarena_gestion'; // Le nom exact de ta base de données
$user = 'root';               // Utilisateur par défaut de XAMPP
$pass = '';                   // Mot de passe par défaut de XAMPP (vide)

try {
    // --- PARTIE 3 : LA CONNEXION PDO ---
    // PDO (PHP Data Objects) est une interface sécurisée pour interagir avec MySQL.
    // On précise l'encodage 'utf8' pour bien gérer les accents (français/arabe).
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

    // --- PARTIE 4 : SÉCURITÉ ET ERREURS ---
    // On demande à PHP de lancer une "Exception" (alerte) s'il y a une erreur dans tes futures requêtes SQL.
    // C'est indispensable pour déboguer ton projet à l'ENSA.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Désactiver l'émulation des requêtes préparées pour une sécurité maximale contre les injections SQL.
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // Si la connexion échoue (ex: MySQL est éteint sur XAMPP), on arrête tout et on affiche l'erreur.
    die("Erreur de connexion : " . $e->getMessage());
}
?>