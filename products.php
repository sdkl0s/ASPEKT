<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirect('/login.php');

$message = '';

// Добавление
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $description = trim($_POST['description']);
    $image = 'placeholder.jpg';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/' . $filename);
        $image = $filename;
    }
    
    $stmt = $pdo->prepare("INSERT INTO products (name, price, category_id, description, image, stock, is_active) VALUES (?,?,?,?,?,999,1)");
    $stmt->execute(array($name, $price, $category_id, $description, $image));
    $message = 'Товар добавлен';
}

// Удаление
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute(array(intval($_GET['delete'])));
    $message = 'Товар удалён';
}

$products = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$categories = $pdo->query("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"><title>Товары — Админка</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f2f3f5;display:flex;min-height:100vh}
        .admin-sidebar{width:250px;background:#1e293b;color:#fff;padding:20px 0}
        .admin-sidebar a{display:block;color:rgba(255,255,255,0.7);text-decoration:none;padding:12px 24px;font-size:14px}
        .admin-sidebar a:hover,.admin-sidebar a.active{background:rgba(255,255,255,0.1);color:#fff}
        .admin-content{flex:1;padding:30px}
        .form-group{margin-bottom:15px}
        .form-group label{display:block;margin-bottom:5px;font-weight:600;font-size:13px}
        .form-group input,.form-group textarea,.form-group select{width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px}
        table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;margin-top:20px}
        th{background:#f0f3f8;padding:12px;text-align:left;font-size:12px;text-transform:uppercase}
        td{padding:12px;border-bottom:1px solid #eee;font-size:14px}
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
    <a href="products.php" class="active">Товары</a>
    <a href="categories.php">Категории</a>
    <a href="orders.php">Заказы</a>
</div>
<div class="admin-content">
    <h1>Товары</h1>
    <?php if($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data" style="background:#fff;padding:20px;border-radius:10px;margin-bottom:20px;">
        <h3 style="margin-bottom:15px;">Добавить товар</h3>
        <div class="form-group"><label>Название</label><input type="text" name="name" required></div>
        <div class="form-group"><label>Цена</label><input type="number" name="price" step="0.01" required></div>
        <div class="form-group"><label>Категория</label><select name="category_id"><?php while($c=$categories->fetch()): ?><option value="<?php echo $c['id']; ?>"><?php echo h($c['name']); ?></option><?php endwhile; ?></select></div>
        <div class="form-group"><label>Описание</label><textarea name="description" rows="3"></textarea></div>
        <div class="form-group"><label>Изображение</label><input type="file" name="image"></div>
        <button type="submit" name="add" class="btn btn-primary">Добавить</button>
    </form>

    <table>
        <thead><tr><th>ID</th><th>Фото</th><th>Название</th><th>Категория</th><th>Цена</th><th></th></tr></thead>
        <tbody>
            <?php while($p=$products->fetch()): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><img src="/uploads/<?php echo h($p['image']); ?>" style="width:40px;height:40px;object-fit:cover;border-radius:4px;"></td>
                    <td><?php echo h($p['name']); ?></td>
                    <td><?php echo h($p['category_name']??'—'); ?></td>
                    <td><?php echo formatPrice($p['price']); ?></td>
                    <td><a href="?delete=<?php echo $p['id']; ?>" class="btn btn-danger" onclick="return confirm('Удалить?')">Удалить</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>