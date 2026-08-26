<?php
session_start();

// Handle language switch globally
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
    // Redirect to same URL without lang param
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    $params = $_GET;
    unset($params['lang']);
    $qs = http_build_query($params);
    header('Location: ' . $url . ($qs ? '?' . $qs : ''));
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Get site setting
function getSetting($key) {
    global $conn;
    $stmt = $conn->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    return '';
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Get cart count for current user
function getCartCount() {
    global $conn;
    if (!isLoggedIn()) return 0;
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ? (int)$row['total'] : 0;
}

// Get cart total for current user
function getCartTotal() {
    global $conn;
    if (!isLoggedIn()) return 0;
    $stmt = $conn->prepare("SELECT SUM(c.quantity * p.price) as total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ? number_format($row['total'], 2) : '0.00';
}

// Format price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Get unread messages count
function getUnreadMessagesCount() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as total FROM messages WHERE is_read = 0");
    $row = $result->fetch_assoc();
    return (int)$row['total'];
}

// Generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Flash message
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Get current language
function getLang() {
    return isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';
}

// Translate helper
function t($ar, $en) {
    return getLang() === 'ar' ? $ar : $en;
}

// Get direction
function getDir() {
    return getLang() === 'ar' ? 'rtl' : 'ltr';
}
?>
