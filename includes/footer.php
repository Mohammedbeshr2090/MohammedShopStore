<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <a href="<?= $basePath ?>index.php" class="footer-logo">Mohammed<span>Shop</span></a>
                <p><?= t('متجر محمد - وجهتك الأولى لأفضل الأجهزة الإلكترونية بأسعار منافسة وجودة عالية.', 'Mohammed Shop - Your first destination for the best electronics at competitive prices and high quality.') ?></p>
                <div class="social-links">
                    <a href="#" title="Facebook">📘</a>
                    <a href="#" title="Twitter">🐦</a>
                    <a href="#" title="Instagram">📷</a>
                    <a href="#" title="WhatsApp">💬</a>
                </div>
            </div>
            <div class="footer-col">
                <h3><?= t('روابط سريعة', 'Quick Links') ?></h3>
                <div class="footer-links">
                    <a href="<?= $basePath ?>index.php"><?= t('الرئيسية', 'Home') ?></a>
                    <a href="<?= $basePath ?>products.php"><?= t('المنتجات', 'Products') ?></a>
                    <a href="<?= $basePath ?>about.php"><?= t('من نحن', 'About Us') ?></a>
                    <a href="<?= $basePath ?>contact.php"><?= t('اتصل بنا', 'Contact') ?></a>
                </div>
            </div>
            <div class="footer-col">
                <h3><?= t('حسابي', 'My Account') ?></h3>
                <div class="footer-links">
                    <a href="<?= $basePath ?>login.php"><?= t('تسجيل الدخول', 'Login') ?></a>
                    <a href="<?= $basePath ?>register.php"><?= t('إنشاء حساب', 'Register') ?></a>
                    <a href="<?= $basePath ?>cart.php"><?= t('سلة التسوق', 'Cart') ?></a>
                </div>
            </div>
            <div class="footer-col">
                <h3><?= t('تواصل معنا', 'Contact Us') ?></h3>
                <div class="footer-links">
                    <a href="#">📍 <?= t('تعز، اليمن', 'Taiz, Yemen') ?></a>
                    <a href="#">📧 mohammedit199@gmail.com</a>
                    <a href="#">📱 +967 730477879</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> <span>Mohammed Shop</span>. <?= t('جميع الحقوق محفوظة', 'All rights reserved') ?>.</p>
        </div>
    </div>
</footer>

<div class="toast" id="toast">
    <span id="toastMessage"></span>
</div>

<script src="<?= $basePath ?>js/main.js"></script>
<?php if(isset($extraJS)): ?>
<script src="<?= $basePath ?><?= $extraJS ?>"></script>
<?php endif; ?>
</body>
</html>
