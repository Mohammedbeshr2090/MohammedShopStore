<?php
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../admin/login.php');

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('products.php');

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) redirect('products.php');

$categories = $conn->query("SELECT * FROM categories WHERE status=1 ORDER BY name_ar");
$pageTitle    = '✏️ تعديل منتج | Edit Product';
$pageSubtitle = sanitize($product['name']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = sanitize($_POST['name'] ?? '');
    $name_ar     = sanitize($_POST['name_ar'] ?? '');
    $desc        = sanitize($_POST['description'] ?? '');
    $desc_ar     = sanitize($_POST['description_ar'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $old_price   = !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null;
    $stock       = (int)($_POST['stock'] ?? 0);
    $cat_id      = (int)($_POST['category_id'] ?? 0) ?: null;
    $featured    = isset($_POST['featured']) ? 1 : 0;
    $status      = isset($_POST['status']) ? 1 : 0;

    $imagePath = $product['image'];
    $hoverPath = $product['hover_image'];
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fn  = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fn);
        $imagePath = 'uploads/' . $fn;
    }
    if (!empty($_FILES['hover_image']['name'])) {
        $ext = pathinfo($_FILES['hover_image']['name'], PATHINFO_EXTENSION);
        $fn  = 'product_hover_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['hover_image']['tmp_name'], $uploadDir . $fn);
        $hoverPath = 'uploads/' . $fn;
    }

    $stmt = $conn->prepare("UPDATE products SET category_id=?, name=?, name_ar=?, description=?, description_ar=?, price=?, old_price=?, image=?, hover_image=?, stock=?, featured=?, status=? WHERE id=?");
    $stmt->bind_param("issssddssiiii", $cat_id, $name, $name_ar, $desc, $desc_ar, $price, $old_price, $imagePath, $hoverPath, $stock, $featured, $status, $id);

    if ($stmt->execute()) {
        setFlash('success', 'تم تحديث المنتج بنجاح!');
        redirect('products.php');
    } else {
        setFlash('error', 'حدث خطأ أثناء التحديث');
    }
}

require_once 'includes/admin_header.php';
?>

<div style="display:flex;justify-content:flex-end;margin-bottom:2rem;">
    <a href="products.php" class="btn btn-secondary">← العودة للمنتجات</a>
</div>

<form action="edit_product.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
    <div class="admin-form-card">
        <h3>📦 معلومات المنتج</h3>
        <div class="form-row">
            <div class="form-group">
                <label>اسم المنتج (إنجليزي) *</label>
                <input type="text" name="name" required value="<?= sanitize($product['name']) ?>">
            </div>
            <div class="form-group">
                <label>اسم المنتج (عربي)</label>
                <input type="text" name="name_ar" value="<?= sanitize($product['name_ar'] ?? '') ?>">
            </div>
            <div class="form-group form-full">
                <label>الوصف (إنجليزي)</label>
                <textarea name="description"><?= sanitize($product['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group form-full">
                <label>الوصف (عربي)</label>
                <textarea name="description_ar"><?= sanitize($product['description_ar'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="admin-form-card">
        <h3>💰 السعر والمخزون</h3>
        <div class="form-row cols-3">
            <div class="form-group">
                <label>السعر الحالي ($) *</label>
                <input type="number" name="price" step="0.01" required value="<?= $product['price'] ?>">
            </div>
            <div class="form-group">
                <label>السعر القديم ($)</label>
                <input type="number" name="old_price" step="0.01" value="<?= $product['old_price'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>الكمية في المخزون</label>
                <input type="number" name="stock" min="0" value="<?= $product['stock'] ?>">
            </div>
            <div class="form-group">
                <label>الفئة</label>
                <select name="category_id">
                    <option value="">-- بدون فئة --</option>
                    <?php while($c = $categories->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>><?= sanitize($c['name_ar']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:1rem;padding-top:3rem;">
                <label style="display:flex;align-items:center;gap:0.8rem;cursor:pointer;margin:0;">
                    <input type="checkbox" name="featured" style="width:2rem;height:2rem;accent-color:var(--primary);" <?= $product['featured'] ? 'checked' : '' ?>>
                    ⭐ منتج مميز
                </label>
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:1rem;padding-top:3rem;">
                <label style="display:flex;align-items:center;gap:0.8rem;cursor:pointer;margin:0;">
                    <input type="checkbox" name="status" style="width:2rem;height:2rem;accent-color:var(--success);" <?= $product['status'] ? 'checked' : '' ?>>
                    ✅ نشط ومرئي
                </label>
            </div>
        </div>
    </div>

    <div class="admin-form-card">
        <h3>🖼️ صور المنتج</h3>
        <div class="form-row">
            <div class="form-group">
                <label>الصورة الرئيسية (اترك فارغاً للاحتفاظ بالحالية)</label>
                <input type="file" name="image" accept="image/*" onchange="previewImg(this, 'prev1')">
                <div class="img-preview" id="prev1">
                    <?php if($product['image']): ?>
                    <img src="../<?= sanitize($product['image']) ?>" style="width:100%;height:100%;object-fit:contain;" onerror="this.style.display='none'">
                    <?php else: ?><span style="color:var(--text-muted);font-size:1.3rem;">لا توجد صورة</span><?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label>صورة التحويم (اترك فارغاً للاحتفاظ بالحالية)</label>
                <input type="file" name="hover_image" accept="image/*" onchange="previewImg(this, 'prev2')">
                <div class="img-preview" id="prev2">
                    <?php if($product['hover_image']): ?>
                    <img src="../<?= sanitize($product['hover_image']) ?>" style="width:100%;height:100%;object-fit:contain;" onerror="this.style.display='none'">
                    <?php else: ?><span style="color:var(--text-muted);font-size:1.3rem;">لا توجد صورة</span><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:1.5rem;">
        <button type="submit" class="btn btn-primary btn-lg">💾 حفظ التعديلات</button>
        <a href="products.php" class="btn btn-secondary btn-lg">إلغاء</a>
    </div>
</form>

<script>
function previewImg(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:contain;">`; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</div></main>
</body></html>
