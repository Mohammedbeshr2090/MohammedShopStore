<?php
require_once 'includes/functions.php';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
    redirect('product_details.php?id=' . (int)$_GET['id']);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) redirect('products.php');

$stmt = $conn->prepare("SELECT p.*, c.name as cat_name, c.name_ar as cat_name_ar 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         WHERE p.id = ? AND p.status = 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) redirect('products.php');

$discount = $product['old_price'] ? round((($product['old_price'] - $product['price']) / $product['old_price']) * 100) : 0;
$pageTitle = t(sanitize($product['name_ar'] ?? $product['name']), sanitize($product['name']));

// Related products
$related = $conn->prepare("SELECT p.*, c.name as cat_name, c.name_ar as cat_name_ar FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? AND p.status = 1 LIMIT 4");
$related->bind_param("ii", $product['category_id'], $id);
$related->execute();
$relatedProducts = $related->get_result();

require_once 'includes/header.php';
?>

<section class="product-details-section">
    <div class="container">
        <!-- Breadcrumb -->
        <nav style="margin-bottom:2rem;font-size:1.4rem;color:var(--text-muted);">
            <a href="index.php" style="color:var(--text-muted);"><?= t('الرئيسية','Home') ?></a> /
            <a href="products.php" style="color:var(--text-muted);"><?= t('المنتجات','Products') ?></a> /
            <span style="color:var(--primary-light);"><?= sanitize($product['name']) ?></span>
        </nav>

        <div class="product-details-grid">
            <!-- Gallery -->
            <div class="product-gallery" id="mainGallery">
                <img src="<?= sanitize($product['image']) ?>" alt="<?= sanitize($product['name']) ?>" id="mainImg" style="max-height:40rem;object-fit:contain;">
                <?php if($product['hover_image']): ?>
                <div style="display:flex;gap:1rem;justify-content:center;margin-top:2rem;">
                    <img src="<?= sanitize($product['image']) ?>" onclick="switchImg(this)" style="width:7rem;height:7rem;object-fit:contain;border-radius:var(--radius);border:2px solid var(--primary);padding:0.5rem;cursor:pointer;background:var(--bg-surface);">
                    <img src="<?= sanitize($product['hover_image']) ?>" onclick="switchImg(this)" style="width:7rem;height:7rem;object-fit:contain;border-radius:var(--radius);border:1px solid var(--border);padding:0.5rem;cursor:pointer;background:var(--bg-surface);">
                </div>
                <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="product-detail-info">
                <span class="detail-category">
                    📁 <?= t(sanitize($product['cat_name_ar'] ?? ''), sanitize($product['cat_name'] ?? '')) ?>
                </span>
                <h1><?= t(sanitize($product['name_ar'] ?? $product['name']), sanitize($product['name'])) ?></h1>

                <div class="detail-price">
                    <span class="current"><?= formatPrice($product['price']) ?></span>
                    <?php if($product['old_price']): ?>
                    <span class="old"><?= formatPrice($product['old_price']) ?></span>
                    <span class="discount">-<?= $discount ?>%</span>
                    <?php endif; ?>
                </div>

                <p class="detail-desc">
                    <?= t(sanitize($product['description_ar'] ?? $product['description'] ?? ''), sanitize($product['description'] ?? '')) ?>
                </p>

                <div class="stock-info <?= $product['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
                    <?= $product['stock'] > 0 ? '✅ ' . t('متوفر في المخزون','In Stock') . ' (' . $product['stock'] . ')' : '❌ ' . t('غير متوفر','Out of Stock') ?>
                </div>

                <?php if($product['stock'] > 0): ?>
                <div class="detail-quantity">
                    <label><?= t('الكمية:','Quantity:') ?></label>
                    <div class="quantity-controls">
                        <button onclick="changeQty(-1)">−</button>
                        <span class="qty-value" id="detailQty">1</span>
                        <button onclick="changeQty(1)">+</button>
                    </div>
                </div>
                <div class="detail-actions">
                    <button class="btn btn-primary btn-lg" onclick="addToCartQty(<?= $product['id'] ?>)">
                        🛒 <?= t('أضف للسلة','Add to Cart') ?>
                    </button>
                    <a href="cart.php" class="btn btn-secondary btn-lg">
                        <?= t('عرض السلة','View Cart') ?>
                    </a>
                </div>
                <?php else: ?>
                <button class="btn btn-secondary btn-lg" disabled><?= t('غير متوفر','Out of Stock') ?></button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Related Products -->
        <?php if($relatedProducts->num_rows > 0): ?>
        <div style="margin-top:6rem;">
            <div class="section-heading">
                <h2><?= t('منتجات', 'Related') ?> <span><?= t('مشابهة', 'Products') ?></span></h2>
            </div>
            <div class="products-grid">
                <?php while($p = $relatedProducts->fetch_assoc()): 
                    $d = $p['old_price'] ? round((($p['old_price'] - $p['price']) / $p['old_price']) * 100) : 0;
                ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if($d): ?><span class="product-badge">-<?= $d ?>%</span><?php endif; ?>
                        <img src="<?= sanitize($p['image']) ?>" class="main-img" alt="<?= sanitize($p['name']) ?>">
                        <?php if($p['hover_image']): ?><img src="<?= sanitize($p['hover_image']) ?>" class="hover-img" alt=""><?php endif; ?>
                        <div class="product-actions">
                            <a href="product_details.php?id=<?= $p['id'] ?>">👁️</a>
                            <button onclick="addToCart(<?= $p['id'] ?>)">🛒</button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><a href="product_details.php?id=<?= $p['id'] ?>"><?= t(sanitize($p['name_ar'] ?? $p['name']), sanitize($p['name'])) ?></a></h3>
                        <div class="product-price">
                            <span class="current"><?= formatPrice($p['price']) ?></span>
                            <?php if($p['old_price']): ?><span class="old"><?= formatPrice($p['old_price']) ?></span><?php endif; ?>
                        </div>
                        <button class="add-cart-btn" onclick="addToCart(<?= $p['id'] ?>)">🛒 <?= t('أضف للسلة','Add to Cart') ?></button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function switchImg(el) {
    document.getElementById('mainImg').src = el.src;
}
function changeQty(delta) {
    const el = document.getElementById('detailQty');
    let v = parseInt(el.textContent) + delta;
    if (v < 1) v = 1;
    if (v > <?= $product['stock'] ?>) v = <?= $product['stock'] ?>;
    el.textContent = v;
}
function addToCartQty(pid) {
    const qty = parseInt(document.getElementById('detailQty').textContent);
    fetch('cart/add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + pid + '&quantity=' + qty
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.cart_count;
        } else {
            if (data.redirect) window.location.href = data.redirect;
            else showToast(data.message, 'error');
        }
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
