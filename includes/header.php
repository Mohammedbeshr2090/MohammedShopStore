<?php if(!isset($pageTitle)) $pageTitle = 'Mohammed Shop';
// Determine base path
$basePath = isset($subdir) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="<?= getLang() ?>" dir="<?= getDir() ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($pageDesc) ? sanitize($pageDesc) : t('متجر محمد - أفضل الأجهزة الإلكترونية', 'Mohammed Shop - Best Electronics Store') ?>">
    <title><?= sanitize($pageTitle) ?> | Mohammed Shop</title>
    <link rel="stylesheet" href="<?= $basePath ?>css/style.css">
    <?php if(isset($extraCSS)): ?>
    <link rel="stylesheet" href="<?= $basePath ?><?= $extraCSS ?>">
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<?php
$flash = getFlash();
if ($flash): ?>
<div class="flash-message <?= $flash['type'] ?>">
    <?= $flash['message'] ?>
</div>
<?php endif; ?>

<header class="header" id="header">
    <a href="<?= $basePath ?>index.php" class="logo">
        Mohammed<span>Shop</span>
    </a>

    <form action="<?= $basePath ?>search.php" method="GET" class="search-form">
        <input type="search" name="q" placeholder="<?= t('ابحث عن منتجات...', 'Search products...') ?>" value="<?= isset($_GET['q']) ? sanitize($_GET['q']) : '' ?>">
        <button type="submit">🔍</button>
    </form>

    <nav class="nav-links" id="navLinks">
        <a href="<?= $basePath ?>index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"><?= t('الرئيسية', 'Home') ?></a>
        <a href="<?= $basePath ?>products.php" class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>"><?= t('المنتجات', 'Products') ?></a>
        <a href="<?= $basePath ?>about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>"><?= t('من نحن', 'About') ?></a>
        <a href="<?= $basePath ?>contact.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>"><?= t('اتصل بنا', 'Contact') ?></a>
    </nav>

    <div class="header-icons">
        <!-- Language Toggle -->
        <button onclick="switchLang()" class="lang-toggle"><?= getLang() === 'ar' ? 'EN' : 'عربي' ?></button>

        <!-- Cart -->
        <a href="<?= $basePath ?>cart.php" title="<?= t('سلة التسوق', 'Cart') ?>">
            🛒
            <?php if(isLoggedIn() && getCartCount() > 0): ?>
            <span class="cart-badge" id="cartBadge"><?= getCartCount() ?></span>
            <?php endif; ?>
        </a>

        <!-- User -->
        <?php if(isLoggedIn()): ?>
        <div class="user-dropdown">
            <button>👤</button>
            <div class="dropdown-menu">
                <a href="#"><?= t('مرحباً', 'Hello') ?>, <?= sanitize($_SESSION['user_name']) ?></a>
                <?php if(isAdmin()): ?>
                <a href="<?= $basePath ?>admin/index.php">🎛️ <?= t('لوحة التحكم', 'Dashboard') ?></a>
                <?php endif; ?>
                <a href="<?= $basePath ?>auth/logout.php">🚪 <?= t('تسجيل الخروج', 'Logout') ?></a>
            </div>
        </div>
        <?php else: ?>
        <a href="<?= $basePath ?>login.php" title="<?= t('تسجيل الدخول', 'Login') ?>">👤</a>
        <?php endif; ?>

        <!-- Mobile Menu -->
        <button class="menu-btn" id="menuBtn" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
