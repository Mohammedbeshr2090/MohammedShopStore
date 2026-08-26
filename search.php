<?php
require_once 'includes/functions.php';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
    redirect('search.php?q=' . urlencode($_GET['q'] ?? ''));
}

$q = sanitize($_GET['q'] ?? '');
$pageTitle = t('نتائج البحث', 'Search Results');

$products = [];
$total = 0;

if (!empty($q)) {
    $searchTerm = '%' . $q . '%';
    $stmt = $conn->prepare("SELECT p.*, c.name as cat_name, c.name_ar as cat_name_ar 
                             FROM products p 
                             LEFT JOIN categories c ON p.category_id = c.id 
                             WHERE p.status = 1 AND (p.name LIKE ? OR p.name_ar LIKE ? OR p.description LIKE ? OR p.description_ar LIKE ?) 
                             ORDER BY p.featured DESC, p.created_at DESC");
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $total = count($products);
}

require_once 'includes/header.php';
?>

<section class="search-section">
    <div class="container">
        <!-- Search Bar -->
        <div style="max-width:60rem;margin:0 auto 4rem;">
            <form action="search.php" method="GET" class="search-form" style="width:100%;height:5.5rem;border-radius:var(--radius-lg);">
                <input type="search" name="q" value="<?= $q ?>" placeholder="<?= t('ابحث عن منتجات...','Search products...') ?>" style="font-size:1.6rem;">
                <button type="submit" style="font-size:2rem;">🔍</button>
            </form>
        </div>

        <?php if(!empty($q)): ?>
        <div class="search-header">
            <h2><?= t('نتائج البحث عن:', 'Search results for:') ?> <span>"<?= $q ?>"</span></h2>
            <p><?= t("تم العثور على $total منتج", "Found $total product(s)") ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($products)): ?>
        <div class="products-grid" style="margin-top:3rem;">
            <?php foreach($products as $product):
                $discount = $product['old_price'] ? round((($product['old_price'] - $product['price']) / $product['old_price']) * 100) : 0;
            ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if($discount): ?><span class="product-badge">-<?= $discount ?>%</span><?php endif; ?>
                    <img src="<?= sanitize($product['image']) ?>" class="main-img" alt="<?= sanitize($product['name']) ?>">
                    <?php if($product['hover_image']): ?><img src="<?= sanitize($product['hover_image']) ?>" class="hover-img" alt=""><?php endif; ?>
                    <div class="product-actions">
                        <a href="product_details.php?id=<?= $product['id'] ?>">👁️</a>
                        <button onclick="addToCart(<?= $product['id'] ?>)">🛒</button>
                    </div>
                </div>
                <div class="product-info">
                    <span class="product-category"><?= t(sanitize($product['cat_name_ar'] ?? ''), sanitize($product['cat_name'] ?? '')) ?></span>
                    <h3 class="product-name"><a href="product_details.php?id=<?= $product['id'] ?>"><?= t(sanitize($product['name_ar'] ?? $product['name']), sanitize($product['name'])) ?></a></h3>
                    <div class="product-price">
                        <span class="current"><?= formatPrice($product['price']) ?></span>
                        <?php if($product['old_price']): ?><span class="old"><?= formatPrice($product['old_price']) ?></span><?php endif; ?>
                    </div>
                    <button class="add-cart-btn" onclick="addToCart(<?= $product['id'] ?>)">🛒 <?= t('أضف للسلة','Add to Cart') ?></button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php elseif(!empty($q)): ?>
        <div class="empty-state">
            <div class="empty-icon">🔍</div>
            <h3><?= t('لا توجد نتائج', 'No Results Found') ?></h3>
            <p><?= t("لم يتم العثور على منتجات لـ \"$q\"", "No products found for \"$q\"") ?></p>
            <a href="products.php" class="btn btn-primary"><?= t('عرض جميع المنتجات','View All Products') ?></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
