<?php
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../admin/login.php');

$pageTitle    = '👥 المستخدمون | Users';
$pageSubtitle = 'إدارة مستخدمي الموقع';
$search       = sanitize($_GET['search'] ?? '');

// Toggle role
if (isset($_GET['toggle_role']) && (int)$_GET['toggle_role'] !== $_SESSION['user_id']) {
    $uid = (int)$_GET['toggle_role'];
    $conn->query("UPDATE users SET role = IF(role='admin','user','admin') WHERE id = $uid");
    redirect('users.php');
}

// Delete user
if (isset($_GET['delete']) && (int)$_GET['delete'] !== $_SESSION['user_id']) {
    $uid = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $uid AND role != 'admin'");
    setFlash('success', 'تم حذف المستخدم');
    redirect('users.php');
}

$where = $search ? "WHERE (name LIKE '%$search%' OR email LIKE '%$search%')" : "";
$users = $conn->query("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id=u.id) as order_count FROM users u $where ORDER BY u.created_at DESC");

require_once 'includes/admin_header.php';
?>

<div class="filter-bar">
    <form action="users.php" method="GET" style="display:flex;gap:1rem;flex:1;">
        <input type="search" name="search" placeholder="🔍 ابحث بالاسم أو البريد..." value="<?= $search ?>">
        <button type="submit" class="btn btn-primary btn-sm">بحث</button>
        <a href="users.php" class="btn btn-secondary btn-sm">مسح</a>
    </form>
</div>

<div class="admin-table-card">
    <div class="table-header">
        <h3>👥 قائمة المستخدمين (<?= $users->num_rows ?>)</h3>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>البريد الإلكتروني</th>
                <th>الهاتف</th>
                <th>الدور</th>
                <th>الطلبات</th>
                <th>تاريخ التسجيل</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if($users->num_rows > 0): ?>
            <?php while($u = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:1rem;">
                        <div style="width:3.5rem;height:3.5rem;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;flex-shrink:0;">
                            <?= mb_substr($u['name'], 0, 1) ?>
                        </div>
                        <strong><?= sanitize($u['name']) ?></strong>
                        <?php if($u['id'] == $_SESSION['user_id']): ?>
                        <span class="badge badge-primary" style="font-size:1rem;">أنت</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td style="color:var(--text-muted);"><?= sanitize($u['email']) ?></td>
                <td><?= sanitize($u['phone'] ?: '—') ?></td>
                <td>
                    <span class="badge <?= $u['role'] === 'admin' ? 'badge-danger' : 'badge-info' ?>">
                        <?= $u['role'] === 'admin' ? '👑 أدمن' : '👤 مستخدم' ?>
                    </span>
                </td>
                <td><span class="badge badge-primary"><?= $u['order_count'] ?></span></td>
                <td style="color:var(--text-muted);font-size:1.2rem;"><?= date('Y/m/d', strtotime($u['created_at'])) ?></td>
                <td>
                    <div style="display:flex;gap:0.5rem;">
                        <?php if($u['id'] !== $_SESSION['user_id']): ?>
                        <a href="users.php?toggle_role=<?= $u['id'] ?>" class="btn btn-warning btn-sm" onclick="return confirm('تغيير دور هذا المستخدم؟')">
                            <?= $u['role'] === 'admin' ? '⬇ إزالة صلاحية' : '⬆ ترقية لأدمن' ?>
                        </a>
                        <?php if($u['role'] !== 'admin'): ?>
                        <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل تريد حذف هذا المستخدم؟')">🗑️</a>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr><td colspan="8"><div class="admin-empty"><div class="empty-icon">👥</div><p>لا يوجد مستخدمون</p></div></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div></main>
</body></html>
