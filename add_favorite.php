<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    $check = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
    $check->execute(array($user_id, $product_id));
    
    if (!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
        $stmt->execute(array($user_id, $product_id));
    }
}

$back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/catalog.php';
header("Location: $back");
exit;
?>