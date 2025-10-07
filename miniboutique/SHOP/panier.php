<?php
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'miniboutique');
if (!$connection) die("Erreur de connexion à la base : " . mysqli_connect_error());

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}


if (isset($_GET['action']) && $_GET['action'] === 'ajouter' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id] += 1;
    } else {
        $_SESSION['panier'][$id] = 1;
    }
    $ajout_message = "✅ Produit ajouté au panier !";
}


if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (isset($_SESSION['panier'][$id])) {
        unset($_SESSION['panier'][$id]);
    }
}

$valider_message = "";
if (isset($_POST['valider'])) {
    if (!isset($_SESSION['utilisateur_id'])) {
        $valider_message = "❌ Vous devez être connecté pour valider le panier.";
    } else {
        $utilisateur_id = $_SESSION['utilisateur_id'];
        $total = 0;
        foreach ($_SESSION['panier'] as $produit_id => $quantite) {
            $res = mysqli_query($connection, "SELECT prix FROM produits WHERE id = $produit_id");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $total += $row['prix'] * $quantite;
            }
        }
        
        mysqli_query($connection, "INSERT INTO commandes (utilisateur_id, total) VALUES ($utilisateur_id, $total)");
        $commande_id = mysqli_insert_id($connection);

        
        foreach ($_SESSION['panier'] as $produit_id => $quantite) {
            $res = mysqli_query($connection, "SELECT prix FROM produits WHERE id = $produit_id");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $prix_unitaire = $row['prix'];
                mysqli_query($connection, "INSERT INTO commande_produits (commande_id, produit_id, quantite, prix_unitaire) VALUES ($commande_id, $produit_id, $quantite, $prix_unitaire)");
            }
        }
        $_SESSION['panier'] = [];
        $valider_message = "✅ Votre commande a été validée !";
    }
}


$produits_panier = [];
$total_panier = 0;
if (!empty($_SESSION['panier'])) {
    $ids = implode(',', array_keys($_SESSION['panier']));
    $res = mysqli_query($connection, "SELECT * FROM produits WHERE id IN ($ids)");
    while ($row = mysqli_fetch_assoc($res)) {
        $row['quantite'] = $_SESSION['panier'][$row['id']];
        $row['total'] = $row['prix'] * $row['quantite'];
        $total_panier += $row['total'];
        $produits_panier[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - TROUVE-TOUT</title>
    <link rel="stylesheet" href="../STYLEES/styles.css">
    <style>
        .panier-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .panier-container h2 { text-align: center; margin-bottom: 20px; color: #27ae60; }
        .panier-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .panier-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px; }
        .panier-item .info { flex: 1; }
        .panier-item .info h4 { margin-bottom: 5px; font-size: 16px; }
        .panier-item .prix { font-weight: 700; color: #ff4747; font-size: 18px; }
        .panier-total { text-align: right; margin-top: 20px; font-size: 20px; font-weight: 700; }
        .btn-valider { background: #27ae60; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; margin-top: 15px; }
        .btn-valider:hover { background: #219150; }
        .btn-supprimer { background: #e74c3c; color: white; padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; }
        .btn-supprimer:hover { background: #c0392b; }
        .notification { margin-bottom: 15px; padding: 12px; background: #dff0d8; color: #3c763d; border-radius: 6px; text-align: center; }
        .error { background: #fce4e4; color: #e74c3c; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="panier-container">
        <h2>Mon Panier</h2>

        <?php if (!empty($ajout_message)): ?>
            <div class="notification"><?= htmlspecialchars($ajout_message) ?></div>
        <?php endif; ?>

        <?php if (!empty($valider_message)): ?>
            <div class="<?= strpos($valider_message,'❌')!==false?'error':'notification' ?>"><?= htmlspecialchars($valider_message) ?></div>
        <?php endif; ?>

        <?php if (empty($produits_panier)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <?php foreach ($produits_panier as $produit): ?>
                <div class="panier-item">
                    <img src="../IMAGES/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                    <div class="info">
                        <h4><?= htmlspecialchars($produit['nom']) ?></h4>
                        <p>Quantité : <?= $produit['quantite'] ?></p>
                        <p class="prix"><?= number_format($produit['total'], 2, ',', ' ') ?> €</p>
                    </div>
                    <a href="panier.php?action=supprimer&id=<?= $produit['id'] ?>" class="btn-supprimer">Supprimer</a>
                </div>
            <?php endforeach; ?>
            <div class="panier-total">Total : <?= number_format($total_panier, 2, ',', ' ') ?> €</div>

            <form method="POST">
                <button type="submit" name="valider" class="btn-valider">Valider le panier</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
