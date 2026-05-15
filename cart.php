<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// Удаление
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    redirect('cart.php');
}

// Очистка
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    redirect('cart.php');
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h2 class="section-title">Корзина</h2>

    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Товар</th><th>Размер</th><th>Цена</th><th>Кол-во</th><th>Сумма</th><th></th></tr>
                </thead>
                <tbody>
                    <?php $total = 0;
                    foreach ($_SESSION['cart'] as $i => $item):
                        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                        $stmt->execute(array($item['product_id']));
                        $p = $stmt->fetch();
                        if (!$p) continue;
                        $subtotal = $p['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td>
                                <img src="/uploads/<?php echo h($p['image']); ?>" style="width:50px;height:50px;object-fit:cover;border-radius:4px;vertical-align:middle;margin-right:10px;">
                                <?php echo h($p['name']); ?>
                            </td>
                            <td><?php echo $item['size'] ? h($item['size']) : '—'; ?></td>
                            <td><?php echo formatPrice($p['price']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><strong><?php echo formatPrice($subtotal); ?></strong></td>
                            <td><a href="cart.php?remove=<?php echo $i; ?>" class="btn btn--sm btn--danger">Удалить</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="cart-summary">
            <a href="cart.php?clear=1" class="btn btn--danger">Очистить корзину</a>
            <div><span style="font-size:18px;margin-right:20px;">Итого:</span><span class="cart-total"><?php echo formatPrice($total); ?></span></div>
            <a href="checkout.php" class="btn btn--accent">Оформить заказ</a>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state__icon">🛒</div>
            <h3>Корзина пуста</h3>
            <a href="catalog.php" class="btn btn--primary" style="margin-top:15px;">В каталог</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>