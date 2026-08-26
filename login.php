<?php
require_once 'includes/functions.php';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
    redirect('login.php');
}

if (isLoggedIn()) redirect('index.php');

$pageTitle = t('تسجيل الدخول', 'Login');
require_once 'includes/header.php';
?>

<section class="auth-section">
    <div class="auth-card">
        <h2>🔐 <?= t('تسجيل الدخول', 'Login') ?></h2>
        <p class="auth-subtitle"><?= t('أهلاً بعودتك! سجل دخولك للمتابعة', 'Welcome back! Login to continue') ?></p>

        <form action="auth/login_process.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="redirect_url" value="<?= sanitize($_GET['redirect'] ?? '') ?>">

            <div class="form-group">
                <label for="email"><?= t('البريد الإلكتروني', 'Email Address') ?></label>
                <input type="email" id="email" name="email" placeholder="example@email.com" required>
            </div>

            <div class="form-group">
                <label for="password"><?= t('كلمة المرور', 'Password') ?></label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="form-check">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember"><?= t('تذكرني', 'Remember Me') ?></label>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <?= t('تسجيل الدخول', 'Login') ?>
            </button>
        </form>

        <div class="auth-link">
            <?= t('ليس لديك حساب؟', "Don't have an account?") ?>
            <a href="register.php"><?= t('سجل الآن', 'Register Now') ?></a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
