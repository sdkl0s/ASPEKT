<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$results = null;

if ($query != '') {
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 AND (p.name LIKE ? OR p.description LIKE ?) ORDER BY p.id DESC");
    $like = "%$query%";
    $stmt->execute(array($like, $like));
    $results = $stmt;
}
?>

<div class="container">
    <h2 class="section-title">Поиск: «<?php echo h($query); ?>»</h2>

    <?php if ($query == ''): ?>
        <div class="empty-state"><div class="empty-state__icon">🔍</div><h3>Введите поисковый запрос</h3></div>
    <?php elseif ($results && $results->rowCount() > 0): ?>
        <div class="products-grid">
            <?php while ($p = $results->fetch()): ?>
                <div class="product-card">
                    <div class="product-card__img-wrap"><img src="/uploads/<?php echo h($p['image']); ?>" class="product-card__img"></div>
                    <div class="product-card__body">
                        <p class="product-card__cat"><?php echo isset($p['category_name'])?h($p['category_name']):'Спецодежда'; ?></p>
                        <h3 class="product-card__title"><a href="product.php?id=<?php echo $p['id']; ?>"><?php echo h($p['name']); ?></a></h3>
                        <p class="product-card__price"><?php echo formatPrice($p['price']); ?></p>
                        <a href="/ajax/add_to_cart.php?id=<?php echo $p['id']; ?>" class="btn btn--accent btn--sm">В корзину</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state"><div class="empty-state__icon">😔</div><h3>Ничего не найдено</h3></div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>