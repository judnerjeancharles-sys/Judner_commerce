<?php
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'miniboutique');
if (!$connection) die("Erreur de connexion : " . mysqli_connect_error());


if (isset($_SESSION['utilisateur_id']) && !empty($_SESSION['panier'])) {
    $user_id = $_SESSION['utilisateur_id'];
    foreach ($_SESSION['panier'] as $produit_id => $quantite) {
        $res = mysqli_query($connection, "SELECT id FROM paniers WHERE utilisateur_id=$user_id AND produit_id=$produit_id");
        if (mysqli_num_rows($res)) {
            mysqli_query($connection, "UPDATE paniers SET quantite=$quantite WHERE utilisateur_id=$user_id AND produit_id=$produit_id");
        } else {
            mysqli_query($connection, "INSERT INTO paniers (utilisateur_id, produit_id, quantite) VALUES ($user_id, $produit_id, $quantite)");
        }
    }
}


$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

header('Location: ../SHOP/index.php');
exit;
