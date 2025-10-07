<?php
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'miniboutique');
if (!$connection) die("Erreur de connexion à la base : " . mysqli_connect_error());

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../STYLEES/connect.php');
    exit;
}

$user_id = $_SESSION['utilisateur_id'];
$message = "";


if (isset($_POST['supprimer_compte'])) {

    
    $stmt = mysqli_prepare($connection, "DELETE FROM utilisateurs WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        header('Location: ../SHOP/index.php');
        exit;
    } else {
        $message = "Erreur lors de la suppression du compte.";
    }
}


$stmt = mysqli_prepare($connection, "SELECT nom, email FROM utilisateurs WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mon Compte - TROUVE-TOUT</title>
<link rel="stylesheet" href="../STYLEES/styles.css">
</head>
<body>

<header class="header">
    <div class="nav-container">
        <a href="index.php" class="logo">TROUVE-TOUT</a>
        <div>
            <a href="index.php" class="nav-btn">Accueil</a>
            <a href="../ADMIN/logout.php" class="nav-btn">Déconnexion</a>
        </div>
    </div>
</header>

<div class="auth-wrapper">
    <div class="auth-box">
        <h1>Mon Compte</h1>

        <?php if ($message): ?>
            <div class="error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>

        <hr style="margin: 20px 0;">

        <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer votre compte ?');">
            <button type="submit" name="supprimer_compte">Supprimer mon compte définitivement</button>
        </form>
    </div>
</div>

</body>
</html>
