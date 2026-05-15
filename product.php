<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
session_start();

if (!isset($_GET['id'])) redirect('catalog.php');

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.is_active = 1");
$stmt->execute(array($id));
$product = $stmt->fetch();

if (!$product) redirect('catalog.php');

// Отзывы
$reviews = $pdo->prepare("SELECT r.*, u.full_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$reviews->execute(array($id));

// Добавление отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    if (!isLoggedIn()) redirect('login.php');
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute(array($_SESSION['user_id'], $id, $rating, $comment));
    // Обновить рейтинг товара
    $avg = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as cnt FROM reviews WHERE product_id = ?");
    $avg->execute(array($id));
    $r = $avg->fetch();
    $pdo->prepare("UPDATE products SET rating = ?, reviews_count = ? WHERE id = ?")->execute(array($r['avg_rating'], $r['cnt'], $id));
    redirect("product.php?id=$id");
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="product-detail">
        <img src="/uploads/<?php echo h($product['image']); ?>" alt="<?php echo h($product['name']); ?>" class="product-detail__image" onerror="this.src='/assets/images/placeholder.jpg'">
        <div class="product-detail__info">
            <p style="color:var(--primary);text-transform:uppercase;font-size:12px;font-weight:600;letter-spacing:1px;"><?php echo h($product['category_name'] ?? 'Спецодежда'); ?></p>
            <h1><?php echo h($product['name']); ?></h1>
            <div style="margin:10px 0;">
                <?php for($i=1;$i<=5;$i++): ?>
                    <span class="star<?php echo $i <= round($product['rating']) ? '' : ' empty'; ?>">★</span>
                <?php endfor; ?>
                <small>(<?php echo $product['reviews_count']; ?> отзывов)</small>
            </div>
            <p class="product-detail__price"><?php echo formatPrice($product['price']); ?></p>
            <p class="product-detail__desc"><?php echo nl2br(h($product['description'])); ?></p>
            <?php if($product['size']): ?><p><strong>Размеры:</strong> <?php echo h($product['size']); ?></p><?php endif; ?>
            <?php if($product['color']): ?><p><strong>Цвет:</strong> <?php echo h($product['color']); ?></p><?php endif; ?>
            <?php if($product['season']): ?><p><strong>Сезон:</strong> <?php echo h($product['season']); ?></p><?php endif; ?>
            <?php if($product['material']): ?><p><strong>Материал:</strong> <?php echo h($product['material']); ?></p><?php endif; ?>
            <p><strong>В наличии:</strong> <?php echo $product['stock']; ?> шт.</p>
            <div class="product-detail__actions" style="margin-top:20px;">
                <a href="/ajax/add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn btn--accent">В корзину</a>
                <a href="/ajax/add_favorite.php?id=<?php echo $product['id']; ?>" class="btn btn--outline">❤️ В избранное</a>
            </div>
        </div>
    </div>

    <!-- Отзывы -->
    <h2 class="section-title">Отзывы (<?php echo $product['reviews_count']; ?>)</h2>
    <div class="reviews-list">
        <?php while($rev = $reviews->fetch()): ?>
            <div class="review-item">
                <strong><?php echo h($rev['full_name']); ?></strong>
                <span style="margin-left:10px;">
                    <?php for($i=1;$i<=5;$i++): ?>
                        <span class="star<?php echo $i <= $rev['rating'] ? '' : ' empty'; ?>">★</span>
                    <?php endfor; ?>
                </span>
                <p><?php echo nl2br(h($rev['comment'])); ?></p>
                <small><?php echo date('d.m.Y', strtotime($rev['created_at'])); ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if(isLoggedIn()): ?>
        <form method="POST" style="margin-top:30px;background:#fff;padding:24px;border-radius:10px;border:1px solid #eef0f4;">
            <h3 style="margin-bottom:15px;">Оставить отзыв</h3>
            <div class="form-group">
                <label>Оценка</label>
                <select name="rating">
                    <option value="5">5 - Отлично</option>
                    <option value="4">4 - Хорошо</option>
                    <option value="3">3 - Нормально</option>
                    <option value="2">2 - Плохо</option>
                    <option value="1">1 - Ужасно</option>
                </select>
            </div>
            <div class="form-group">
                <label>Комментарий</label>
                <textarea name="comment" rows="3"></textarea>
            </div>
            <button type="submit" name="add_review" class="btn btn--primary">Отправить</button>
        </form>
    <?php else: ?>
        <p style="text-align:center;margin-top:20px;"><a href="login.php">Войдите</a> чтобы оставить отзыв</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>