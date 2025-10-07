<?php
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'miniboutique');
if (!$connection) die("Erreur de connexion à la base : " . mysqli_connect_error());


if (isset($_SESSION['utilisateur_id'])) {
    if ($_SESSION['est_admin']) {
        header('Location: ../ADMIN/admin.php');
    } else {
        header('Location: ../SHOP/index.php');
    }
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = mysqli_real_escape_string($connection, $_POST['nom'] ?? '');
    $email = mysqli_real_escape_string($connection, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nom && $email && $password) {
        if (strlen($password) >= 6) {
            $check = mysqli_query($connection, "SELECT id FROM utilisateurs WHERE email = '$email'");
            
            if (mysqli_num_rows($check) == 0) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                if (mysqli_query($connection, "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES ('$nom', '$email', '$password_hash')")) {
                    $_SESSION['utilisateur_id'] = mysqli_insert_id($connection);
                    $_SESSION['nom_utilisateur'] = $nom;
                    $_SESSION['est_admin'] = false;

                    header('Location: ../SHOP/index.php');
                    exit;
                } else {
                    $message = "Erreur lors de la création du compte";
                }
            } else {
                $message = "Email déjà utilisé";
            }
        } else {
            $message = "Mot de passe trop court (minimum 6 caractères)";
        }
    } else {
        $message = "Tous les champs sont requis";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - TROUVE-TOUT</title>
    <link rel="stylesheet" href="../STYLEES/styles.css">
</head>
<body>

<header class="header">
    <div class="nav-container">
        <a href="../SHOP/index.php" class="logo">TROUVE-TOUT</a>
        <div>
            <a href="../SHOP/index.php" class="nav-btn">Accueil</a>
            <a href="../STYLEES/connect.php" class="nav-btn">Connexion</a>
        </div>
    </div>
</header>

<div class="auth-wrapper">
    <div class="auth-box">
        <h1>Inscription</h1>
        <p>Créer votre compte TROUVE-TOUT</p>

        <?php if ($message): ?>
            <div class="error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="nom">Nom complet :</label>
            <input type="text" name="nom" id="nom" required placeholder="Votre nom">

            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required placeholder="votre@email.com">

            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required placeholder="Minimum 6 caractères">

            <button type="submit">Créer mon compte</button>
        </form>

        <div class="switch-link">
            <p>Déjà un compte ? <a href="../STYLEES/connect.php">Se connecter</a></p>
            <p><a href="../SHOP/index.php">Retour à l'accueil</a></p>
        </div>
    </div>
</div>

</body>
</html>
