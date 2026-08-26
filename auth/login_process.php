<?php
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../login.php');

// Preserve redirect URL across requests
$redirectAfter = '';
if (!empty($_POST['redirect_url'])) {
    $url = $_POST['redirect_url'];
    // Only allow same-origin redirects for security
    $host = $_SERVER['HTTP_HOST'];
    if (strpos($url, 'http://' . $host) === 0 || strpos($url, 'https://' . $host) === 0) {
        $redirectAfter = $url;
    }
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', t('طلب غير صالح', 'Invalid request'));
    redirect('../login.php');
}

$email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    setFlash('error', t('يرجى ملء جميع الحقول', 'Please fill all fields'));
    redirect('../login.php');
}

$stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    setFlash('error', t('البريد الإلكتروني أو كلمة المرور غير صحيحة', 'Invalid email or password'));
    redirect('../login.php');
}

// Set session
$_SESSION['user_id']   = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email']= $user['email'];
$_SESSION['user_role'] = $user['role'];

if (isset($_POST['remember'])) {
    setcookie('user_email', $email, time() + 30*24*3600, '/');
}

setFlash('success', t('مرحباً ' . sanitize($user['name']) . '!', 'Welcome ' . sanitize($user['name']) . '!'));

// Redirect to original page if set, otherwise default
if ($redirectAfter) {
    redirect($redirectAfter);
} elseif ($user['role'] === 'admin') {
    redirect('../admin/index.php');
} else {
    redirect('../index.php');
}
?>
