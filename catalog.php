<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

$category_slug = isset($_GET['category']) ? $_GET['category'] : '';
$category_name = 'Все товары';

if ($category_slug != '') {
    $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE slug = ?");
    $stmt->execute(array($category_slug));
    $cat = $stmt->fetch();
    if ($cat) {
        $category_name = $cat['name'];
        $stmt2 = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 AND p.category_id = ? ORDER BY p.id DESC");
        $stmt2->execute(array($cat['id']));
        $products = $stmt2;
    } else {
        $products = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.id DESC");
    }
} else {
    $products = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.id DESC");
}
?>

<div class="container">
    <div class="main-layout">

        <aside class="sidebar">
            <h2 class="sidebar__title">Категории</h2>
            <a href="catalog.php" class="sidebar__link<?php if($category_slug == '') echo ' active'; ?>">Все товары</a>
            <?php
            $cats = $pdo->query("SELECT * FROM categories ORDER BY name");
            while ($c = $cats->fetch()):
                $active = ($category_slug == $c['slug']) ? ' active' : '';
            ?>
                <a href="catalog.php?category=<?php echo h($c['slug']); ?>" class="sidebar__link<?php echo $active; ?>">
                    <?php echo h($c['name']); ?>
                </a>
            <?php endwhile; ?>
        </aside>

        <div class="main-content">
            <h2 class="section-title"><?php echo h($category_name); ?></h2>

            <?php if ($products && $products->rowCount() > 0): ?>
                <div class="products-grid">
                    <?php while ($p = $products->fetch()): ?>
                        <div class="product-card">
                            <div class="product-card__img-wrap">
                                <img src="/uploads/<?php echo h($p['image']); ?>" alt="<?php echo h($p['name']); ?>" class="product-card__img" onerror="this.src='/assets/images/placeholder.jpg'">
                            </div>
                            <div class="product-card__body">
                                <p class="product-card__cat"><?php echo isset($p['category_name']) ? h($p['category_name']) : 'Спецодежда'; ?></p>
                                <h3 class="product-card__title"><a href="product.php?id=<?php echo $p['id']; ?>"><?php echo h($p['name']); ?></a></h3>
                                <p class="product-card__price"><?php echo formatPrice($p['price']); ?></p>
                                <div class="product-card__actions">
                                    <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn--outline btn--sm">Подробнее</a>
                                    <a href="/ajax/add_to_cart.php?id=<?php echo $p['id']; ?>" class="btn btn--accent btn--sm">В корзину</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state__icon">📭</div>
                    <h3>Товаров в этой категории нет</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>