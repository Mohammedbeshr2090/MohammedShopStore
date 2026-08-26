<?php
require_once '../includes/functions.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => t('غير مصرح','Unauthorized')]);
    exit;
}

$cartId = (int)($_POST['cart_id'] ?? 0);
if (!$cartId) {
    echo json_encode(['success' => false, 'message' => t('بيانات غير صالحة','Invalid data')]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cartId, $_SESSION['user_id']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode([
        'success'    => true,
        'message'    => t('تم حذف المنتج من السلة', 'Product removed from cart'),
        'cart_count' => getCartCount()
    ]);
} else {
    echo json_encode(['success' => false, 'message' => t('لم يتم العثور على العنصر','Item not found')]);
}
?>
