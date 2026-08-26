<?php
require_once 'includes/functions.php';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
    redirect('register.php');
}

if (isLoggedIn()) redirect('index.php');

$pageTitle = t('إنشاء حساب', 'Register');
require_once 'includes/header.php';
?>

<section class="auth-section">
    <div class="auth-card">
        <h2>📝 <?= t('إنشاء حساب', 'Create Account') ?></h2>
        <p class="auth-subtitle"><?= t('انضم إلينا اليوم وابدأ التسوق!', 'Join us today and start shopping!') ?></p>

        <form action="auth/register_process.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="form-group">
                <label for="name"><?= t('الاسم الكامل', 'Full Name') ?></label>
                <input type="text" id="name" name="name" placeholder="<?= t('أدخل اسمك', 'Enter your name') ?>" required>
            </div>

            <div class="form-group">
                <label for="email"><?= t('البريد الإلكتروني', 'Email Address') ?></label>
                <input type="email" id="email" name="email" placeholder="example@email.com" required>
            </div>

            <div class="form-group">
                <label for="phone"><?= t('رقم الهاتف', 'Phone Number') ?></label>
                <input type="tel" id="phone" name="phone" placeholder="+966 50 000 0000">
            </div>

            <div class="form-group">
                <label for="password"><?= t('كلمة المرور', 'Password') ?></label>
                <input type="password" id="password" name="password" placeholder="<?= t('8 أحرف على الأقل', 'At least 8 characters') ?>" required minlength="8">
            </div>

            <div class="form-group">
                <label for="confirm_password"><?= t('تأكيد كلمة المرور', 'Confirm Password') ?></label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <?= t('إنشاء حساب', 'Create Account') ?>
            </button>
        </form>

        <div class="auth-link">
            <?= t('لديك حساب بالفعل؟', 'Already have an account?') ?>
            <a href="login.php"><?= t('تسجيل الدخول', 'Login') ?></a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
