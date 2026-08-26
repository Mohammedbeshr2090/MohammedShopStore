<?php
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../admin/login.php');

// Mark as read
if (isset($_GET['read'])) {
    $msgId = (int)$_GET['read'];
    $conn->query("UPDATE messages SET is_read = 1 WHERE id = $msgId");
    redirect('messages.php');
}

// Mark all as read
if (isset($_GET['readall'])) {
    $conn->query("UPDATE messages SET is_read = 1");
    setFlash('success', 'تم تعليم جميع الرسائل كمقروءة');
    redirect('messages.php');
}

// Delete message
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $conn->query("DELETE FROM messages WHERE id = $delId");
    setFlash('success', 'تم حذف الرسالة');
    redirect('messages.php');
}

$pageTitle    = '📨 الرسائل | Messages';
$pageSubtitle = 'رسائل العملاء';
$filter       = sanitize($_GET['filter'] ?? '');

$where = $filter === 'unread' ? "WHERE is_read = 0" : "";
$messages = $conn->query("SELECT * FROM messages $where ORDER BY created_at DESC");
$unread   = $conn->query("SELECT COUNT(*) as c FROM messages WHERE is_read = 0")->fetch_assoc()['c'];

require_once 'includes/admin_header.php';
?>

<div style="display:flex;gap:1.5rem;margin-bottom:2rem;align-items:center;flex-wrap:wrap;">
    <a href="messages.php" class="btn <?= !$filter ? 'btn-primary' : 'btn-secondary' ?> btn-sm">كل الرسائل</a>
    <a href="messages.php?filter=unread" class="btn <?= $filter === 'unread' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
        غير المقروءة <?php if($unread): ?><span class="nav-badge"><?= $unread ?></span><?php endif; ?>
    </a>
    <?php if($unread > 0): ?>
    <a href="messages.php?readall=1" class="btn btn-secondary btn-sm">✅ تعليم الكل كمقروء</a>
    <?php endif; ?>
</div>

<div class="admin-table-card">
    <div class="table-header">
        <h3>📨 قائمة الرسائل (<?= $messages->num_rows ?>)</h3>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>المرسل</th>
                <th>البريد</th>
                <th>الموضوع</th>
                <th>الرسالة</th>
                <th>التاريخ</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if($messages->num_rows > 0): ?>
            <?php while($m = $messages->fetch_assoc()): ?>
            <tr style="<?= !$m['is_read'] ? 'background:rgba(124,58,237,0.05);' : '' ?>">
                <td><?= $m['id'] ?></td>
                <td>
                    <strong><?= sanitize($m['name']) ?></strong>
                    <?php if(!$m['is_read']): ?><br><span class="badge badge-primary" style="font-size:1rem;">جديد</span><?php endif; ?>
                </td>
                <td style="font-size:1.3rem;color:var(--text-muted);"><?= sanitize($m['email']) ?></td>
                <td><?= sanitize($m['subject'] ?: '—') ?></td>
                <td style="max-width:25rem;">
                    <div style="position:relative;">
                        <p style="font-size:1.3rem;color:var(--text-secondary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:25rem;" title="<?= sanitize($m['message']) ?>">
                            <?= sanitize(substr($m['message'], 0, 80)) ?>...
                        </p>
                    </div>
                </td>
                <td style="color:var(--text-muted);font-size:1.2rem;"><?= date('Y/m/d H:i', strtotime($m['created_at'])) ?></td>
                <td>
                    <span class="badge <?= $m['is_read'] ? 'badge-success' : 'badge-warning' ?>">
                        <?= $m['is_read'] ? '✅ مقروء' : '🔔 جديد' ?>
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                        <?php if(!$m['is_read']): ?>
                        <a href="messages.php?read=<?= $m['id'] ?>" class="btn btn-success btn-sm" title="تعليم كمقروء">✔</a>
                        <?php endif; ?>
                        <a href="mailto:<?= sanitize($m['email']) ?>?subject=Re: <?= urlencode($m['subject'] ?: 'رد على رسالتك') ?>" class="btn btn-secondary btn-sm" title="رد">📧</a>
                        <a href="messages.php?delete=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل تريد حذف هذه الرسالة؟')" title="حذف">🗑️</a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr><td colspan="8"><div class="admin-empty"><div class="empty-icon">📭</div><p>لا توجد رسائل</p></div></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div></main>
</body></html>
