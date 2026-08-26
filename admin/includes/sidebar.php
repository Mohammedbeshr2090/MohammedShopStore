<?php
// Admin includes/sidebar.php
$currentPage = basename($_SERVER['PHP_SELF']);

// Get unread messages count
$unreadMsg = 0;
$result = $conn->query("SELECT COUNT(*) as c FROM messages WHERE is_read = 0");
if ($result) $unreadMsg = $result->fetch_assoc()['c'];

// Get pending orders count
$pendingOrders = 0;
$result2 = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status = 'pending'");
if ($result2) $pendingOrders = $result2->fetch_assoc()['c'];
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
        <a href="index.php">Mohammed<span>Shop</span></a>
        <p>لوحة التحكم | Dashboard</p>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-title">الرئيسية | Main</div>
        <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">
            <span class="nav-icon">📊</span> الإحصائيات | Stats
        </a>
        <a href="../index.php" target="_blank">
            <span class="nav-icon">🌐</span> عرض الموقع | View Site
        </a>

        <div class="nav-section-title">المتجر | Store</div>
        <a href="products.php" class="<?= $currentPage === 'products.php' || $currentPage === 'add_product.php' || $currentPage === 'edit_product.php' ? 'active' : '' ?>">
            <span class="nav-icon">📦</span> المنتجات | Products
        </a>
        <a href="categories.php" class="<?= $currentPage === 'categories.php' ? 'active' : '' ?>">
            <span class="nav-icon">📁</span> الفئات | Categories
        </a>
        <a href="orders.php" class="<?= $currentPage === 'orders.php' ? 'active' : '' ?>">
            <span class="nav-icon">🛒</span> الطلبات | Orders
            <?php if ($pendingOrders > 0): ?>
            <span class="nav-badge"><?= $pendingOrders ?></span>
            <?php endif; ?>
        </a>

        <div class="nav-section-title">المستخدمون | Users</div>
        <a href="users.php" class="<?= $currentPage === 'users.php' ? 'active' : '' ?>">
            <span class="nav-icon">👥</span> المستخدمون | Users
        </a>
        <a href="messages.php" class="<?= $currentPage === 'messages.php' ? 'active' : '' ?>">
            <span class="nav-icon">📨</span> الرسائل | Messages
            <?php if ($unreadMsg > 0): ?>
            <span class="nav-badge"><?= $unreadMsg ?></span>
            <?php endif; ?>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../auth/logout.php">🚪 تسجيل الخروج | Logout</a>
    </div>
</aside>
