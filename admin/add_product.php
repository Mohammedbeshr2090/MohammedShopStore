<?php
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../admin/login.php');

$pageTitle    = '➕ إضافة منتج | Add Product';
$pageSubtitle = 'أضف منتجاً جديداً إلى المتجر';
$categories   = $conn->query("SELECT * FROM categories WHERE status=1 ORDER BY name_ar");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = sanitize($_POST['name'] ?? '');
    $name_ar      = sanitize($_POST['name_ar'] ?? '');
    $desc         = sanitize($_POST['description'] ?? '');
    $desc_ar      = sanitize($_POST['description_ar'] ?? '');
    $price        = (float)($_POST['price'] ?? 0);
    $old_price    = !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null;
    $stock        = (int)($_POST['stock'] ?? 0);
    $category_id  = (int)($_POST['category_id'] ?? 0);
    $featured     = isset($_POST['featured']) ? 1 : 0;
    $status       = isset($_POST['status']) ? 1 : 0;

    if (!$name || $price <= 0) {
        setFlash('error', 'يرجى ملء اسم المنتج والسعر');
        redirect('add_product.php');
    }

    // Handle image upload
    $imagePath = '';
    $hoverPath = '';
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if (!empty($_FILES['image']['name'])) {
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            setFlash('error', 'نوع الملف غير مسموح به. يُسمح بـ JPG, PNG, WebP فقط');
            redirect('add_product.php');
        }
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName);
        $imagePath = 'uploads/' . $fileName;
    }

    if (!empty($_FILES['hover_image']['name'])) {
        $ext = pathinfo($_FILES['hover_image']['name'], PATHINFO_EXTENSION);
        $fileName = 'product_hover_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['hover_image']['tmp_name'], $uploadDir . $fileName);
        $hoverPath = 'uploads/' . $fileName;
    }

    $stmt = $conn->prepare("INSERT INTO products (category_id, name, name_ar, description, description_ar, price, old_price, image, hover_image, stock, featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $catId = $category_id ?: null;
    $stmt->bind_param("issssddssiii", $catId, $name, $name_ar, $desc, $desc_ar, $price, $old_price, $imagePath, $hoverPath, $stock, $featured, $status);

    if ($stmt->execute()) {
        setFlash('success', 'تم إضافة المنتج بنجاح!');
        redirect('products.php');
    } else {
        setFlash('error', 'حدث خطأ أثناء الإضافة');
    }
}

require_once 'includes/admin_header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
    <div></div>
    <a href="products.php" class="btn btn-secondary">← العودة للمنتجات</a>
</div>

<form action="add_product.php" method="POST" enctype="multipart/form-data">
    <div class="admin-form-card">
        <h3>📦 معلومات المنتج</h3>
        <div class="form-row">
            <div class="form-group">
                <label>اسم المنتج (إنجليزي) *</label>
                <input type="text" name="name" placeholder="e.g. iPhone 15 Pro Max" required value="<?= isset($_POST['name']) ? sanitize($_POST['name']) : '' ?>">
            </div>
            <div class="form-group">
                <label>اسم المنتج (عربي)</label>
                <input type="text" name="name_ar" placeholder="مثال: آيفون 15 برو ماكس" value="<?= isset($_POST['name_ar']) ? sanitize($_POST['name_ar']) : '' ?>">
            </div>
            <div class="form-group form-full">
                <label>الوصف (إنجليزي)</label>
                <textarea name="description" placeholder="Product description in English..."><?= isset($_POST['description']) ? sanitize($_POST['description']) : '' ?></textarea>
            </div>
            <div class="form-group form-full">
                <label>الوصف (عربي)</label>
                <textarea name="description_ar" placeholder="وصف المنتج بالعربية..."><?= isset($_POST['description_ar']) ? sanitize($_POST['description_ar']) : '' ?></textarea>
            </div>
        </div>
    </div>

    <div class="admin-form-card">
        <h3>💰 السعر والمخزون</h3>
        <div class="form-row cols-3">
            <div class="form-group">
                <label>السعر الحالي ($) *</label>
                <input type="number" name="price" step="0.01" min="0" placeholder="0.00" required value="<?= isset($_POST['price']) ? sanitize($_POST['price']) : '' ?>">
            </div>
            <div class="form-group">
                <label>السعر القديم ($) (اختياري)</label>
                <input type="number" name="old_price" step="0.01" min="0" placeholder="0.00" value="<?= isset($_POST['old_price']) ? sanitize($_POST['old_price']) : '' ?>">
            </div>
            <div class="form-group">
                <label>الكمية في المخزون *</label>
                <input type="number" name="stock" min="0" placeholder="0" required value="<?= isset($_POST['stock']) ? sanitize($_POST['stock']) : '0' ?>">
            </div>
            <div class="form-group">
                <label>الفئة</label>
                <select name="category_id">
                    <option value="">-- اختر فئة --</option>
                    <?php while($c = $categories->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $c['id']) ? 'selected' : '' ?>><?= sanitize($c['name_ar']) ?> | <?= sanitize($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:1rem;padding-top:3rem;">
                <label style="display:flex;align-items:center;gap:0.8rem;cursor:pointer;margin:0;">
                    <input type="checkbox" name="featured" style="width:2rem;height:2rem;accent-color:var(--primary);" <?= isset($_POST['featured']) ? 'checked' : '' ?>>
                    ⭐ منتج مميز
                </label>
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:1rem;padding-top:3rem;">
                <label style="display:flex;align-items:center;gap:0.8rem;cursor:pointer;margin:0;">
                    <input type="checkbox" name="status" style="width:2rem;height:2rem;accent-color:var(--success);" checked <?= isset($_POST['status']) ? 'checked' : '' ?>>
                    ✅ نشط ومرئي
                </label>
            </div>
        </div>
    </div>

    <div class="admin-form-card">
        <h3>🖼️ صور المنتج</h3>
        <div class="form-row">
            <div class="form-group">
                <label>الصورة الرئيسية</label>
                <input type="file" name="image" accept="image/*" onchange="previewImg(this, 'prev1')">
                <div class="img-preview" id="prev1">
                    <span style="color:var(--text-muted);font-size:1.3rem;">اختر صورة</span>
                </div>
            </div>
            <div class="form-group">
                <label>صورة التحويم (Hover)</label>
                <input type="file" name="hover_image" accept="image/*" onchange="previewImg(this, 'prev2')">
                <div class="img-preview" id="prev2">
                    <span style="color:var(--text-muted);font-size:1.3rem;">اختر صورة</span>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:1.5rem;">
        <button type="submit" class="btn btn-primary btn-lg">✅ حفظ المنتج</button>
        <a href="products.php" class="btn btn-secondary btn-lg">إلغاء</a>
    </div>
</form>

<script>
function previewImg(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:contain;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</div></main>
</body></html>
