<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

$popular = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.id DESC LIMIT 8");
?>

<div class="container">
    <div class="main-layout">

        <aside class="sidebar">
            <h2 class="sidebar__title">Каталог</h2>
            <?php
            $cats = $pdo->query("SELECT * FROM categories ORDER BY name");
            while ($cat = $cats->fetch()):
                $active = (isset($_GET['category']) && $_GET['category'] == $cat['slug']) ? 'active' : '';
            ?>
                <a href="catalog.php?category=<?php echo h($cat['slug']); ?>" class="sidebar__link <?php echo $active; ?>">
                    <?php echo h($cat['name']); ?>
                </a>
            <?php endwhile; ?>
        </aside>

        <div class="main-content">

            <div class="hero-banner">
                <div class="hero-banner__content">
                    <span class="hero-banner__badge">Premium Quality</span>
                    <h1>СПЕЦ<span>ОДЕЖДА</span></h1>
                    <p>Надёжная экипировка для профессионалов. ГОСТ, прочность, комфорт.</p>
                    <a href="catalog.php" class="btn btn--white">Перейти в каталог</a>
                </div>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <span class="feature-card__icon">🛡️</span>
                    <h3>ГОСТ сертификация</h3>
                    <p>Продукция соответствует стандартам качества РФ</p>
                </div>
                <div class="feature-card">
                    <span class="feature-card__icon">🚚</span>
                    <h3>Доставка по РФ</h3>
                    <p>Отправка во все регионы России</p>
                </div>
                <div class="feature-card">
                    <span class="feature-card__icon">🔄</span>
                    <h3>Обмен и возврат</h3>
                    <p>14 дней на примерку и обмен</p>
                </div>
                <div class="feature-card">
                    <span class="feature-card__icon">💯</span>
                    <h3>Гарантия качества</h3>
                    <p>Проверенные материалы и фурнитура</p>
                </div>
            </div>

            <h2 class="section-title">Популярные товары</h2>

            <?php if ($popular && $popular->rowCount() > 0): ?>
                <div class="products-grid">
                    <?php while ($p = $popular->fetch()): ?>
                        <div class="product-card">
                            <div class="product-card__img-wrap">
                                <img src="/uploads/<?php echo h($p['image']); ?>" alt="<?php echo h($p['name']); ?>" class="product-card__img" onerror="this.src='/assets/images/placeholder.jpg'">
                                <span class="product-card__badge">NEW</span>
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
                    <div class="empty-state__icon">📦</div>
                    <h3>Товары скоро появятся</h3>
                    <p>Добавьте товары через админ-панель</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>