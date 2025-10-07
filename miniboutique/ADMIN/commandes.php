<?php
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'miniboutique');
if (!$connection) die("Erreur de connexion à la base de données");

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['utilisateur_id'])) {
    die("Accès interdit : utilisateur non connecté.");
}

$user_id = intval($_SESSION['utilisateur_id']);
$user = mysqli_fetch_assoc(mysqli_query($connection, "SELECT est_admin FROM utilisateurs WHERE id = $user_id"));
if (!$user || !$user['est_admin']) {
    die("Accès interdit : administrateur requis.");
}

$message = "";


if (isset($_POST['creer_commande']) && !empty($_SESSION['panier'])) {
    $total = 0;

    foreach ($_SESSION['panier'] as $id => $qty) {
        $p = mysqli_fetch_assoc(mysqli_query($connection, "SELECT prix FROM produits WHERE id = $id"));
        if ($p) {
            $total += $p['prix'] * $qty;
        }
    }

    if ($total > 0) {
        $sql = "INSERT INTO commandes (utilisateur_id, total, statut, date_commande) VALUES ($user_id, $total, 'En attente', NOW())";
        if (mysqli_query($connection, $sql)) {
            $commande_id = mysqli_insert_id($connection);

            /
            foreach ($_SESSION['panier'] as $id => $qty) {
                $p = mysqli_fetch_assoc(mysqli_query($connection, "SELECT prix FROM produits WHERE id = $id"));
                if ($p) {
                    $prix = $p['prix'];
                    mysqli_query($connection, "INSERT INTO commande_produits (commande_id, produit_id, quantite, prix_unitaire)
                                               VALUES ($commande_id, $id, $qty, $prix)");
                }
            }

            $_SESSION['panier'] = array(); // vider panier
            $message = "✅ Commande #$commande_id créée avec succès.";
        } else {
            $message = "❌ Erreur lors de la création de la commande.";
        }
    } else {
        $message = "⚠️ Le panier est vide ou invalide.";
    }
}


if (isset($_POST['changer_statut'])) {
    $id = intval($_POST['commande_id']);
    $statut = mysqli_real_escape_string($connection, $_POST['statut']);

    if (mysqli_query($connection, "UPDATE commandes SET statut='$statut' WHERE id=$id")) {
        $message = "✅ Statut de la commande #$id mis à jour.";
    } else {
        $message = "❌ Erreur lors de la mise à jour du statut.";
    }
}


$commandes = mysqli_query($connection, "SELECT * FROM commandes ORDER BY date_commande DESC");
$panier_count = array_sum($_SESSION['panier'] ?? []);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des commandes - Admin</title>
<style>
body { font-family: Arial, sans-serif; margin: 30px; background: #f8f9fa; }
h1 { color: #2c3e50; }
.box { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
.ok { color: #155724; background: #d4edda; padding: 8px; border-radius: 5px; margin-bottom: 10px; }
.btn { background: #3498db; color: white; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; }
.btn:hover { background: #2980b9; }
select { padding: 4px; }
.produit-commande { display: flex; align-items: center; margin-bottom: 5px; }
.produit-commande img { height: 40px; margin-right: 10px; border: 1px solid #ccc; border-radius: 5px; }
</style>
</head>
<body>

<h1>🧾 Gestion des Commandes</h1>

<?php if ($message): ?>
    <div class="ok"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($panier_count > 0): ?>
<div class="box">
    <h3>Créer une commande à partir du panier</h3>
    <p><?= $panier_count ?> article(s) dans le panier.</p>
    <form method="POST">
        <button type="submit" name="creer_commande" class="btn">Créer commande</button>
    </form>
</div>
<?php endif; ?>

<div class="box">
    <h3>Liste des commandes</h3>

    <?php if (mysqli_num_rows($commandes) > 0): ?>
        <?php while ($c = mysqli_fetch_assoc($commandes)): ?>
            <div style="border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 8px;">
                <h4>Commande #<?= $c['id'] ?></h4>
                <p>Date : <?= date('d/m/Y H:i', strtotime($c['date_commande'])) ?></p>
                <p>Total : <strong><?= number_format($c['total'], 2) ?> €</strong></p>
                <p>Statut actuel : <strong><?= htmlspecialchars($c['statut']) ?></strong></p>

                <!-- Produits de la commande -->
                <?php
                $produits_commande = mysqli_query($connection, "SELECT cp.*, p.nom, p.image FROM commande_produits cp 
                                                                 LEFT JOIN produits p ON cp.produit_id = p.id 
                                                                 WHERE cp.commande_id = {$c['id']}");
                while ($prod = mysqli_fetch_assoc($produits_commande)):
                ?>
                    <div class="produit-commande">
                        <?php if ($prod['image'] && file_exists("../image/produits/" . $prod['image'])): ?>
                            <img src="../image/produits/<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['nom']) ?>">
                        <?php else: ?>
                            <img src="../image/produits/default.jpg" alt="Pas d'image">
                        <?php endif; ?>
                        <span><?= htmlspecialchars($prod['nom']) ?> x <?= $prod['quantite'] ?> (<?= number_format($prod['prix_unitaire'],2) ?> €)</span>
                    </div>
                <?php endwhile; ?>

                <form method="POST" style="margin-top: 5px;">
                    <input type="hidden" name="commande_id" value="<?= $c['id'] ?>">
                    <select name="statut">
                        <?php
                        $statuts = ['En attente', 'En préparation', 'Expédiée', 'Livrée', 'Annulée'];
                        foreach ($statuts as $s) {
                            $selected = ($c['statut'] == $s) ? 'selected' : '';
                            echo "<option value=\"$s\" $selected>$s</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" name="changer_statut" class="btn">Mettre à jour</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aucune commande pour le moment.</p>
    <?php endif; ?>
</div>

<p><a href="admin.php">← Retour à l’administration</a></p>

</body>
</html>
