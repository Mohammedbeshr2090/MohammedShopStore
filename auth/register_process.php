<?php
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../register.php');

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', t('طلب غير صالح', 'Invalid request'));
    redirect('../register.php');
}

$name            = sanitize($_POST['name'] ?? '');
$email           = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone           = sanitize($_POST['phone'] ?? '');
$password        = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validations
if (!$name || !$email || !$password || !$confirmPassword) {
    setFlash('error', t('يرجى ملء جميع الحقول المطلوبة', 'Please fill all required fields'));
    redirect('../register.php');
}
if (strlen($password) < 8) {
    setFlash('error', t('يجب أن تكون كلمة المرور 8 أحرف على الأقل', 'Password must be at least 8 characters'));
    redirect('../register.php');
}
if ($password !== $confirmPassword) {
    setFlash('error', t('كلمتا المرور غير متطابقتين', 'Passwords do not match'));
    redirect('../register.php');
}

// Check if email exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    setFlash('error', t('البريد الإلكتروني مسجل مسبقاً', 'Email is already registered'));
    redirect('../register.php');
}

// Insert user
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $hashedPassword);

if ($stmt->execute()) {
    $userId = $conn->insert_id;
    $_SESSION['user_id']    = $userId;
    $_SESSION['user_name']  = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role']  = 'user';
    setFlash('success', t('تم إنشاء الحساب بنجاح! مرحباً ' . $name, 'Account created successfully! Welcome ' . $name));
    redirect('../index.php');
} else {
    setFlash('error', t('حدث خطأ أثناء إنشاء الحساب', 'An error occurred during registration'));
    redirect('../register.php');
}
?>
