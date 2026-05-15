<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirect('/login.php');

if (isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute(array($_POST['status'], intval($_POST['order_id'])));
}

$orders = $pdo->query("SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"><title>Заказы — Админка</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f2f3f5;display:flex;min-height:100vh}
        .admin-sidebar{width:250px;background:#1e293b;color:#fff;padding:20px 0}
        .admin-sidebar a{display:block;color:rgba(255,255,255,0.7);text-decoration:none;padding:12px 24px;font-size:14px}
        .admin-sidebar a:hover,.admin-sidebar a.active{background:rgba(255,255,255,0.1);color:#fff}
        .admin-content{flex:1;padding:30px}
        table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden}
        th{background:#f0f3f8;padding:12px;text-align:left;font-size:12px;text-transform:uppercase}
        td{padding:12px;border-bottom:1px solid #eee;font-size:14px}
        select{padding:6px 10px;border-radius:6px;border:1px solid #ddd}
        .btn{padding:6px 12px;background:#3b5998;color:#fff;border:0;border-radius:6px;cursor:pointer;font-size:12px}
    </style>
</head>
<body>
<div class="admin-sidebar">
    <h2 style="padding:0 24px 20px;">Админка</h2>
    <a href="index.php">Главная</a>
    <a href="products.php">Товары</a>
    <a href="categories.php">Категории</a>
    <a href="orders.php" class="active">Заказы</a>
</div>
<div class="admin-content">
    <h1>Заказы</h1>
    <table>
        <thead><tr><th>№</th><th>Клиент</th><th>Email</th><th>Сумма</th><th>Статус</th><th>Дата</th><th></th></tr></thead>
        <tbody>
            <?php while($o=$orders->fetch()): ?>
                <tr>
                    <td><strong>#<?php echo $o['id']; ?></strong></td>
                    <td><?php echo h($o['full_name']); ?></td>
                    <td><?php echo h($o['email']); ?></td>
                    <td><?php echo formatPrice($o['total_price']); ?></td>
                    <td>
                        <form method="POST" style="display:flex;gap:6px;">
                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                            <select name="status">
                                <option <?php echo $o['status']=='Новый'?'selected':''; ?>>Новый</option>
                                <option <?php echo $o['status']=='В обработке'?'selected':''; ?>>В обработке</option>
                                <option <?php echo $o['status']=='Отправлен'?'selected':''; ?>>Отправлен</option>
                                <option <?php echo $o['status']=='Доставлен'?'selected':''; ?>>Доставлен</option>
                                <option <?php echo $o['status']=='Отменён'?'selected':''; ?>>Отменён</option>
                            </select>
                            <button type="submit" name="update_status" class="btn">ОК</button>
                        </form>
                    </td>
                    <td><?php echo date('d.m.Y', strtotime($o['created_at'])); ?></td>
                    <td><?php echo h($o['city']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>