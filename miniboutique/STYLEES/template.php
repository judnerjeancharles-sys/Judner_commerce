<?php



function secure($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}


function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}


function showAlert($message, $type = 'info') {
    $color = $type == 'error' ? 'red' : 'green';
    return '<p style="color: ' . $color . ';">' . secure($message) . '</p>';
}


function calculateCartTotal($panier, $connection) {
    if (empty($panier)) return 0;
    
    $total = 0;
    foreach ($panier as $id => $qty) {
        $result = mysqli_query($connection, "SELECT prix FROM produits WHERE id = " . intval($id));
        if ($product = mysqli_fetch_assoc($result)) {
            $total += $product['prix'] * $qty;
        }
    }
    return $total;
}


function cleanInput($input) {
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}


define('SITE_NAME', 'MiniBoutique');
?>