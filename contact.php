<?php
require_once 'includes/functions.php';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
    redirect('contact.php');
}

$pageTitle = t('اتصل بنا', 'Contact Us');
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', t('طلب غير صالح', 'Invalid request'));
        redirect('contact.php');
    }
    $name    = sanitize($_POST['name'] ?? '');
    $email   = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        setFlash('error', t('يرجى ملء جميع الحقول المطلوبة', 'Please fill all required fields'));
        redirect('contact.php');
    }

    $stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    if ($stmt->execute()) {
        $success = true;
        setFlash('success', t('تم إرسال رسالتك بنجاح! سنتواصل معك قريباً', 'Your message has been sent successfully! We will contact you soon'));
    } else {
        setFlash('error', t('حدث خطأ، يرجى المحاولة مجدداً', 'An error occurred, please try again'));
    }
    redirect('contact.php');
}

require_once 'includes/header.php';
?>

<section class="section-padding" style="padding-top:10rem;">
    <div class="container">
        <div class="section-heading">
            <h2><?= t('تواصل', 'Get in') ?> <span><?= t('معنا', 'Touch') ?></span></h2>
            <p><?= t('نحن هنا لمساعدتك! تواصل معنا وسنرد في أقرب وقت', "We're here to help! Contact us and we'll respond ASAP") ?></p>
        </div>

        <div class="contact-grid">
            <!-- Info Cards -->
            <div class="contact-info-cards">
                <?php
                $infos = [
                    ['📍', t('العنوان','Address'), t('تعز، اليمن','Taiz, Yemen')],
                    ['📧', t('البريد الإلكتروني','Email'), 'mohammedit199@gmail.com'],
                    ['📱', t('رقم الهاتف','Phone'), '+967 730477879'],
                    ['⏰', t('ساعات العمل','Working Hours'), t('السبت - الخميس: 9ص - 9م','Sat - Thu: 9AM - 9PM')],
                ];
                foreach($infos as $info): ?>
                <div class="contact-info-card">
                    <div class="icon"><?= $info[0] ?></div>
                    <div>
                        <h4><?= $info[1] ?></h4>
                        <p><?= $info[2] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-card">
                <h3>📨 <?= t('أرسل رسالة', 'Send a Message') ?></h3>
                <form action="contact.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <div class="form-group">
                        <label><?= t('الاسم *', 'Name *') ?></label>
                        <input type="text" name="name" placeholder="<?= t('اسمك الكامل','Your full name') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><?= t('البريد الإلكتروني *', 'Email *') ?></label>
                        <input type="email" name="email" placeholder="example@email.com" required>
                    </div>
                    <div class="form-group">
                        <label><?= t('الموضوع', 'Subject') ?></label>
                        <input type="text" name="subject" placeholder="<?= t('موضوع رسالتك','Your message subject') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= t('الرسالة *', 'Message *') ?></label>
                        <textarea name="message" placeholder="<?= t('اكتب رسالتك هنا...','Write your message here...') ?>" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        📨 <?= t('إرسال الرسالة', 'Send Message') ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
