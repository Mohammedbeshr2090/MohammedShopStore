<?php
require_once 'includes/functions.php';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    redirect($url);
}

$pageTitle = t('المنتجات', 'Products');

// Filter by category
$categoryFilter = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

// Pagination
$perPage = 12;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Build query
$where = "WHERE p.status = 1";
$params = [];
$types = "";

if ($categoryFilter > 0) {
    $where .= " AND p.category_id = ?";
    $params[] = $categoryFilter;
    $types .= "i";
}

// Get total count
$countStmt = $conn->prepare("SELECT COUNT(*) as total FROM products p $where");
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalProducts = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalProducts / $perPage);

// Get products
$sql = "SELECT p.*, c.name as cat_name, c.name_ar as cat_name_ar 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $where 
        ORDER BY p.created_at DESC 
        LIMIT $perPage OFFSET $offset";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// Get all categories for filter
$allCategories = $conn->query("SELECT * FROM categories WHERE status = 1 ORDER BY name_ar ASC");

// Get active category name
$activeCatName = '';
if ($categoryFilter > 0) {
    $catStmt = $conn->prepare("SELECT name, name_ar FROM categories WHERE id = ?");
    $catStmt->bind_param("i", $categoryFilter);
    $catStmt->execute();
    $catResult = $catStmt->get_result();
    if ($catRow = $catResult->fetch_assoc()) {
        $activeCatName = t($catRow['name_ar'], $catRow['name']);
    }
}

require_once 'includes/header.php';
?>

<!-- Categories -->
<section class="section-padding" style="padding-top: 10rem;">
    <div class="container">
        <div class="section-heading">
            <h2><?= t('تسوق حسب', 'Shop by') ?> <span><?= t('الفئات', 'Categories') ?></span></h2>
        </div>
        <div class="categories-grid">
            <a href="products.php" class="category-card <?= $categoryFilter === 0 ? 'active' : '' ?>" style="<?= $categoryFilter === 0 ? 'border-color: var(--primary);' : '' ?>">
                <h3><?= t('الكل', 'All') ?></h3>
            </a>
            <?php while($cat = $allCategories->fetch_assoc()): ?>
            <a href="products.php?cat=<?= $cat['id'] ?>" class="category-card" style="<?= $categoryFilter == $cat['id'] ? 'border-color: var(--primary);' : '' ?>">
                <?php if($cat['image']): ?>
                <img src="<?= sanitize($cat['image']) ?>" alt="<?= sanitize($cat['name']) ?>">
                <?php endif; ?>
                <h3><?= t(sanitize($cat['name_ar']), sanitize($cat['name'])) ?></h3>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Products -->
<section class="section-padding" style="padding-top: 0;">
    <div class="container">
        <div class="section-heading">
            <h2><?= $activeCatName ? $activeCatName : t('جميع', 'All') ?> <span><?= t('المنتجات', 'Products') ?></span></h2>
            <p><?= $totalProducts ?> <?= t('منتج', 'products') ?></p>
        </div>

        <?php if($products->num_rows > 0): ?>
        <div class="products-grid">
            <?php while($product = $products->fetch_assoc()): 
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

        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
            <a href="?<?= $categoryFilter ? 'cat='.$categoryFilter.'&' : '' ?>page=<?= $page-1 ?>">←</a>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?= $categoryFilter ? 'cat='.$categoryFilter.'&' : '' ?>page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>" <?= $i == $page ? 'style="background:var(--primary);color:#fff;border-color:var(--primary);"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if($page < $totalPages): ?>
            <a href="?<?= $categoryFilter ? 'cat='.$categoryFilter.'&' : '' ?>page=<?= $page+1 ?>">→</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📦</div>
            <h3><?= t('لا توجد منتجات', 'No products found') ?></h3>
            <p><?= t('لم يتم العثور على منتجات في هذه الفئة', 'No products were found in this category') ?></p>
            <a href="products.php" class="btn btn-primary"><?= t('عرض جميع المنتجات', 'View All Products') ?></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
