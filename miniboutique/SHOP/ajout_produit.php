<?php
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie'];

  
    $image_nom = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $dossier_cible = "../IMAGES/" . basename($image_nom);

    if (move_uploaded_file($tmp_name, $dossier_cible)) {
        $sql = "INSERT INTO produits (nom, prix, categorie, image)
                VALUES ('$nom', '$prix', '$categorie', '$image_nom')";
        mysqli_query($connection, $sql);
        header("Location: index.php");
        exit;
    } else {
        echo "<p>❌ Erreur lors du téléchargement de l'image.</p>";
    }
}
?>

<main style="padding:20px;">
    <h1>Ajouter un produit</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="nom" placeholder="Nom du produit" required><br><br>
        <input type="number" step="0.01" name="prix" placeholder="Prix" required><br><br>
        <input type="text" name="categorie" placeholder="Catégorie" required><br><br>
        <input type="file" name="image" accept="image/*" required><br><br>
        <button type="submit">Ajouter</button>
    </form>
</main>
