<?php
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../admin/login.php');

// Handle delete
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $conn->prepare("SELECT image, hover_image FROM products WHERE id = ?");
    $stmt->bind_param("i", $delId);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    if ($p) {
        // Optionally delete image files
        $conn->prepare("DELETE FROM products WHERE id = ?")->bind_param("i", $delId) && $conn->prepare("DELETE FROM products WHERE id = ?")->execute();
        $del = $conn->prepare("DELETE FROM products WHERE id = ?");
        $del->bind_param("i", $delId);
        $del->execute();
        setFlash('success', 'تم حذف المنتج بنجاح');
    }
    redirect('products.php');
}

// Handle status toggle
if (isset($_GET['toggle'])) {
    $tId = (int)$_GET['toggle'];
    $conn->prepare("UPDATE products SET status = 1 - status WHERE id = ?")->bind_param("i", $tId);
    $conn->query("UPDATE products SET status = 1 - status WHERE id = $tId");
    redirect('products.php');
}

$pageTitle    = '📦 المنتجات | Products';
$pageSubtitle = 'إدارة جميع المنتجات';

// Filters
$search = sanitize($_GET['search'] ?? '');
$catFilter = (int)($_GET['cat'] ?? 0);

$where = "WHERE 1=1";
$params = []; $types = "";
if ($search) { $where .= " AND (p.name LIKE ? OR p.name_ar LIKE ?)"; $s = "%$search%"; $params[] = $s; $params[] = $s; $types .= "ss"; }
if ($catFilter) { $where .= " AND p.category_id = ?"; $params[] = $catFilter; $types .= "i"; }

$sql = "SELECT p.*, c.name_ar as cat_name_ar FROM products p LEFT JOIN categories c ON p.category_id = c.id $where ORDER BY p.created_at DESC";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();

$categories = $conn->query("SELECT * FROM categories WHERE status=1 ORDER BY name_ar");

require_once 'includes/admin_header.php';
?>

<div class="filter-bar">
    <form action="products.php" method="GET" style="display:flex;gap:1rem;flex:1;flex-wrap:wrap;">
        <input type="search" name="search" placeholder="🔍 ابحث عن منتج..." value="<?= $search ?>">
        <select name="cat">
            <option value="">كل الفئات</option>
            <?php $categories->data_seek(0); while($c = $categories->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>" <?= $catFilter == $c['id'] ? 'selected' : '' ?>><?= sanitize($c['name_ar']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">بحث</button>
        <a href="products.php" class="btn btn-secondary btn-sm">مسح</a>
    </form>
    <a href="add_product.php" class="btn btn-primary">➕ إضافة منتج</a>
</div>

<div class="admin-table-card">
    <div class="table-header">
        <h3>📦 قائمة المنتجات (<?= $products->num_rows ?>)</h3>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>الصورة</th>
                <th>اسم المنتج</th>
                <th>الفئة</th>
                <th>السعر</th>
                <th>السعر القديم</th>
                <th>المخزون</th>
                <th>مميز</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if($products->num_rows > 0): ?>
            <?php while($p = $products->fetch_assoc()): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td>
                    <img src="../<?= sanitize($p['image']) ?>" class="product-thumb" alt="" onerror="this.src='../img/store.jpg'">
                </td>
                <td>
                    <strong><?= sanitize($p['name']) ?></strong>
                    <?php if($p['name_ar']): ?><br><small style="color:var(--text-muted);"><?= sanitize($p['name_ar']) ?></small><?php endif; ?>
                </td>
                <td><?= sanitize($p['cat_name_ar'] ?? '-') ?></td>
                <td style="color:var(--primary-light);font-weight:700;">$<?= number_format($p['price'], 2) ?></td>
                <td style="color:var(--text-muted);text-decoration:line-through;"><?= $p['old_price'] ? '$' . number_format($p['old_price'], 2) : '-' ?></td>
                <td>
                    <span class="badge <?= $p['stock'] > 10 ? 'badge-success' : ($p['stock'] > 0 ? 'badge-warning' : 'badge-danger') ?>">
                        <?= $p['stock'] ?>
                    </span>
                </td>
                <td><?= $p['featured'] ? '<span class="badge badge-warning">⭐ مميز</span>' : '-' ?></td>
                <td>
                    <a href="products.php?toggle=<?= $p['id'] ?>" class="badge <?= $p['status'] ? 'badge-success' : 'badge-danger' ?>" style="cursor:pointer;text-decoration:none;">
                        <?= $p['status'] ? '✅ نشط' : '❌ مخفي' ?>
                    </a>
                </td>
                <td>
                    <div style="display:flex;gap:0.5rem;">
                        <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                        <a href="products.php?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">🗑️</a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr><td colspan="10"><div class="admin-empty"><div class="empty-icon">📦</div><p>لا توجد منتجات</p></div></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div></main>
</body></html>
