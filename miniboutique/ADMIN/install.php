<?php
session_start();


$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time()-42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();


$connection = mysqli_connect('localhost', 'root', '', '');
if (!$connection) die("Erreur MySQL : " . mysqli_connect_error());

mysqli_query($connection, "DROP DATABASE IF EXISTS miniboutique");

mysqli_query($connection, "CREATE DATABASE miniboutique");
mysqli_select_db($connection, 'miniboutique');


mysqli_query($connection, "CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    mot_de_passe VARCHAR(255),
    est_admin BOOLEAN DEFAULT FALSE
)");


mysqli_query($connection, "CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prix DECIMAL(10,2),
    stock INT DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL,
    categorie VARCHAR(100) DEFAULT 'Divers',
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");


mysqli_query($connection, "CREATE TABLE paniers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    UNIQUE KEY utilisateur_produit (utilisateur_id, produit_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
)");


mysqli_query($connection, "CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    total DECIMAL(10,2),
    statut VARCHAR(50) DEFAULT 'En attente',
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
)");


mysqli_query($connection, "CREATE TABLE commande_produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT DEFAULT 1,
    prix_unitaire DECIMAL(10,2),
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
)");

$password = password_hash('admin123', PASSWORD_DEFAULT);
mysqli_query($connection, "INSERT INTO utilisateurs (nom, email, mot_de_passe, est_admin)
    VALUES ('Admin', 'admin@test.com', '$password', TRUE)");

mysqli_query($connection, "INSERT INTO produits (nom, prix, stock, image, categorie) VALUES
('Smartphone Galaxy Pro', 649.99, 15, 'smartphone.jpg', 'Électronique'),
('Jean Classic Fit', 79.99, 30, 'jean.jpg', 'Vêtements'),
('Roman Fantasy', 19.99, 25, 'roman.jpg', 'Livres'),
('Guide Cuisine', 24.99, 40, 'cuisine.jpg', 'Maison'),
('Casque Bluetooth', 59.99, 20, 'casque.jpg', 'Électronique')");

echo "<h3>✅ Installation terminée !</h3>";
echo "<p>Compte admin :</p>";
echo "<ul><li>Email: admin@test.com</li><li>Mot de passe: admin123</li></ul>";
echo "<p><a href='../STYLEES/connect.php'>Se connecter</a></p>";

mysqli_close($connection);
?>
