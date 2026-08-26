<?php
require_once 'includes/functions.php';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
    redirect('cart.php');
}

if (!isLoggedIn()) {
    setFlash('info', t('يرجى تسجيل الدخول أولاً', 'Please login first'));
    redirect('login.php');
}

$pageTitle = t('سلة التسوق', 'Shopping Cart');

$stmt = $conn->prepare("SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.name_ar, p.price, p.old_price, p.image, p.stock 
                         FROM cart c 
                         JOIN products p ON c.product_id = p.id 
                         WHERE c.user_id = ? 
                         ORDER BY c.created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$cartItems = $stmt->get_result();

$subtotal = 0;
$cartData = [];
while ($item = $cartItems->fetch_assoc()) {
    $subtotal += $item['price'] * $item['quantity'];
    $cartData[] = $item;
}
$shipping = $subtotal > 0 ? 0 : 0; // Free shipping
$total = $subtotal + $shipping;

require_once 'includes/header.php';
?>

<section class="cart-section">
    <div class="container">
        <div class="section-heading">
            <h2><?= t('سلة', 'Shopping') ?> <span><?= t('التسوق', 'Cart') ?></span></h2>
        </div>

        <?php if(count($cartData) > 0): ?>
        <div class="cart-container">
            <!-- Cart Items -->
            <div class="cart-items">
                <?php foreach($cartData as $item): ?>
                <div class="cart-item" id="cart-item-<?= $item['cart_id'] ?>" style="transition: opacity 0.3s, transform 0.3s;">
                    <img src="<?= sanitize($item['image']) ?>" alt="<?= sanitize($item['name']) ?>">
                    <div class="item-details">
                        <h3><?= t(sanitize($item['name_ar'] ?? $item['name']), sanitize($item['name'])) ?></h3>
                        <div class="item-price">
                            <?php if($item['old_price']): ?>
                            <span><?= formatPrice($item['old_price']) ?></span>
                            <?php endif; ?>
                            <?= formatPrice($item['price']) ?>
                        </div>
                        <div style="font-size:1.3rem;color:var(--text-muted);margin-top:0.5rem;">
                            <?= t('المجموع:', 'Total:') ?> 
                            <span id="item-total-<?= $item['cart_id'] ?>" style="color:var(--primary-light);font-weight:700;">
                                <?= formatPrice($item['price'] * $item['quantity']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="quantity-controls">
                        <button onclick="updateQuantity(<?= $item['cart_id'] ?>, -1)">−</button>
                        <span class="qty-value" id="qty-<?= $item['cart_id'] ?>"><?= $item['quantity'] ?></span>
                        <button onclick="updateQuantity(<?= $item['cart_id'] ?>, 1)">+</button>
                    </div>
                    <button class="remove-btn" onclick="removeFromCart(<?= $item['cart_id'] ?>)" title="<?= t('حذف','Remove') ?>">🗑️</button>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary">
                <h3><?= t('ملخص الطلب', 'Order Summary') ?></h3>
                <div class="summary-row">
                    <span><?= t('المجموع الفرعي', 'Subtotal') ?></span>
                    <span id="cartSubtotal"><?= formatPrice($subtotal) ?></span>
                </div>
                <div class="summary-row">
                    <span><?= t('الشحن', 'Shipping') ?></span>
                    <span style="color:var(--success);"><?= $shipping > 0 ? formatPrice($shipping) : t('مجاني', 'Free') ?></span>
                </div>
                <div class="summary-row total">
                    <span><?= t('الإجمالي', 'Total') ?></span>
                    <span id="cartTotal"><?= formatPrice($total) ?></span>
                </div>
                <a href="cart/checkout.php" class="btn btn-primary checkout-btn btn-lg">
                    💳 <?= t('إتمام الطلب', 'Checkout') ?>
                </a>
                <a href="products.php" class="btn btn-secondary btn-block" style="margin-top:1rem;text-align:center;">
                    ← <?= t('متابعة التسوق', 'Continue Shopping') ?>
                </a>
            </div>
        </div>

        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">🛒</div>
            <h3><?= t('سلة التسوق فارغة', 'Your cart is empty') ?></h3>
            <p><?= t('لم تضف أي منتجات بعد', 'You have not added any products yet') ?></p>
            <a href="products.php" class="btn btn-primary"><?= t('تسوق الآن', 'Shop Now') ?></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
