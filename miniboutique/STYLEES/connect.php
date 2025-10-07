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
    $email = mysqli_real_escape_string($connection, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $res = mysqli_query($connection, "SELECT id, nom, email, mot_de_passe, est_admin FROM utilisateurs WHERE email = '$email'");
        $user = mysqli_fetch_assoc($res);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['utilisateur_id'] = $user['id'];
            $_SESSION['nom_utilisateur'] = $user['nom'];
            $_SESSION['est_admin'] = $user['est_admin'];

           
            if ($user['est_admin']) {
                header('Location: ../ADMIN/admin.php');
            } else {
                header('Location: ../SHOP/index.php');
            }
            exit;
        } else {
            $message = "Email ou mot de passe incorrect";
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
    <title>Connexion - TROUVE-TOUT</title>
    <link rel="stylesheet" href="../STYLEES/styles.css">
</head>
<body>

<header class="header">
    <div class="nav-container">
        <a href="../SHOP/index.php" class="logo">TROUVE-TOUT</a>
        <div>
            <a href="../SHOP/index.php" class="nav-btn">Accueil</a>
            <a href="../ADMIN/register.php" class="nav-btn">Inscription</a>
        </div>
    </div>
</header>

<div class="auth-wrapper">
    <div class="auth-box">
        <h1>Connexion</h1>
        <p>Connectez-vous à votre compte TROUVE-TOUT</p>

        <?php if ($message): ?>
            <div class="error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required placeholder="votre@email.com">

            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required placeholder="Votre mot de passe">

            <button type="submit">Se connecter</button>
        </form>

        <div class="switch-link">
            <p>Pas de compte ? <a href="../ADMIN/register.php">Créer un compte</a></p>
            <p><a href="../SHOP/index.php">Retour à l'accueil</a></p>
        </div>
    </div>
</div>

</body>
</html>
