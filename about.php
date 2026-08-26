<?php
require_once 'includes/functions.php';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
    redirect('about.php');
}

$pageTitle = t('من نحن', 'About Us');
require_once 'includes/header.php';
?>

<section class="about-section">
    <div class="container">
        <div class="about-grid">
            <div class="about-image">
                <img src="img/store.jpg" alt="Mohammed Shop">
            </div>
            <div class="about-content">
                <h2><?= t('قصتنا', 'Our') ?> <span><?= t('', 'Story') ?></span></h2>
                <p><?= t(
                    'مرحباً بكم في متجرنا الرائد في عالم الإلكترونيات الحديثة. نحن نسعى جاهدين لتقديم تجربة تسوق استثنائية تلبي كافة احتياجاتكم التقنية بأعلى معايير الجودة والتميز.',
                    'Welcome to our leading store in the world of modern electronics. We strive to provide an exceptional shopping experience that meets all your tech needs with the highest standards of quality and excellence.'
                ) ?></p>
                <p><?= t(
                    'نقدم تشكيلة واسعة من أحدث الهواتف الذكية، الساعات الذكية، والإكسسوارات الأصلية بأسعار تنافسية. كما نتميز بتقديم خدمات ما بعد البيع، ضمان شامل، ودعم فني متواصل لعملائنا الكرام.',
                    'We offer a wide range of the latest smartphones, smartwatches, and original accessories at competitive prices. We also stand out by providing after-sales services, comprehensive warranty, and continuous technical support for our valued customers.'
                ) ?></p>
                <a href="contact.php" class="btn btn-primary"><?= t('تواصل معنا', 'Contact Us') ?> →</a>
            </div>
        </div>

        <!-- Stats -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(18rem,1fr));gap:2rem;margin-top:6rem;">
            <?php
            $stats = [
                ['🛒', '500+', t('منتج متاح', 'Products Available')],
                ['👥', '1000+', t('عميل سعيد', 'Happy Customers')],
                ['⭐', '4.8', t('تقييم المتوسط', 'Average Rating')],
                ['🚀', '24h', t('وقت التوصيل', 'Delivery Time')],
            ];
            foreach($stats as $stat): ?>
            <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:3rem;text-align:center;">
                <div style="font-size:3.5rem;margin-bottom:1rem;"><?= $stat[0] ?></div>
                <div style="font-size:3rem;font-weight:800;background:linear-gradient(135deg,var(--primary),var(--secondary));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= $stat[1] ?></div>
                <div style="font-size:1.4rem;color:var(--text-secondary);margin-top:0.5rem;"><?= $stat[2] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<?php require_once 'includes/footer.php'; ?>
