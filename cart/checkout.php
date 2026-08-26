<?php
$subdir = true; // We're in a subdirectory
require_once '../includes/functions.php';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
    redirect('../cart/checkout.php');
}

if (!isLoggedIn()) {
    setFlash('info', t('يرجى تسجيل الدخول أولاً','Please login first'));
    redirect('../login.php');
}

// Get cart items
$stmt = $conn->prepare("SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.name_ar, p.price, p.image, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$cartItems = $stmt->get_result();
$cartData = [];
$subtotal = 0;
while ($item = $cartItems->fetch_assoc()) {
    $subtotal += $item['price'] * $item['quantity'];
    $cartData[] = $item;
}

if (empty($cartData)) {
    setFlash('info', t('السلة فارغة', 'Cart is empty'));
    redirect('../cart.php');
}

// Process order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', t('طلب غير صالح','Invalid request'));
        redirect('../cart/checkout.php');
    }

    $address = sanitize($_POST['address'] ?? '');
    $phone   = sanitize($_POST['phone'] ?? '');
    $notes   = sanitize($_POST['notes'] ?? '');
    $city    = sanitize($_POST['city'] ?? '');

    if (!$address || !$phone) {
        setFlash('error', t('يرجى ملء جميع الحقول المطلوبة','Please fill all required fields'));
        redirect('../cart/checkout.php');
    }

    $fullAddress = "$address, $city";
    $total = $subtotal;

    // Create order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, phone, notes) VALUES (?, ?, 'pending', 'cod', ?, ?, ?)");
    $stmt->bind_param("idsss", $_SESSION['user_id'], $total, $fullAddress, $phone, $notes);
    $stmt->execute();
    $orderId = $conn->insert_id;

    // Insert order items
    foreach ($cartData as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();

        // Update stock
        $stmt2 = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        $stmt2->bind_param("iii", $item['quantity'], $item['product_id'], $item['quantity']);
        $stmt2->execute();
    }

    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    setFlash('success', t("تم تأكيد طلبك رقم #$orderId بنجاح! سنتواصل معك قريباً.", "Order #$orderId confirmed successfully! We will contact you soon."));
    redirect('../index.php');
}

$pageTitle = t('إتمام الطلب','Checkout');

// Pre-fill user data
$userStmt = $conn->prepare("SELECT phone, address FROM users WHERE id = ?");
$userStmt->bind_param("i", $_SESSION['user_id']);
$userStmt->execute();
$userData = $userStmt->get_result()->fetch_assoc();

require_once '../includes/header.php';
?>

<section class="section-padding" style="padding-top:10rem;">
    <div class="container">
        <div class="section-heading">
            <h2><?= t('إتمام', 'Complete') ?> <span><?= t('الطلب', 'Order') ?></span></h2>
        </div>

        <div class="checkout-grid">
            <!-- Checkout Form -->
            <div class="checkout-form-card">
                <h3>📦 <?= t('بيانات الشحن', 'Shipping Details') ?></h3>
                <form action="../cart/checkout.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <div class="form-group">
                        <label><?= t('العنوان الكامل *','Full Address *') ?></label>
                        <input type="text" name="address" value="<?= sanitize($userData['address'] ?? '') ?>" placeholder="<?= t('الشارع، الحي','Street, District') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><?= t('المدينة *','City *') ?></label>
                        <input type="text" name="city" placeholder="<?= t('مدينتك','Your city') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><?= t('رقم الهاتف *','Phone Number *') ?></label>
                        <input type="tel" name="phone" value="<?= sanitize($userData['phone'] ?? '') ?>" placeholder="+966 50 000 0000" required>
                    </div>
                    <div class="form-group">
                        <label><?= t('ملاحظات','Notes') ?></label>
                        <textarea name="notes" placeholder="<?= t('أي ملاحظات للتوصيل...','Any delivery notes...') ?>"></textarea>
                    </div>

                    <div class="payment-method">
                        <span class="pay-icon">💵</span>
                        <div>
                            <h4><?= t('الدفع عند الاستلام','Cash on Delivery') ?></h4>
                            <p><?= t('ادفع عند وصول طلبك','Pay when your order arrives') ?></p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:2.5rem;">
                        ✅ <?= t('تأكيد الطلب','Confirm Order') ?> — <?= formatPrice($subtotal) ?>
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="order-summary-card">
                <h3>🧾 <?= t('ملخص الطلب','Order Summary') ?></h3>
                <?php foreach ($cartData as $item): ?>
                <div class="order-item">
                    <img src="<?= sanitize($item['image']) ?>" alt="<?= sanitize($item['name']) ?>">
                    <div class="item-info">
                        <h4><?= t(sanitize($item['name_ar'] ?? $item['name']), sanitize($item['name'])) ?></h4>
                        <span><?= t('الكمية:','Qty:') ?> <?= $item['quantity'] ?></span>
                    </div>
                    <span class="item-total"><?= formatPrice($item['price'] * $item['quantity']) ?></span>
                </div>
                <?php endforeach; ?>
                <div style="margin-top:2rem;border-top:1px solid var(--border);padding-top:1.5rem;">
                    <div class="summary-row">
                        <span><?= t('الشحن','Shipping') ?></span>
                        <span style="color:var(--success);"><?= t('مجاني','Free') ?></span>
                    </div>
                    <div class="summary-row total" style="margin-top:1rem;">
                        <span><?= t('الإجمالي','Total') ?></span>
                        <span><?= formatPrice($subtotal) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
