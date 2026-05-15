<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) redirect('login.php');
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) redirect('cart.php');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute(array($user_id));
$user = $stmt->fetch();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $postal_code = trim($_POST['postal_code']);
    $phone = trim($_POST['phone']);

    if (empty($address) || empty($city) || empty($phone)) {
        $error = 'Заполните обязательные поля';
    } else {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute(array($item['product_id']));
            $p = $stmt->fetch();
            $total += $p['price'] * $item['quantity'];
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, address, city, postal_code, phone, status) VALUES (?, ?, ?, ?, ?, ?, 'Новый')");
            $stmt->execute(array($user_id, $total, $address, $city, $postal_code, $phone));
            $order_id = $pdo->lastInsertId();

            foreach ($_SESSION['cart'] as $item) {
                $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->execute(array($item['product_id']));
                $p = $stmt->fetch();
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute(array($order_id, $item['product_id'], $item['quantity'], $p['price'], $item['size']));
            }

            // Уведомление
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute(array($user_id, "Заказ #$order_id оформлен на сумму " . formatPrice($total)));

            $pdo->commit();
            unset($_SESSION['cart']);
            redirect("cabinet.php?section=orders");
        } catch (Exception $e) {
            $pdo->rollback();
            $error = 'Ошибка: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h2 class="section-title">Оформление заказа</h2>
    <div style="max-width:700px;margin:0 auto;">
        <?php if($error): ?><div class="alert alert--error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" class="form-container" style="margin-top:0;">
            <div class="form-group"><label>Город *</label><input type="text" name="city" value="<?php echo h($user['city']); ?>" required></div>
            <div class="form-group"><label>Адрес доставки *</label><textarea name="address" required><?php echo h($user['address']); ?></textarea></div>
            <div class="form-group"><label>Индекс</label><input type="text" name="postal_code" value="<?php echo h($user['postal_code']); ?>"></div>
            <div class="form-group"><label>Телефон *</label><input type="text" name="phone" value="<?php echo h($user['phone']); ?>" required></div>

            <h3 style="margin-bottom:15px;">Ваш заказ:</h3>
            <?php $total = 0;
            foreach ($_SESSION['cart'] as $item):
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute(array($item['product_id']));
                $p = $stmt->fetch();
                $subtotal = $p['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #eee;">
                    <span><?php echo h($p['name']); ?> × <?php echo $item['quantity']; ?></span>
                    <span><?php echo formatPrice($subtotal); ?></span>
                </div>
            <?php endforeach; ?>
            <div style="display:flex;justify-content:space-between;padding:15px 0;font-size:20px;font-weight:700;">
                <span>Итого:</span><span style="color:var(--accent);"><?php echo formatPrice($total); ?></span>
            </div>
            <button type="submit" class="btn btn--accent" style="width:100%;">Подтвердить заказ</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>