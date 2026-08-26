<?php
// Admin includes/admin_header.php
if(!isset($pageTitle)) $pageTitle = 'Dashboard';
if(!isset($pageSubtitle)) $pageSubtitle = '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle) ?> | Mohammed Shop Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php
$flash = getFlash();
if ($flash): ?>
<div class="flash-message <?= $flash['type'] ?>" id="flashMsg">
    <?= $flash['message'] ?>
</div>
<script>setTimeout(() => { const f = document.getElementById('flashMsg'); if(f) { f.style.opacity='0'; f.style.transform='translateX(100%)'; f.style.transition='all 0.4s'; setTimeout(() => f.remove(), 400); } }, 4000);</script>
<?php endif; ?>

<?php require_once 'sidebar.php'; ?>

<main class="admin-main">
    <header class="admin-header">
        <div class="page-title">
            <h1><?= $pageTitle ?></h1>
            <?php if($pageSubtitle): ?>
            <p><?= $pageSubtitle ?></p>
            <?php endif; ?>
        </div>
        <div class="header-actions">
            <span style="font-size:1.3rem;color:var(--text-muted);">👤 <?= sanitize($_SESSION['user_name']) ?></span>
            <a href="../index.php" target="_blank">🌐 الموقع</a>
            <a href="../auth/logout.php" style="color:var(--danger);">🚪 خروج</a>
        </div>
    </header>
    <div class="admin-content">
