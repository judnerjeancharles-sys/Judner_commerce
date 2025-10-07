<?php

session_start();


function isConnected() {
    return isset($_SESSION['utilisateur_id']);
}


function isAdmin() {
    return isConnected() && isset($_SESSION['est_admin']) && $_SESSION['est_admin'];
}


function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../SHOP/index.php');
        exit;
    }
}


function requireLogin() {
    if (!isConnected()) {
        header('Location: login.php');
        exit;
    }
}


function logout() {
    $_SESSION = array();
    session_destroy();
}


$utilisateur_connecte = isConnected();
$est_admin = isAdmin();
?>