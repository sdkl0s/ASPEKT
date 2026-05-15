<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/functions.php';

$cart_count = getCartCount();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPECIALWEAR — Спецодежда</title>
    <style>
        :root {
            --bg:#f2f3f5; --surface:#fff; --header-bg:#1e293b;
            --primary:#3b5998; --primary-hover:#2d4373;
            --accent:#c8a45c; --accent-hover:#b8933f;
            --text:#1a1a1a; --text-secondary:#5a5f6b;
            --border:#dde1e7; --border-light:#eef0f4;
            --shadow:0 1px 3px rgba(0,0,0,0.06);
            --font:'Segoe UI',Arial,sans-serif;
        }
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:var(--font);background:var(--bg);color:var(--text);line-height:1.6}
        .container{max-width:1400px;margin:0 auto;padding:0 20px}

        .header{background:var(--header-bg);position:sticky;top:0;z-index:1000;box-shadow:0 2px 8px rgba(0,0,0,0.15)}
        .header__inner{display:flex;align-items:center;justify-content:space-between;padding:14px 0;gap:20px;flex-wrap:wrap}
        .logo{font-size:28px;font-weight:700;text-decoration:none;letter-spacing:3px;color:#fff;text-transform:uppercase}
        .logo span{color:var(--accent)}

        .header__search{flex:1;max-width:400px;min-width:200px}
        .search-form{display:flex;border-radius:6px;overflow:hidden;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2)}
        .search-form input{flex:1;padding:10px 16px;background:0;border:0;color:#fff;font-size:14px;outline:0}
        .search-form input::placeholder{color:rgba(255,255,255,0.55)}
        .search-form button{padding:10px 18px;background:var(--accent);border:0;color:#1a1a1a;font-weight:700;font-size:13px;cursor:pointer;text-transform:uppercase}
        .search-form button:hover{background:var(--accent-hover)}

        .nav__list{list-style:none;display:flex;align-items:center;gap:4px;flex-wrap:wrap}
        .nav__link{color:rgba(255,255,255,0.78);text-decoration:none;font-weight:500;font-size:14px;padding:8px 14px;border-radius:6px;white-space:nowrap}
        .nav__link:hover{color:#fff;background:rgba(255,255,255,0.08)}
        .nav__link.active{color:#fff;background:rgba(255,255,255,0.12);font-weight:600}
        .nav__link.cart-link{color:var(--accent)!important;font-weight:700}
        .cart-count{background:var(--accent);color:#1a1a1a;font-size:11px;font-weight:800;padding:2px 7px;border-radius:12px;margin-left:5px}

        .btn{display:inline-block;padding:12px 26px;font-weight:600;font-size:14px;text-decoration:none;border-radius:6px;border:0;cursor:pointer;text-align:center}
        .btn--primary{background:var(--primary);color:#fff}.btn--primary:hover{background:var(--primary-hover)}
        .btn--accent{background:var(--accent);color:#1a1a1a;font-weight:700}.btn--accent:hover{background:var(--accent-hover)}
        .btn--outline{background:0;border:2px solid var(--primary);color:var(--primary)}.btn--outline:hover{background:var(--primary);color:#fff}
        .btn--white{background:#fff;color:#1a1a1a;border:1px solid var(--border)}.btn--white:hover{background:#f7f8fa}
        .btn--sm{padding:8px 16px;font-size:12px}
        .btn--danger{background:#c0392b;color:#fff}.btn--danger:hover{background:#a93226}

        .main-layout{display:flex;gap:30px;margin:40px auto}
        .sidebar{width:260px;flex-shrink:0;background:var(--surface);border-radius:10px;padding:24px;box-shadow:var(--shadow);height:fit-content;position:sticky;top:90px;border:1px solid var(--border-light)}
        .sidebar__title{font-size:20px;font-weight:700;margin-bottom:18px;padding-bottom:12px;border-bottom:2px solid var(--primary)}
        .sidebar__link{display:block;padding:11px 14px;color:var(--text-secondary);text-decoration:none;font-size:14px;border-radius:6px;margin-bottom:2px}
        .sidebar__link:hover{background:#f0f3f8;color:var(--primary)}
        .sidebar__link.active{background:#e8edf6;color:var(--primary);font-weight:600}
        .main-content{flex:1;min-width:0}

        .hero-banner{height:460px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;text-align:center;background:linear-gradient(135deg,#1e293b 0%,#334155 100%);margin-bottom:40px}
        .hero-banner__content{max-width:700px;padding:0 20px}
        .hero-banner__badge{display:inline-block;background:var(--accent);color:#1a1a1a;padding:6px 16px;border-radius:4px;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:20px}
        .hero-banner h1{font-size:48px;font-weight:700;text-transform:uppercase;margin-bottom:16px}
        .hero-banner h1 span{color:var(--accent)}
        .hero-banner p{font-size:18px;color:rgba(255,255,255,0.78);margin-bottom:30px}

        .features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;margin-bottom:50px}
        .feature-card{background:var(--surface);padding:28px;border-radius:10px;text-align:center;border:1px solid var(--border-light);box-shadow:var(--shadow)}
        .feature-card__icon{font-size:36px;margin-bottom:14px;display:block}
        .feature-card h3{font-size:16px;font-weight:700;margin-bottom:6px}
        .feature-card p{font-size:13px;color:var(--text-secondary)}

        .section-title{font-size:28px;font-weight:700;margin-bottom:28px;padding-left:16px;border-left:4px solid var(--primary)}

        .products-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:22px;margin-bottom:50px}
        .product-card{background:var(--surface);border-radius:10px;overflow:hidden;border:1px solid var(--border-light);box-shadow:var(--shadow);display:flex;flex-direction:column;transition:transform 0.3s}
        .product-card:hover{transform:translateY(-4px);box-shadow:0 10px 25px rgba(0,0,0,0.1)}
        .product-card__img-wrap{position:relative;height:240px;overflow:hidden;background:#e8ecf1}
        .product-card__img{width:100%;height:100%;object-fit:cover;transition:transform 0.5s}
        .product-card:hover .product-card__img{transform:scale(1.06)}
        .product-card__badge{position:absolute;top:12px;left:12px;background:var(--primary);color:#fff;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:700}
        .product-card__body{padding:18px;flex:1;display:flex;flex-direction:column}
        .product-card__cat{font-size:11px;color:var(--primary);text-transform:uppercase;font-weight:600;margin-bottom:4px}
        .product-card__title{font-size:15px;font-weight:700;margin-bottom:8px}
        .product-card__title a{color:var(--text);text-decoration:none}
        .product-card__title a:hover{color:var(--primary)}
        .product-card__price{font-size:20px;font-weight:800;color:var(--accent);margin-bottom:14px}
        .product-card__actions{margin-top:auto;display:flex;gap:8px}

        .form-container{max-width:520px;margin:50px auto;background:var(--surface);padding:36px;border-radius:12px;border:1px solid var(--border-light);box-shadow:var(--shadow)}
        .form-container--wide{max-width:860px}
        .form-title{font-size:26px;font-weight:700;margin-bottom:28px;text-align:center}
        .form-group{margin-bottom:18px}
        .form-group label{display:block;margin-bottom:5px;font-weight:600;font-size:13px;color:var(--text-secondary);text-transform:uppercase}
        .form-group input,.form-group textarea,.form-group select{width:100%;padding:11px 14px;background:#f8f9fb;border:1px solid var(--border);border-radius:6px;font-size:14px;font-family:var(--font);color:var(--text)}
        .form-group input:focus,.form-group textarea:focus{outline:0;border-color:var(--primary);background:#fff}

        .table-wrap{overflow-x:auto;border-radius:10px;box-shadow:var(--shadow)}
        .table{width:100%;border-collapse:collapse;background:var(--surface)}
        .table th{background:#f0f3f8;color:var(--text);padding:14px 16px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:1px;font-weight:700;border-bottom:2px solid var(--border)}
        .table td{padding:14px 16px;border-bottom:1px solid var(--border-light);font-size:14px}
        .table tbody tr:hover{background:#fafbfc}

        .alert{padding:14px 18px;border-radius:6px;margin-bottom:18px;font-weight:500;font-size:14px}
        .alert--success{background:#eafaf1;color:#1e7e34;border:1px solid #c3e6cb}
        .alert--error{background:#fdf2f2;color:#a71d2a;border:1px solid #f5c6cb}

        .cart-summary{background:var(--surface);padding:24px;border-radius:10px;border:1px solid var(--border-light);box-shadow:var(--shadow);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;margin-top:24px}
        .cart-total{font-size:24px;font-weight:800;color:var(--accent)}

        .empty-state{text-align:center;padding:60px 20px;color:var(--text-secondary);background:var(--surface);border-radius:10px;border:1px solid var(--border-light)}
        .empty-state__icon{font-size:48px;margin-bottom:16px}
        .empty-state h3{font-size:20px;color:var(--text);margin-bottom:8px}

        .footer{background:var(--header-bg);text-align:center;padding:28px;margin-top:80px;color:rgba(255,255,255,0.6);font-size:13px}

        .product-detail{display:grid;grid-template-columns:1fr 1fr;gap:40px;margin:40px 0;background:var(--surface);padding:40px;border-radius:12px;border:1px solid var(--border-light);box-shadow:var(--shadow)}
        .product-detail__image{width:100%;border-radius:8px;background:#e8ecf1}
        .product-detail__info h1{font-size:32px;font-weight:700;margin-bottom:10px}
        .product-detail__price{font-size:36px;font-weight:800;color:var(--accent);margin-bottom:20px}
        .product-detail__desc{color:var(--text-secondary);margin-bottom:20px;line-height:1.8}
        .product-detail__actions{display:flex;gap:10px;flex-wrap:wrap}

        .star{color:#f0a500;font-size:18px}
        .star.empty{color:#ddd}
        .reviews-list{margin-top:30px}
        .review-item{background:#f8f9fb;padding:15px;border-radius:8px;margin-bottom:10px}
        .review-item strong{color:var(--text)}
        .review-item small{color:var(--text-secondary)}

        @media(max-width:900px){
            .main-layout{flex-direction:column}
            .sidebar{width:100%;position:static}
            .header__inner{flex-direction:column;gap:12px}
            .header__search{max-width:100%;width:100%}
            .hero-banner{height:340px}
            .hero-banner h1{font-size:34px}
            .product-detail{grid-template-columns:1fr}
        }
    </style>
</head>
<body>
<header class="header">
    <div class="container header__inner">
        <a href="/index.php" class="logo">SPECIAL<span>WEAR</span></a>

        <div class="header__search">
            <form action="/search.php" method="get" class="search-form">
                <input type="text" name="query" placeholder="Поиск по каталогу..." required>
                <button type="submit">Поиск</button>
            </form>
        </div>

        <nav>
            <ul class="nav__list">
                <li><a href="/index.php" class="nav__link<?php if($current=='index.php') echo ' active'; ?>">Главная</a></li>
                <li><a href="/catalog.php" class="nav__link<?php if($current=='catalog.php') echo ' active'; ?>">Каталог</a></li>
                <?php if(isLoggedIn()): ?>
                    <li><a href="/cabinet.php" class="nav__link<?php if($current=='cabinet.php') echo ' active'; ?>">Кабинет</a></li>
                    <?php if(isAdmin()): ?>
                        <li><a href="/admin/index.php" class="nav__link">Админ</a></li>
                    <?php endif; ?>
                    <li><a href="/logout.php" class="nav__link">Выход</a></li>
                <?php else: ?>
                    <li><a href="/login.php" class="nav__link<?php if($current=='login.php') echo ' active'; ?>">Вход</a></li>
                <?php endif; ?>
                <li>
                    <a href="/cart.php" class="nav__link cart-link<?php if($current=='cart.php') echo ' active'; ?>">
                        Корзина <span class="cart-count"><?php echo $cart_count; ?></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>
<main>