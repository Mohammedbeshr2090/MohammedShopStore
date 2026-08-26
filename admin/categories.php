<?php
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../admin/login.php');

// Add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name    = sanitize($_POST['name'] ?? '');
    $name_ar = sanitize($_POST['name_ar'] ?? '');
    $image   = sanitize($_POST['image_path'] ?? '');
    if ($name && $name_ar) {
        $stmt = $conn->prepare("INSERT INTO categories (name, name_ar, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $name_ar, $image);
        $stmt->execute() ? setFlash('success', 'تمت إضافة الفئة بنجاح') : setFlash('error', 'حدث خطأ');
    }
    redirect('categories.php');
}

// Delete category
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $conn->query("UPDATE products SET category_id = NULL WHERE category_id = $delId");
    $conn->query("DELETE FROM categories WHERE id = $delId");
    setFlash('success', 'تم حذف الفئة');
    redirect('categories.php');
}

// Toggle status
if (isset($_GET['toggle'])) {
    $tId = (int)$_GET['toggle'];
    $conn->query("UPDATE categories SET status = 1 - status WHERE id = $tId");
    redirect('categories.php');
}

$pageTitle    = '📁 الفئات | Categories';
$pageSubtitle = 'إدارة فئات المنتجات';
$categories   = $conn->query("SELECT c.*, COUNT(p.id) as prod_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.created_at DESC");

require_once 'includes/admin_header.php';
?>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:2.5rem;align-items:start;">
    <!-- Add Category Form -->
    <div class="admin-form-card">
        <h3>➕ إضافة فئة جديدة</h3>
        <form action="categories.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>اسم الفئة (إنجليزي) *</label>
                <input type="text" name="name" placeholder="e.g. Smartphones" required>
            </div>
            <div class="form-group">
                <label>اسم الفئة (عربي) *</label>
                <input type="text" name="name_ar" placeholder="مثال: هواتف ذكية" required>
            </div>
            <div class="form-group">
                <label>مسار الصورة (اختياري)</label>
                <input type="text" name="image_path" placeholder="img/cat_img1.png">
            </div>
            <button type="submit" class="btn btn-primary btn-block">➕ إضافة الفئة</button>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="admin-table-card">
        <div class="table-header">
            <h3>📁 قائمة الفئات (<?= $categories->num_rows ?>)</h3>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الصورة</th>
                    <th>الاسم (عربي)</th>
                    <th>الاسم (إنجليزي)</th>
                    <th>المنتجات</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if($categories->num_rows > 0): ?>
                <?php while($c = $categories->fetch_assoc()): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td>
                        <?php if($c['image']): ?>
                        <img src="../<?= sanitize($c['image']) ?>" class="product-thumb" alt="" onerror="this.style.display='none'">
                        <?php else: ?>
                        <span style="color:var(--text-muted);">—</span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= sanitize($c['name_ar']) ?></strong></td>
                    <td><?= sanitize($c['name']) ?></td>
                    <td><span class="badge badge-info"><?= $c['prod_count'] ?> منتج</span></td>
                    <td>
                        <a href="categories.php?toggle=<?= $c['id'] ?>" class="badge <?= $c['status'] ? 'badge-success' : 'badge-danger' ?>" style="cursor:pointer;text-decoration:none;">
                            <?= $c['status'] ? '✅ نشطة' : '❌ مخفية' ?>
                        </a>
                    </td>
                    <td>
                        <a href="categories.php?delete=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل تريد حذف هذه الفئة؟ سيتم إزالة ارتباطها بالمنتجات')">🗑️ حذف</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr><td colspan="7"><div class="admin-empty"><div class="empty-icon">📁</div><p>لا توجد فئات</p></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div></main>
</body></html>
