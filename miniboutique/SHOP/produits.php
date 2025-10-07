<?php
include 'header.php';
$result = mysqli_query($connection, "SELECT * FROM produits ORDER BY categorie, nom");
?>
<section class="produits-container">
    <h1 class="titre-principal">Catalogue complet</h1>
    <div class="produits-grille">
        <?php while ($p = mysqli_fetch_assoc($result)): ?>
            <div class="produit-card">
                <img src="../IMAGES/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" class="produit-img">
                <h3><?= htmlspecialchars($p['nom']) ?></h3>
                <p class="categorie"><?= htmlspecialchars($p['categorie']) ?></p>
                <p class="prix"><?= number_format($p['prix'], 2, ',', ' ') ?> €</p>
                <a href="panier.php?id=<?= $p['id'] ?>" class="btn-ajouter">🛒 Ajouter au panier</a>
            </div>
        <?php endwhile; ?>
    </div>
</section>
<?php include 'footer.php'; ?>
