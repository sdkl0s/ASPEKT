<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
    $stmt->execute(array($_SESSION['user_id'], intval($_GET['id'])));
}

header('Location: /cabinet.php?section=favorites');
exit;
?>