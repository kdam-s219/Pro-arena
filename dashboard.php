<?php

require_once 'config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Récupérer les inscriptions de l'utilisateur connecté
$sql_mes_inscr = "SELECT t.* FROM tournois t 
                  JOIN inscription i ON t.id = i.id_competition 
                  WHERE i.id_utulisateur = :user_id";
$stmt = $pdo->prepare($sql_mes_inscr);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$mes_inscriptions = $stmt->fetchAll();


// --- AJOUT DE LA LOGIQUE SQL ICI ---
// On récupère les tournois et le nom du club créateur via une jointure
$sql = "SELECT t.*, u.nom
        FROM tournois t 
        JOIN utulisateurs u ON t.club_id = u.id 
        ORDER BY t.date_debut ASC";

$requete = $pdo->query($sql);
$tous_les_tournois = $requete->fetchAll();

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


        <br>

        

     

    


<?php if ($_SESSION['user_role'] === 'club'): ?>
    <hr>
    <h3>Menu Club</h3>
    <ul>
        <li>
            <a href="creer_tournoi.php">Créer un nouveau tournoi</a>
        </li>
    </ul>
    <hr>
<?php endif; ?> 




<hr>
<h2>Liste des Tournois Disponibles</h2>


<table border="1">
    <thead>
        <tr>
            <th>Titre</th>
            <th>Lieu</th>
            <th>Date</th>
            <th>Organisateur</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tous_les_tournois as $tournoi): ?>
            <tr>
                <td><?php echo $tournoi['titre']; ?></td>
                <td><?php echo $tournoi['lieu']; ?></td>
                <td><?php echo $tournoi['date_debut']; ?></td>
                <td><?php echo $tournoi['nom']; ?></td>
                <td>
                   <?php if ($_SESSION['user_role'] === 'athlete'): ?>
                   <a href="inscription_tournoi.php?id=<?= $tournoi['id'] ?>">S'inscrire</a>
                   <?php elseif ($_SESSION['user_role'] === 'club' && $tournoi['club_id'] == $_SESSION['user_id']): ?>
                   <a href="gestion_tournoi.php?id=<?= $tournoi['id'] ?>" style="color: orange;">Gérer les inscrits</a>
                   <?php else: ?>
                     --
                   <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php if (isset($_GET['success']) && $_GET['success'] == 'inscrit'): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;">
        ✅ Félicitations ! Votre inscription au tournoi a été enregistrée avec succès.
    </div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 'echec'): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;">
        ❌ Une erreur est survenue lors de l'inscription. Veuillez réessayer.
    </div>
<?php endif; ?>

<?php if ($_SESSION['user_role'] === 'athlete'): ?>
    <h2>Mes Inscriptions</h2>
    <?php if (count($mes_inscriptions) > 0): ?>
        <table border="1">
        <thead>
                                 <tr>
                         <th>Tournoi</th>
                           <th>Lieu</th>
                      <th>Date</th>
                          <th>Action</th> </tr>
        </thead>
    <tbody>
                 <?php foreach ($mes_inscriptions as $insc): ?>
                        <tr>
                        <td><?= htmlspecialchars($insc['titre']) ?></td>
                        <td><?= htmlspecialchars($insc['lieu']) ?></td>
                        <td><?= htmlspecialchars($insc['date_debut']) ?></td>
                            <td>
                        <a href="annuler_inscription.php?id=<?= $insc['id'] ?>" 
                            onclick="return confirm('Voulez-vous vraiment vous désinscrire ?')" 
                            style="color: red;">
                            Se désinscrire
                        </a>
                        </td>
                     </tr>
                 <?php endforeach; ?>
    </tbody>
        </table>
    <?php else: ?>
        <p>Vous n'êtes inscrit à aucun tournoi pour le moment.</p>
    <?php endif; ?>
    <hr>
<?php endif; ?>
    </body>
     <p><a href="logout.php">Se déconnecter</a></p>
</html>