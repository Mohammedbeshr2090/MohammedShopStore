<?php
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../admin/login.php');

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId   = (int)$_POST['order_id'];
    $newStatus = sanitize($_POST['status'] ?? '');
    $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($newStatus, $allowedStatuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $orderId);
        $stmt->execute();
        setFlash('success', "تم تحديث حالة الطلب #$orderId");
    }
    redirect('orders.php');
}

$pageTitle    = '🛒 الطلبات | Orders';
$pageSubtitle = 'إدارة جميع الطلبات';
$statusFilter = sanitize($_GET['status'] ?? '');

$where  = "WHERE 1=1";
$params = []; $types = "";
if ($statusFilter) { $where .= " AND o.status = ?"; $params[] = $statusFilter; $types .= "s"; }

$sql  = "SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id = u.id $where ORDER BY o.created_at DESC";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$orders = $stmt->get_result();

$statusMap = [
    'pending'    => ['label' => 'قيد الانتظار', 'class' => 'warning'],
    'processing' => ['label' => 'قيد المعالجة', 'class' => 'info'],
    'shipped'    => ['label' => 'تم الشحن',     'class' => 'primary'],
    'delivered'  => ['label' => 'تم التسليم',   'class' => 'success'],
    'cancelled'  => ['label' => 'ملغي',          'class' => 'danger'],
];

require_once 'includes/admin_header.php';
?>

<div class="filter-bar">
    <a href="orders.php" class="btn <?= !$statusFilter ? 'btn-primary' : 'btn-secondary' ?> btn-sm">الكل</a>
    <?php foreach($statusMap as $key => $s): ?>
    <a href="orders.php?status=<?= $key ?>" class="btn <?= $statusFilter === $key ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
        <?= $s['label'] ?>
    </a>
    <?php endforeach; ?>
</div>

<div class="admin-table-card">
    <div class="table-header">
        <h3>🛒 قائمة الطلبات (<?= $orders->num_rows ?>)</h3>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>العميل</th>
                <th>المبلغ</th>
                <th>طريقة الدفع</th>
                <th>العنوان</th>
                <th>الهاتف</th>
                <th>التاريخ</th>
                <th>الحالة</th>
                <th>تغيير الحالة</th>
            </tr>
        </thead>
        <tbody>
            <?php if($orders->num_rows > 0): ?>
            <?php while($o = $orders->fetch_assoc()): 
                $s = $statusMap[$o['status']] ?? ['label' => $o['status'], 'class' => 'secondary'];
            ?>
            <tr>
                <td><strong>#<?= $o['id'] ?></strong></td>
                <td>
                    <strong><?= sanitize($o['user_name']) ?></strong>
                    <br><small style="color:var(--text-muted);"><?= sanitize($o['user_email']) ?></small>
                </td>
                <td style="color:var(--primary-light);font-weight:800;">$<?= number_format($o['total_amount'], 2) ?></td>
                <td><span class="badge badge-info">💵 COD</span></td>
                <td style="font-size:1.2rem;max-width:15rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= sanitize($o['shipping_address']) ?></td>
                <td><?= sanitize($o['phone']) ?></td>
                <td style="color:var(--text-muted);font-size:1.2rem;"><?= date('Y/m/d<br>H:i', strtotime($o['created_at'])) ?></td>
                <td><span class="badge badge-<?= $s['class'] ?>"><?= $s['label'] ?></span></td>
                <td>
                    <form action="orders.php" method="POST" style="display:flex;gap:0.5rem;align-items:center;">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                        <select name="status" style="padding:0.5rem;font-size:1.2rem;background:var(--admin-bg);border:1px solid var(--admin-border);border-radius:8px;color:var(--text-primary);">
                            <?php foreach($statusMap as $key => $sv): ?>
                            <option value="<?= $key ?>" <?= $o['status'] === $key ? 'selected' : '' ?>><?= $sv['label'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">✔</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr><td colspan="9"><div class="admin-empty"><div class="empty-icon">🛒</div><p>لا توجد طلبات</p></div></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div></main>
</body></html>
