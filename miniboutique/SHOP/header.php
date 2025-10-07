<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$connection = mysqli_connect('localhost', 'root', '', 'miniboutique');
if (!$connection) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

$utilisateur_connecte = isset($_SESSION['utilisateur_id']);
$est_admin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'];
$panier_count = array_sum($_SESSION['panier'] ?? []);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'TROUVE-TOUT' ?></title>
    <link rel="stylesheet" href="../STYLEES/styles.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f9f9f9;
        }

        .header {
            background: #2c3e50;
            padding: 15px 0;
            margin-bottom: 30px;
        }

        .nav-container {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            color: white;
            font-size: 1.6em;
            font-weight: bold;
            text-decoration: none;
            letter-spacing: 1px;
        }

        .nav-btn {
            background: #27ae60;
            color: white;
            padding: 8px 15px;
            margin: 0 5px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .nav-btn:hover {
            background: #1f8e4d;
        }

        .categories {
            text-align: center;
            margin-bottom: 30px;
        }

        .cat-btn {
            background: #27ae60;
            color: white;
            padding: 8px 14px;
            margin: 5px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
            display: inline-block;
        }

        .cat-btn:hover {
            background: #1f8e4d;
        }

        .produits-container {
            max-width: 1200px;
            margin: auto;
            padding: 0 20px 40px;
        }

        .titre-principal {
            text-align: center;
            font-size: 1.8em;
            margin-bottom: 15px;
            color: #333;
        }

        .produits-grille {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 20px;
        }

        .produit-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
            transition: 0.3s;
        }

        .produit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .produit-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .categorie {
            color: #888;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .prix {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 10px;
        }

        .btn-ajouter {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-ajouter:hover {
            background: #1e8f50;
        }

        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
<header class="header">
    <div class="nav-container">
        <a href="index.php" class="logo">TROUVE-TOUT</a>
        <nav>
            <a href="index.php" class="nav-btn">Accueil</a>
            <a href="panier.php" class="nav-btn">Panier (<?= $panier_count ?>)</a>
            <?php if ($utilisateur_connecte): ?>
                <?php if ($est_admin): ?>
                    <a href="../ADMIN/admin.php" class="nav-btn">Admin</a>
                <?php endif; ?>
                <a href="mon_compte.php" class="nav-btn">Mon compte</a>
                <a href="../ADMIN/logout.php" class="nav-btn">Déconnexion</a>
            <?php else: ?>
                <a href="../STYLEES/connect.php" class="nav-btn">Connexion</a>
                <a href="../ADMIN/register.php" class="nav-btn">Inscription</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main>
