<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_GET['id'])) {
    header('Location: /catalog.php');
    exit;
}

$product_id = intval($_GET['id']);
$size = isset($_GET['size']) ? $_GET['size'] : '';

// Проверка товара
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND is_active = 1");
$stmt->execute(array($product_id));
if (!$stmt->fetch()) {
    header('Location: /catalog.php');
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] == $product_id && $item['size'] == $size) {
        $item['quantity']++;
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = array(
        'product_id' => $product_id,
        'quantity' => 1,
        'size' => $size
    );
}

header('Location: /cart.php');
exit;
?>