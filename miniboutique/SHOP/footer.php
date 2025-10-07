<?php

?>

<footer style="background: #2c3e50; padding: 15px 0; margin-top: 40px;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px;">
        <div style="color: white; font-size: 1.2em;">
            &copy; 2025 TROUVE-TOUT - JJC
        </div>
        
        <div>
            <a href="index.php" style="background: #242525ff; color: white; padding: 8px 15px; margin: 0 5px; border-radius: 5px; text-decoration: none; font-size: 14px;">Accueil</a>
            <a href="panier.php" style="background: #27ae60; color: white; padding: 8px 15px; margin: 0 5px; border-radius: 5px; text-decoration: none; font-size: 14px;">Panier</a>
            
            <?php if (isset($est_admin) && $est_admin): ?>
                <a href="../ADMIN/admin.php" style="background: #27ae60; color: white; padding: 8px 15px; margin: 0 5px; border-radius: 5px; text-decoration: none; font-size: 14px;">Admin</a>
            
            <?php endif; ?>
        </div>
    </div>
</footer>
</body>
</html>