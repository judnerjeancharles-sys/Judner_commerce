<?php

$page_title = "Mes Commandes";
include 'header.php';

if (!$utilisateur_connecte) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['utilisateur_id'];


$commandes = mysqli_query($connection, "SELECT * FROM commandes WHERE utilisateur_id = $user_id ORDER BY date_commande DESC");

/
function statutClass($statut) {
    $map = [
        'En attente' => 'en-attente',
        'En préparation' => 'en-preparation',
        'Expédiée' => 'expediee',
        'Livrée' => 'livree',
        'Annulée' => 'annulee'
    ];
    return $map[$statut] ?? '';
}
?>

<style>
.hero { background: linear-gradient(45deg, #3498db, #2c3e50); color: white; text-align: center; padding: 30px; border-radius: 10px; margin-bottom: 30px; }
.card { background: white; padding: 20px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.status { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
.en-attente { background: #fff3cd; color: #856404; }
.en-preparation { background: #ffeeba; color: #856404; }
.expediee { background: #d4edda; color: #155724; }
.livree { background: #d4edda; color: #155724; }
.annulee { background: #f8d7da; color: #721c24; }
.btn { background: #3498db; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px; margin: 3px; }
.btn-sec { background: #7f8c8d; }
.flex { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
.vide { text-align: center; padding: 40px; color: #7f8c8d; }
</style>

<div class="hero">
    <h1>Mes Commandes</h1>
    <p>Historique de vos achats</p>
</div>

<?php if (mysqli_num_rows($commandes) > 0): ?>
    <?php while ($cmd = mysqli_fetch_assoc($commandes)): ?>
        <div class="card">
            <div class="flex">
                <div>
                    <h3>Commande #<?= $cmd['id'] ?></h3>
                    <p><?= date('d/m/Y', strtotime($cmd['date_commande'])) ?></p>
                    <span class="status <?= statutClass($cmd['statut']) ?>">
                        <?= htmlspecialchars($cmd['statut']) ?>
                    </span>
                </div>
                <div style="text-align: right;">
                    <h3><?= number_format($cmd['total'], 2) ?> €</h3>
                    <a href="facture.php?id=<?= $cmd['id'] ?>" class="btn">Facture</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="card vide">
        <h3>Aucune commande</h3>
        <p>Vous n'avez pas encore commandé</p>
        <a href="index.php" class="btn">Voir les produits</a>
    </div>
<?php endif; ?>

<div style="text-align: center; margin: 30px 0;">
    <a href="index.php" class="btn btn-sec">← Accueil</a>
    <a href="mon_compte.php" class="btn">Mon compte</a>
</div>

<?php include 'footer.php'; ?>
