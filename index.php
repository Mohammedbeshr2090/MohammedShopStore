<?php
require_once 'includes/functions.php';

// Handle language switch
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    redirect($url);
}

$pageTitle = t('الرئيسية', 'Home');
$pageDesc = t('متجر محمد - أفضل الأجهزة الإلكترونية', 'Mohammed Shop - Best Electronics');

// Get featured products
$featuredProducts = $conn->query("SELECT p.*, c.name as cat_name, c.name_ar as cat_name_ar FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 AND p.status = 1 ORDER BY p.created_at DESC LIMIT 6");

// Get categories
$categories = $conn->query("SELECT * FROM categories WHERE status = 1 ORDER BY id ASC LIMIT 7");

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <span class="badge">🔥 <?= t('خصم يصل إلى 30%', 'Up to 30% OFF') ?></span>
            <h1><?= t('اكتشف أحدث', 'Discover the Latest') ?> <span><?= t('الأجهزة الإلكترونية', 'Electronics') ?></span></h1>
            <p><?= t('تسوق أفضل الهواتف الذكية والساعات والسماعات بأسعار لا تقاوم مع خدمة توصيل سريعة.', 'Shop the best smartphones, watches, and headphones at unbeatable prices with fast delivery.') ?></p>
            <div class="hero-btns">
                <a href="products.php" class="btn btn-primary btn-lg"><?= t('تسوق الآن', 'Shop Now') ?> →</a>
                <a href="about.php" class="btn btn-secondary btn-lg"><?= t('تعرف علينا', 'About Us') ?></a>
            </div>
        </div>
        <div class="hero-image">
            <img src="img/iphone 15 ¹.jpg" alt="iPhone 15 Pro Max">
        </div>
    </div>
</section>

<!-- Banner Section -->
<section class="banner-section">
    <div class="container">
        <div class="banner-grid">
            <a href="products.php?cat=3" class="banner-card">
                <img src="img/سماعات 1.jpg" alt="Headphones">
                <div class="banner-overlay">
                    <span><?= t('عرض خاص', 'Special Offer') ?></span>
                    <h3><?= t('سماعات بخصم 30%', 'Headphones 30% OFF') ?></h3>
                </div>
            </a>
            <a href="products.php?cat=2" class="banner-card">
                <img src="img/product-6.jpg" alt="Smartwatch">
                <div class="banner-overlay">
                    <span><?= t('عرض خاص', 'Special Offer') ?></span>
                    <h3><?= t('ساعات ذكية بخصم 25%', 'Smartwatches 25% OFF') ?></h3>
                </div>
            </a>
            <a href="products.php?cat=4" class="banner-card">
                <img src="img/mr1.jpg" alt="Speakers">
                <div class="banner-overlay">
                    <span><?= t('عرض خاص', 'Special Offer') ?></span>
                    <h3><?= t('مكبرات صوت بخصم 40%', 'Speakers 40% OFF') ?></h3>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section-padding">
    <div class="container">
        <div class="section-heading">
            <h2><?= t('تسوق حسب', 'Shop by') ?> <span><?= t('الفئات', 'Categories') ?></span></h2>
            <p><?= t('اختر الفئة التي تناسبك', 'Choose the category that suits you') ?></p>
        </div>
        <div class="categories-grid">
            <?php while($cat = $categories->fetch_assoc()): ?>
            <a href="products.php?cat=<?= $cat['id'] ?>" class="category-card">
                <img src="<?= sanitize($cat['image']) ?>" alt="<?= sanitize($cat['name']) ?>">
                <h3><?= t(sanitize($cat['name_ar']), sanitize($cat['name'])) ?></h3>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section-padding" style="padding-top: 0;">
    <div class="container">
        <div class="section-heading">
            <h2><?= t('منتجات', 'Featured') ?> <span><?= t('مميزة', 'Products') ?></span></h2>
            <p><?= t('أحدث المنتجات المختارة لك', 'Latest handpicked products for you') ?></p>
        </div>
        <div class="products-grid">
            <?php while($product = $featuredProducts->fetch_assoc()): 
                $discount = $product['old_price'] ? round((($product['old_price'] - $product['price']) / $product['old_price']) * 100) : 0;
            ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if($discount > 0): ?>
                    <span class="product-badge">-<?= $discount ?>%</span>
                    <?php endif; ?>
                    <img src="<?= sanitize($product['image']) ?>" class="main-img" alt="<?= sanitize($product['name']) ?>">
                    <?php if($product['hover_image']): ?>
                    <img src="<?= sanitize($product['hover_image']) ?>" class="hover-img" alt="<?= sanitize($product['name']) ?>">
                    <?php endif; ?>
                    <div class="product-actions">
                        <a href="product_details.php?id=<?= $product['id'] ?>" title="<?= t('عرض التفاصيل', 'View Details') ?>">👁️</a>
                        <button onclick="addToCart(<?= $product['id'] ?>)" title="<?= t('أضف للسلة', 'Add to Cart') ?>">🛒</button>
                    </div>
                </div>
                <div class="product-info">
                    <span class="product-category"><?= t(sanitize($product['cat_name_ar'] ?? ''), sanitize($product['cat_name'] ?? '')) ?></span>
                    <h3 class="product-name">
                        <a href="product_details.php?id=<?= $product['id'] ?>"><?= t(sanitize($product['name_ar'] ?? $product['name']), sanitize($product['name'])) ?></a>
                    </h3>
                    <div class="product-price">
                        <span class="current"><?= formatPrice($product['price']) ?></span>
                        <?php if($product['old_price']): ?>
                        <span class="old"><?= formatPrice($product['old_price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <button class="add-cart-btn" onclick="addToCart(<?= $product['id'] ?>)">
                        🛒 <?= t('أضف للسلة', 'Add to Cart') ?>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center" style="margin-top: 4rem;">
            <a href="products.php" class="btn btn-primary btn-lg"><?= t('عرض جميع المنتجات', 'View All Products') ?> →</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
