<?php

session_start();
$connection = mysqli_connect('localhost', 'root', '', 'miniboutique');

if (isset($_SESSION['utilisateur_id'])) {
    $user_id = $_SESSION['utilisateur_id'];
} else {
    $user_id = 0;
}
$is_admin = mysqli_fetch_assoc(mysqli_query($connection, "SELECT est_admin FROM utilisateurs WHERE id = $user_id"))['est_admin'] ?? false;

if (!$is_admin) {
    die("Accès refusé");
}

$message = "";


if (isset($_POST['toggle_admin'])) {
    $target_id = intval($_POST['target_id']);
    $new_status = intval($_POST['new_status']);
    
    if ($target_id != $user_id) {
        if (mysqli_query($connection, "UPDATE utilisateurs SET est_admin = $new_status WHERE id = $target_id")) {
            $message = $new_status ? "Promu admin" : "Retiré admin";
        }
    }
}

if (isset($_POST['delete_user'])) {
    $target_id = intval($_POST['target_id']);
    
    if ($target_id != $user_id) {
        if (mysqli_query($connection, "DELETE FROM utilisateurs WHERE id = $target_id")) {
            $message = "Utilisateur supprimé";
        }
    }
}

$users = mysqli_query($connection, "SELECT * FROM utilisateurs ORDER BY est_admin DESC, nom");
?>

<h1>Gestion Utilisateurs</h1>

<?php if ($message): ?>
    <p style="color: green;"><?= $message ?></p>
<?php endif; ?>

<table border="1" style="width: 100%;">
    <tr>
        <th>Nom</th>
        <th>Email</th>
        <th>Type</th>
        <th>Actions</th>
    </tr>
    <?php while ($u = mysqli_fetch_assoc($users)): ?>
    <tr>
        <td><?= htmlspecialchars($u['nom']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= $u['est_admin'] ? 'ADMIN' : 'USER' ?></td>
        <td>
            <?php if ($u['id'] != $user_id): ?>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="target_id" value="<?= $u['id'] ?>">
                    <input type="hidden" name="new_status" value="<?= $u['est_admin'] ? '0' : '1' ?>">
                    <button type="submit" name="toggle_admin" 
                            onclick="return confirm('Changer le statut admin ?')">
                        <?= $u['est_admin'] ? 'Retirer Admin' : 'Faire Admin' ?>
                    </button>
                </form>
                
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="target_id" value="<?= $u['id'] ?>">
                    <button type="submit" name="delete_user" 
                            onclick="return confirm('Supprimer cet utilisateur ?')"
                            style="background: red; color: white;">
                        Supprimer
                    </button>
                </form>
            <?php else: ?>
                <em>Votre compte</em>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<p><a href="admin.php">Retour admin</a></p>