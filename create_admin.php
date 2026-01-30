<?php
require_once 'config.php';

$nom = "admin";
$prenom = "admin";
$email = "admin@gmail.com";
// On utilise le hachage pour la sécurité, comme dans tes autres scripts
$password = password_hash("admin123", PASSWORD_DEFAULT); 
$role = "admin";

$sql = "INSERT INTO utulisateurs (nom, prenom, email, mot_de_passe, role) 
        VALUES (:nom, :prenom, :email, :pass, :role)";

$stmt = $pdo->prepare($sql);
if ($stmt->execute(['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'pass' => $password, 'role' => $role])) {
    echo "✅ Compte Administrateur créé ! Identifiants : admin@proarena.com / admin123";
} else {
    echo "❌ Erreur lors de la création du compte.";
}
?>