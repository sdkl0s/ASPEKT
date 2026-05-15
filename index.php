<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) redirect('/login.php');

$products_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orders_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"><title>Админка SPECIALWEAR</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f2f3f5;display:flex;min-height:100vh}
        .admin-sidebar{width:250px;background:#1e293b;color:#fff;padding:20px 0;flex-shrink:0}
        .admin-sidebar a{display:block;color:rgba(255,255,255,0.7);text-decoration:none;padding:12px 24px;font-size:14px}
        .admin-sidebar a:hover,.admin-sidebar a.active{background:rgba(255,255,255,0.1);color:#fff}
        .admin-content{flex:1;padding:30px}
        .admin-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:20px}
        .admin-card{background:#fff;padding:24px;border-radius:10px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.06)}
        .admin-card h2{font-size:36px;color:#3b5998}
        .admin-card p{color:#666;margin-top:5px}
        .btn{display:inline-block;padding:10px 20px;background:#3b5998;color:#fff;text-decoration:none;border-radius:6px;margin-top:10px;font-size:13px}
    </style>
</head>
<body>
<div class="admin-sidebar">
    <h2 style="padding:0 24px 20px;font-size:20px;">SPECIALWEAR</h2>
    <a href="index.php" class="active">Главная</a>
    <a href="products.php">Товары</a>
    <a href="categories.php">Категории</a>
    <a href="orders.php">Заказы</a>
    <a href="/index.php">← На сайт</a>
</div>
<div class="admin-content">
    <h1>Панель управления</h1>
    <div class="admin-cards">
        <div class="admin-card"><h2><?php echo $products_count; ?></h2><p>Товаров</p><a href="products.php" class="btn">Управлять</a></div>
        <div class="admin-card"><h2><?php echo $orders_count; ?></h2><p>Заказов</p><a href="orders.php" class="btn">Смотреть</a></div>
        <div class="admin-card"><h2><?php echo $users_count; ?></h2><p>Пользователей</p></div>
    </div>
</div>
</body>
</html>