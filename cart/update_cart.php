<?php
require_once '../includes/functions.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => t('غير مصرح','Unauthorized')]);
    exit;
}

// Summary request
if (isset($_GET['summary'])) {
    $stmt = $conn->prepare("SELECT SUM(c.quantity * p.price) as subtotal FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $subtotal = $row['subtotal'] ?? 0;
    echo json_encode([
        'success'  => true,
        'subtotal' => formatPrice($subtotal),
        'total'    => formatPrice($subtotal)
    ]);
    exit;
}

$cartId   = (int)($_POST['cart_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

if (!$cartId || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => t('بيانات غير صالحة','Invalid data')]);
    exit;
}

// Verify ownership and get item
$stmt = $conn->prepare("SELECT c.id, c.quantity, p.price, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
$stmt->bind_param("ii", $cartId, $_SESSION['user_id']);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    echo json_encode(['success' => false, 'message' => t('العنصر غير موجود','Item not found')]);
    exit;
}

$finalQty = min($quantity, $item['stock']);
$stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
$stmt->bind_param("ii", $finalQty, $cartId);
$stmt->execute();

echo json_encode([
    'success'    => true,
    'item_total' => formatPrice($item['price'] * $finalQty),
    'cart_count' => getCartCount()
]);
?>
