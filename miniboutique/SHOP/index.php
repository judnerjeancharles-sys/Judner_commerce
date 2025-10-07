<?php
include 'header.php';


$categorie = $_GET['categorie'] ?? 'Tous';

if ($categorie === 'Tous') {
    $query = mysqli_query($connection, "SELECT * FROM produits ORDER BY date_ajout DESC");
} else {
    $stmt = mysqli_prepare($connection, "SELECT * FROM produits WHERE categorie = ? ORDER BY date_ajout DESC");
    mysqli_stmt_bind_param($stmt, "s", $categorie);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
}


$categories_result = mysqli_query($connection, "SELECT DISTINCT categorie FROM produits");
$categories = ['Tous'];
while ($cat = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $cat['categorie'];
}


if (isset($_GET['add'])) {
    $idProduit = intval($_GET['add']);
    if (!isset($_SESSION['panier'])) $_SESSION['panier'] = [];
    if (isset($_SESSION['panier'][$idProduit])) {
        $_SESSION['panier'][$idProduit]++;
    } else {
        $_SESSION['panier'][$idProduit] = 1;
    }
    $messageAjout = "✅ Produit ajouté au panier !";
}
?>

<section class="produits-container">
    <h1 class="titre-principal">Nos produits</h1>

    <?php if (!empty($messageAjout)): ?>
        <div class="notification" style="text-align:center; margin-bottom:15px; color:#27ae60; font-weight:600;">
            <?= $messageAjout ?>
        </div>
    <?php endif; ?>

    <div class="categories-bar">
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?categorie=<?= urlencode($cat) ?>" 
               class="categorie-btn <?= ($cat === $categorie) ? 'active' : '' ?>">
               <?= htmlspecialchars($cat) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="grille-produits">
        <?php while ($p = mysqli_fetch_assoc($query)): ?>
            <div class="produit-card">
                <img src="../IMAGES/<?= htmlspecialchars($p['image']) ?>" 
                     alt="<?= htmlspecialchars($p['nom']) ?>" class="produit-image">
                <div>
                    <p class="categorie"><?= htmlspecialchars($p['categorie']) ?></p>
                    <h3><?= htmlspecialchars($p['nom']) ?></h3>
                    <p class="prix"><?= number_format($p['prix'], 2, ',', ' ') ?> €</p>
                    <a href="index.php?add=<?= $p['id'] ?>" class="btn-ajouter">🛒 Ajouter au panier</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
