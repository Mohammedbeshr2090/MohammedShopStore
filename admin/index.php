<?php
require_once '../includes/functions.php';
if (!isAdmin()) { redirect('../admin/login.php'); }

$pageTitle    = '📊 الإحصائيات | Dashboard';
$pageSubtitle = 'مرحباً بك في لوحة تحكم Mohammed Shop';

// Stats
$totalProducts  = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$totalOrders    = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$totalUsers     = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'];
$totalMessages  = $conn->query("SELECT COUNT(*) as c FROM messages WHERE is_read=0")->fetch_assoc()['c'];
$totalRevenue   = $conn->query("SELECT COALESCE(SUM(total_amount),0) as rev FROM orders WHERE status != 'cancelled'")->fetch_assoc()['rev'];
$pendingOrders  = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'];

// Recent orders
$recentOrders = $conn->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 8");

// Recent messages
$recentMessages = $conn->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 6");

require_once 'includes/admin_header.php';
?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-info">
            <h3><?= $totalProducts ?></h3>
            <p>إجمالي المنتجات | Total Products</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🛒</div>
        <div class="stat-info">
            <h3><?= $totalOrders ?></h3>
            <p>إجمالي الطلبات | Total Orders</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <h3><?= $totalUsers ?></h3>
            <p>إجمالي المستخدمين | Total Users</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-info">
            <h3>$<?= number_format($totalRevenue, 0) ?></h3>
            <p>إجمالي الإيرادات | Total Revenue</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div style="display:flex;gap:1.5rem;flex-wrap:wrap;margin-bottom:3rem;">
    <a href="add_product.php" class="btn btn-primary">➕ إضافة منتج</a>
    <a href="categories.php" class="btn btn-secondary">📁 الفئات</a>
    <a href="orders.php" class="btn btn-secondary">
        🛒 الطلبات <?php if($pendingOrders): ?><span class="nav-badge"><?= $pendingOrders ?></span><?php endif; ?>
    </a>
    <a href="messages.php" class="btn btn-secondary">
        📨 الرسائل <?php if($totalMessages): ?><span class="nav-badge"><?= $totalMessages ?></span><?php endif; ?>
    </a>
</div>

<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:2.5rem;">
    <!-- Recent Orders -->
    <div class="admin-table-card">
        <div class="table-header">
            <h3>🛒 آخر الطلبات | Recent Orders</h3>
            <a href="orders.php" class="btn btn-secondary btn-sm">عرض الكل</a>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>العميل</th>
                    <th>المبلغ</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                <?php if($recentOrders && $recentOrders->num_rows > 0): ?>
                <?php while($order = $recentOrders->fetch_assoc()): 
                    $statusClass = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'][$order['status']] ?? 'secondary';
                    $statusAr = ['pending'=>'قيد الانتظار','processing'=>'قيد المعالجة','shipped'=>'تم الشحن','delivered'=>'تم التسليم','cancelled'=>'ملغي'][$order['status']] ?? $order['status'];
                ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td><?= sanitize($order['user_name']) ?></td>
                    <td style="color:var(--primary-light);font-weight:700;">$<?= number_format($order['total_amount'], 2) ?></td>
                    <td><span class="badge badge-<?= $statusClass ?>"><?= $statusAr ?></span></td>
                    <td style="color:var(--text-muted);font-size:1.2rem;"><?= date('Y/m/d', strtotime($order['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr><td colspan="5" class="admin-empty"><p>لا توجد طلبات</p></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Recent Messages -->
    <div class="admin-table-card">
        <div class="table-header">
            <h3>📨 آخر الرسائل | Recent Messages</h3>
            <a href="messages.php" class="btn btn-secondary btn-sm">عرض الكل</a>
        </div>
        <div style="padding:1rem 0;">
            <?php if($recentMessages && $recentMessages->num_rows > 0): ?>
            <?php while($msg = $recentMessages->fetch_assoc()): ?>
            <div style="padding:1.5rem 2.5rem;border-bottom:1px solid rgba(255,255,255,0.04);display:flex;gap:1.5rem;align-items:flex-start;">
                <div style="width:4rem;height:4rem;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">👤</div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.3rem;">
                        <strong style="font-size:1.4rem;"><?= sanitize($msg['name']) ?></strong>
                        <?php if(!$msg['is_read']): ?>
                        <span class="badge badge-primary" style="font-size:1rem;">جديد</span>
                        <?php endif; ?>
                    </div>
                    <p style="font-size:1.3rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= sanitize(substr($msg['message'], 0, 50)) ?>...</p>
                    <span style="font-size:1.1rem;color:var(--text-muted);"><?= date('Y/m/d H:i', strtotime($msg['created_at'])) ?></span>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <div class="admin-empty"><div class="empty-icon">📭</div><p>لا توجد رسائل</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div></main>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, i * 100);
    });
});
</script>
</body>
</html>
