<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
session_start();

if (!isLoggedIn()) redirect('login.php');

$user_id = $_SESSION['user_id'];
$section = isset($_GET['section']) ? $_GET['section'] : 'orders';

// Обновление профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $postal_code = trim($_POST['postal_code']);
    $stmt = $pdo->prepare("UPDATE users SET phone=?, address=?, city=?, postal_code=? WHERE id=?");
    $stmt->execute(array($phone, $address, $city, $postal_code, $user_id));
    $profile_success = 'Данные обновлены';
}

$user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user->execute(array($user_id));
$user = $user->fetch();

$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders->execute(array($user_id));

$favorites = $pdo->prepare("SELECT p.*, c.name AS category_name FROM favorites f JOIN products p ON f.product_id = p.id LEFT JOIN categories c ON p.category_id = c.id WHERE f.user_id = ? ORDER BY f.created_at DESC");
$favorites->execute(array($user_id));

$notifications = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
$notifications->execute(array($user_id));
$pdo->prepare("UPDATE notifications SET is_read=1 WHERE user_id=? AND is_read=0")->execute(array($user_id));

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="main-layout">
        <aside class="sidebar">
            <h2 class="sidebar__title">Личный кабинет</h2>
            <a href="cabinet.php?section=orders" class="sidebar__link<?php echo $section=='orders'?' active':''; ?>">📦 Мои заказы</a>
            <a href="cabinet.php?section=favorites" class="sidebar__link<?php echo $section=='favorites'?' active':''; ?>">❤️ Избранное</a>
            <a href="cabinet.php?section=notifications" class="sidebar__link<?php echo $section=='notifications'?' active':''; ?>">🔔 Уведомления</a>
            <a href="cabinet.php?section=profile" class="sidebar__link<?php echo $section=='profile'?' active':''; ?>">👤 Мои данные</a>
            <a href="logout.php" class="sidebar__link" style="color:#c0392b;">🚪 Выход</a>
        </aside>

        <div class="main-content">
            <?php if($section == 'orders'): ?>
                <h2 class="section-title">Мои заказы</h2>
                <?php if($orders->rowCount() > 0): ?>
                    <div class="table-wrap"><table class="table">
                        <thead><tr><th>№ заказа</th><th>Сумма</th><th>Статус</th><th>Дата</th></tr></thead>
                        <tbody>
                            <?php while($o = $orders->fetch()): ?>
                                <tr><td><strong>#<?php echo $o['id']; ?></strong></td><td><?php echo formatPrice($o['total_price']); ?></td><td><?php echo h($o['status']); ?></td><td><?php echo date('d.m.Y', strtotime($o['created_at'])); ?></td></tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table></div>
                <?php else: ?><div class="empty-state"><div class="empty-state__icon">📦</div><h3>Заказов пока нет</h3></div><?php endif; ?>

            <?php elseif($section == 'favorites'): ?>
                <h2 class="section-title">Избранное</h2>
                <?php if($favorites->rowCount() > 0): ?>
                    <div class="products-grid">
                        <?php while($p = $favorites->fetch()): ?>
                            <div class="product-card">
                                <div class="product-card__img-wrap"><img src="/uploads/<?php echo h($p['image']); ?>" class="product-card__img"></div>
                                <div class="product-card__body">
                                    <h3 class="product-card__title"><a href="product.php?id=<?php echo $p['id']; ?>"><?php echo h($p['name']); ?></a></h3>
                                    <p class="product-card__price"><?php echo formatPrice($p['price']); ?></p>
                                    <a href="/ajax/remove_favorite.php?id=<?php echo $p['id']; ?>" class="btn btn--sm btn--danger">Убрать</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?><div class="empty-state"><div class="empty-state__icon">❤️</div><h3>В избранном пусто</h3></div><?php endif; ?>

            <?php elseif($section == 'notifications'): ?>
                <h2 class="section-title">Уведомления</h2>
                <?php while($n = $notifications->fetch()): ?>
                    <div style="background:#fff;padding:15px;border-radius:8px;margin-bottom:10px;border-left:3px solid <?php echo $n['is_read']?'#ddd':'var(--primary)'; ?>;">
                        <p><?php echo h($n['message']); ?></p>
                        <small style="color:#999;"><?php echo date('d.m.Y H:i', strtotime($n['created_at'])); ?></small>
                    </div>
                <?php endwhile; ?>

            <?php elseif($section == 'profile'): ?>
                <h2 class="section-title">Мои данные</h2>
                <?php if(isset($profile_success)): ?><div class="alert alert--success"><?php echo $profile_success; ?></div><?php endif; ?>
                <form method="POST" style="max-width:600px;">
                    <div class="form-group"><label>ФИО</label><input type="text" value="<?php echo h($user['full_name']); ?>" disabled></div>
                    <div class="form-group"><label>Email</label><input type="email" value="<?php echo h($user['email']); ?>" disabled></div>
                    <div class="form-group"><label>Телефон</label><input type="text" name="phone" value="<?php echo h($user['phone']); ?>"></div>
                    <div class="form-group"><label>Город</label><input type="text" name="city" value="<?php echo h($user['city']); ?>"></div>
                    <div class="form-group"><label>Адрес</label><textarea name="address"><?php echo h($user['address']); ?></textarea></div>
                    <div class="form-group"><label>Индекс</label><input type="text" name="postal_code" value="<?php echo h($user['postal_code']); ?>"></div>
                    <button type="submit" name="update_profile" class="btn btn--primary">Сохранить</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>