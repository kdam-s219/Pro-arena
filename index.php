<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ProArena – Tournois sportifs</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/design-system.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom px-4">
    <a class="navbar-brand text-white fw-bold" href="#">ProArena</a>

    <div class="ms-auto">
        <a href="login.php" class="btn btn-outline-custom me-2">Se connecter</a>
        <a href="inscription.php" class="btn btn-primary-custom">S’inscrire</a>
    </div>
</nav>

<!-- Hero -->
<section class="full-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center glass-card fade-up">

                <h1 class="mb-3">La compétition, simplifiée.</h1>

                <p class="text-muted-custom mb-4">
                    ProArena connecte les athlètes aux tournois de sports individuels
                    et permet aux clubs d’organiser facilement leurs compétitions.
                </p>

                <div class="d-flex justify-content-center gap-3">
                    <a href="login.php" class="btn btn-primary-custom">Je suis athlète</a>
                    <a href="login.php" class="btn btn-outline-custom">Je suis organisateur</a>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Sections -->
<section class="container my-5">
    <div class="row g-4">

        <div class="col-md-4">
            <div class="glass-card h-100">
                <h4>Pour les athlètes</h4>
                <p class="text-muted-custom">
                    Trouvez des tournois proches de vous et inscrivez-vous facilement.
                </p>
                <ul>
                    <li>Judo</li>
                    <li>Échecs</li>
                    <li>Tennis</li>
                    <li>Et plus</li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card h-100">
                <h4>Pour les organisateurs</h4>
                <p class="text-muted-custom">
                    Créez et gérez vos compétitions sportives en quelques clics.
                </p>
                <ul>
                    <li>Création de tournois</li>
                    <li>Gestion des inscriptions</li>
                    <li>Visibilité</li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card h-100">
                <h4>Notre vision</h4>
                <p class="text-muted-custom">
                    Démocratiser l’accès aux compétitions sportives dans toutes les régions.
                </p>
            </div>
        </div>

    </div>
</section>

<!-- Footer -->
<footer class="text-center text-muted-custom py-4">
    © 2026 ProArena
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
