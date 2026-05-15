<?php
function h($string) {
    if (!isset($string)) $string = '';
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function getCartCount() {
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        return count($_SESSION['cart']);
    }
    return 0;
}

function formatPrice($price) {
    return number_format($price, 0, ',', ' ') . ' ₽';
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function redirect($url) {
    header("Location: $url");
    exit;
}
?>