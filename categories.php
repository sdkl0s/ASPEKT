<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirect('/login.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
    $stmt->execute(array($name, $slug));
    $message = 'Категория добавлена';
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute(array(intval($_GET['delete'])));
    $message = 'Категория удалена';
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"><title>Категории — Админка</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f2f3f5;display:flex;min-height:100vh}
        .admin-sidebar{width:250px;background:#1e293b;color:#fff;padding:20px 0}
        .admin-sidebar a{display:block;color:rgba(255,255,255,0.7);text-decoration:none;padding:12px 24px;font-size:14px}
        .admin-sidebar a:hover,.admin-sidebar a.active{background:rgba(255,255,255,0.1);color:#fff}
        .admin-content{flex:1;padding:30px}
        .form-group{margin-bottom:15px}
        .form-group label{display:block;margin-bottom:5px;font-weight:600;font-size:13px}
        .form-group input{width:100%;padding:10px;border:1px solid #ddd;border-radius:6px}
        table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;margin-top:20px}
        th{background:#f0f3f8;padding:12px;text-align:left;font-size:12px;text-transform:uppercase}
        td{padding:12px;border-bottom:1px solid #eee}
        .btn{padding:8px 16px;border:0;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-block}
        .btn-primary{background:#3b5998;color:#fff}
        .btn-danger{background:#c0392b;color:#fff}
        .alert{padding:12px;border-radius:6px;margin-bottom:15px;background:#eafaf1;color:#1e7e34}
    </style>
</head>
<body>
<div class="admin-sidebar">
    <h2 style="padding:0 24px 20px;">Админка</h2>
    <a href="index.php">Главная</a>
    <a href="products.php">Товары</a>
    <a href="categories.php" class="active">Категории</a>
    <a href="orders.php">Заказы</a>
</div>
<div class="admin-content">
    <h1>Категории</h1>
    <?php if($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>
    <form method="POST" style="background:#fff;padding:20px;border-radius:10px;margin-bottom:20px;">
        <div class="form-group"><label>Название</label><input type="text" name="name" required></div>
        <div class="form-group"><label>Slug (латиница)</label><input type="text" name="slug" required></div>
        <button type="submit" name="add" class="btn btn-primary">Добавить</button>
    </form>
    <table>
        <thead><tr><th>ID</th><th>Название</th><th>Slug</th><th></th></tr></thead>
        <tbody>
            <?php while($c=$categories->fetch()): ?>
                <tr><td><?php echo $c['id']; ?></td><td><?php echo h($c['name']); ?></td><td><?php echo h($c['slug']); ?></td><td><a href="?delete=<?php echo $c['id']; ?>" class="btn btn-danger" onclick="return confirm('Удалить?')">Удалить</a></td></tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>