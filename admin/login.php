<?php
require_once '../includes/functions.php';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
    redirect('admin/login.php');
}

if (isAdmin()) redirect('index.php');

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? AND role = 'admin'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role']  = $user['role'];
            redirect('index.php');
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
        }
    } else {
        $error = 'يرجى ملء جميع الحقول';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Mohammed Shop</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:var(--admin-bg); }
        .login-wrapper { width:100%; max-width:42rem; padding:2rem; }
        .login-card { background:var(--admin-card); border:1px solid var(--admin-border); border-radius:20px; padding:4rem; box-shadow:0 20px 60px rgba(124,58,237,0.15); }
        .login-logo { text-align:center; margin-bottom:3rem; }
        .login-logo h1 { font-size:2.5rem; font-weight:800; }
        .login-logo h1 span { background:linear-gradient(135deg,var(--primary),var(--secondary)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .login-logo p { color:var(--text-muted); font-size:1.3rem; margin-top:0.5rem; }
        .error-msg { background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#fca5a5; padding:1.2rem 1.8rem; border-radius:var(--radius); font-size:1.4rem; margin-bottom:2rem; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <h1>Mohammed<span>Shop</span></h1>
            <p>🎛️ لوحة تحكم الإدارة</p>
        </div>

        <?php if (isset($error)): ?>
        <div class="error-msg">❌ <?= sanitize($error) ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" placeholder="admin@email.com" required value="<?= isset($_POST['email']) ? sanitize($_POST['email']) : '' ?>">
            </div>
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:1rem;">
                🔐 تسجيل الدخول
            </button>
        </form>
        <div style="text-align:center;margin-top:2rem;font-size:1.3rem;color:var(--text-muted);">
            <a href="../index.php" style="color:var(--primary-light);text-decoration:none;">← العودة إلى الموقع</a>
        </div>
    </div>
</div>
</body>
</html>
