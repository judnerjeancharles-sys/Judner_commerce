<?php
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'miniboutique');

if (isset($_SESSION['utilisateur_id'])) {
    $user_id = $_SESSION['utilisateur_id'];
} else {
    $user_id = 0;
}

$user = mysqli_fetch_assoc(mysqli_query($connection, "SELECT est_admin FROM utilisateurs WHERE id = $user_id"));
if (!$user || !$user['est_admin']) {
    die("Accès interdit");
}

$message = "";


if (isset($_POST['ajouter'])) {
    $nom = mysqli_real_escape_string($connection, $_POST['nom']);
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);
    $image_name = "";

 
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../image/produits/";
        $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "webp"];

        if (in_array($extension, $allowed)) {
            
            if (mysqli_query($connection, "INSERT INTO produits (nom, prix, stock) VALUES ('$nom', $prix, $stock)")) {
                $id = mysqli_insert_id($connection);
                $image_name = $id . "." . $extension;
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name);
                
                mysqli_query($connection, "UPDATE produits SET image='$image_name' WHERE id=$id");
                $message = "✅ Produit ajouté avec image";
            }
        } else {
            $message = "❌ Format d’image non autorisé (jpg, jpeg, png, webp uniquement).";
        }
    } else {
        
        if (mysqli_query($connection, "INSERT INTO produits (nom, prix, stock) VALUES ('$nom', $prix, $stock)")) {
            $message = "✅ Produit ajouté (sans image)";
        }
    }
}


if (isset($_POST['modifier'])) {
    $id = intval($_POST['id']);
    $nom = mysqli_real_escape_string($connection, $_POST['nom']);
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);

    mysqli_query($connection, "UPDATE produits SET nom='$nom', prix=$prix, stock=$stock WHERE id=$id");

    
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../image/produits/";
        $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "webp"];
        if (in_array($extension, $allowed)) {
            $image_name = $id . "." . $extension;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name);
            mysqli_query($connection, "UPDATE produits SET image='$image_name' WHERE id=$id");
            $message = "✅ Produit et image mis à jour";
        } else {
            $message = "❌ Format d’image non autorisé.";
        }
    } else {
        $message = "✅ Produit modifié (image inchangée)";
    }
}


if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
   
    $res = mysqli_fetch_assoc(mysqli_query($connection, "SELECT image FROM produits WHERE id=$id"));
    if ($res && $res['image'] && file_exists("../image/produits/" . $res['image'])) {
        unlink("../image/produits/" . $res['image']);
    }
    if (mysqli_query($connection, "DELETE FROM produits WHERE id = $id")) {
        $message = "🗑️ Produit supprimé";
    }
}


$edit = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM produits WHERE id = $id"));
}

$produits = mysqli_query($connection, "SELECT * FROM produits ORDER BY id DESC LIMIT 10");
?>

<h1>Administration</h1>

<?php if ($message): ?>
    <p style="color: green;"><?= $message ?></p>
<?php endif; ?>

<h3><?= $edit ? 'Modifier un produit' : 'Ajouter un produit' ?></h3>

<form method="POST" enctype="multipart/form-data">
    <?php if ($edit): ?>
        <input type="hidden" name="id" value="<?= $edit['id'] ?>">
    <?php endif; ?>
    
    <p>
        <label>Nom :</label><br>
        <input type="text" name="nom" value="<?= htmlspecialchars($edit['nom'] ?? '') ?>" required>
    </p>
    <p>
        <label>Image :</label><br>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"><br>
        <?php if ($edit && $edit['image']): ?>
            <img src="../image/produits/<?= htmlspecialchars($edit['image']) ?>" alt="" style="height:60px;border:1px solid #ccc;margin-top:5px;">
        <?php endif; ?>
    </p>
    <p>
        <label>Prix (€) :</label><br>
        <input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($edit['prix'] ?? '') ?>" required>
    </p>
    <p>
        <label>Stock :</label><br>
        <input type="number" name="stock" value="<?= htmlspecialchars($edit['stock'] ?? 0) ?>">
    </p>
    
    <?php if ($edit): ?>
        <button type="submit" name="modifier">Modifier</button>
        <a href="admin.php">Nouveau produit</a>
    <?php else: ?>
        <button type="submit" name="ajouter">Ajouter</button>
    <?php endif; ?>
</form>

<h3>Produits récents</h3>
<table border="1" style="width: 100%; border-collapse: collapse;">
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Image</th>
        <th>Prix</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <?php while ($p = mysqli_fetch_assoc($produits)): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['nom']) ?></td>
        <td>
            <?php if ($p['image'] && file_exists("../image/produits/" . $p['image'])): ?>
                <img src="../image/produits/<?= htmlspecialchars($p['image']) ?>" alt="" style="height:50px;">
            <?php else: ?>
                <span style="color:gray;">(aucune)</span>
            <?php endif; ?>
        </td>
        <td><?= number_format($p['prix'], 2) ?> €</td>
        <td><?= $p['stock'] ?></td>
        <td>
            <a href="admin.php?edit=<?= $p['id'] ?>">Modifier</a> |
            <a href="admin.php?delete=<?= $p['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<p><a href="../SHOP/index.php">← Retour au site</a></p>
