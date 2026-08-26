<?php
require_once '../includes/functions.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode([
        'success'     => false,
        'needs_login' => true,
        'message'     => t('يجب تسجيل الدخول أولاً للإضافة إلى السلة', 'You must be logged in to add items to cart')
    ]);
    exit;
}

$productId = (int)($_POST['product_id'] ?? 0);
$quantity  = max(1, (int)($_POST['quantity'] ?? 1));

if (!$productId) {
    echo json_encode(['success' => false, 'message' => t('منتج غير صالح', 'Invalid product')]);
    exit;
}

// Check product exists and has stock
$stmt = $conn->prepare("SELECT id, name, name_ar, stock FROM products WHERE id = ? AND status = 1");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => t('المنتج غير موجود', 'Product not found')]);
    exit;
}
if ($product['stock'] < $quantity) {
    echo json_encode(['success' => false, 'message' => t('الكمية غير متوفرة في المخزون', 'Requested quantity not available')]);
    exit;
}

// Check if already in cart
$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $_SESSION['user_id'], $productId);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();

if ($existing) {
    $newQty = $existing['quantity'] + $quantity;
    if ($newQty > $product['stock']) $newQty = $product['stock'];
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $newQty, $existing['id']);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $_SESSION['user_id'], $productId, $quantity);
    $stmt->execute();
}

echo json_encode([
    'success'    => true,
    'message'    => t('تمت الإضافة إلى السلة!', 'Added to cart!'),
    'cart_count' => getCartCount()
]);
?>
