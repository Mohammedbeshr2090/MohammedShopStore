<?php
/**
 * Mohammed Shop - Database Setup Script
 * Run this file once to create the database and tables
 */

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create database
    $conn->query("CREATE DATABASE IF NOT EXISTS store1_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db('store1_db');

    // Users table
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        phone VARCHAR(20) DEFAULT NULL,
        address TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Categories table
    $conn->query("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        name_ar VARCHAR(100) NOT NULL,
        image VARCHAR(255) DEFAULT NULL,
        status TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Products table
    $conn->query("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT DEFAULT NULL,
        name VARCHAR(200) NOT NULL,
        name_ar VARCHAR(200) DEFAULT NULL,
        description TEXT DEFAULT NULL,
        description_ar TEXT DEFAULT NULL,
        price DECIMAL(10,2) NOT NULL,
        old_price DECIMAL(10,2) DEFAULT NULL,
        image VARCHAR(255) DEFAULT NULL,
        hover_image VARCHAR(255) DEFAULT NULL,
        stock INT DEFAULT 0,
        featured TINYINT(1) DEFAULT 0,
        status TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Cart table
    $conn->query("CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Orders table
    $conn->query("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
        payment_method VARCHAR(50) DEFAULT 'cod',
        shipping_address TEXT NOT NULL,
        phone VARCHAR(20) NOT NULL,
        notes TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Order items table
    $conn->query("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Messages table
    $conn->query("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL,
        subject VARCHAR(255) DEFAULT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Site settings table
    $conn->query("CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Insert default admin
    $adminPassword = password_hash('admin01', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT IGNORE INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
    $adminName = 'Admin';
    $adminEmail = 'admin0@gmail.com';
    $stmt->bind_param("sss", $adminName, $adminEmail, $adminPassword);
    $stmt->execute();

    // Insert default categories
    $categories = [
        ['Smartphones', 'هواتف ذكية', 'img/cat_img2.png'],
        ['Smartwatches', 'ساعات ذكية', 'img/cat_img4.png'],
        ['Headphones', 'سماعات', 'img/cat_img3.png'],
        ['Speakers', 'مكبرات صوت', 'img/cat_img7.png'],
        ['Cameras', 'كاميرات', 'img/cat_img6.png'],
        ['Televisions', 'تلفزيونات', 'img/cat_img1.png'],
        ['Games', 'ألعاب', 'img/cat_img5.png']
    ];

    $stmt = $conn->prepare("INSERT IGNORE INTO categories (name, name_ar, image) VALUES (?, ?, ?)");
    foreach ($categories as $cat) {
        $stmt->bind_param("sss", $cat[0], $cat[1], $cat[2]);
        $stmt->execute();
    }

    // Insert sample products
    $products = [
        [1, 'iPhone 15 Pro Max', 'آيفون 15 برو ماكس', 'Latest iPhone with A17 Pro chip', 'أحدث آيفون بمعالج A17 Pro', 2350.99, 2500.99, 'img/15.jpg', 'img/15 1.jpg', 10, 1],
        [1, 'iPhone 14 Pro', 'آيفون 14 برو', 'Powerful iPhone 14 Pro', 'آيفون 14 برو القوي', 1200.99, 1400.99, 'img/iphone 14 ³.jpg', 'img/iphone 14 ¹.jpg', 15, 1],
        [1, 'iPhone 13 Pro', 'آيفون 13 برو', 'iPhone 13 Pro with ProMotion', 'آيفون 13 برو مع شاشة ProMotion', 750.99, 899.99, 'img/iphone 13 ¹.jpg', 'img/iphone 13 ².jpg', 20, 1],
        [1, 'iPhone 12 Pro Max', 'آيفون 12 برو ماكس', 'iPhone 12 Pro Max with 5G', 'آيفون 12 برو ماكس مع 5G', 640.99, 799.99, 'img/iphone 12 ².jpg', 'img/iphone 12 ¹1.jpg', 12, 1],
        [1, 'iPhone 11 Pro', 'آيفون 11 برو', 'iPhone 11 Pro triple camera', 'آيفون 11 برو بثلاث كاميرات', 399.99, 499.99, 'img/iphone 11 ⁰.jpg', 'img/iphone 11 ²1.jpg', 8, 1],
        [1, 'iPhone 8', 'آيفون 8', 'Classic iPhone 8', 'آيفون 8 الكلاسيكي', 199.99, 299.99, 'img/iphone 8.jpg', 'img/iphone 8.jpg', 5, 0],
        [4, 'New Speakers', 'مكبر صوت جديد', 'Premium wireless speaker', 'مكبر صوت لاسلكي فاخر', 249.99, 399.99, 'img/mr1.jpg', 'img/20.jpg', 25, 1],
        [2, 'Smartwatch', 'ساعة ذكية', 'Premium smartwatch with health tracking', 'ساعة ذكية مع تتبع الصحة', 79.99, 99.99, 'img/product-6.jpg', 'img/product-6-hover.jpg', 30, 0],
        [3, 'Headphone', 'سماعة رأس', 'Wireless noise-cancelling headphone', 'سماعة لاسلكية بإلغاء الضوضاء', 39.99, 59.99, 'img/سماعات 1.jpg', 'img/سماعة 1.jpg', 50, 0],
        [5, 'Camera', 'كاميرا', 'Professional DSLR camera', 'كاميرا DSLR احترافية', 249.99, 399.99, 'img/product-2.jpg', 'img/product-2-hover.jpg', 7, 0],
        [6, 'Television', 'تلفزيون', 'Smart 4K TV', 'تلفزيون ذكي 4K', 549.99, 699.99, 'img/product-3.jpg', 'img/product-3-hover.jpg', 10, 0],
        [2, 'Smartwatch Pro', 'ساعة ذكية برو', 'Advanced smartwatch', 'ساعة ذكية متقدمة', 149.99, 199.99, 'img/ساعة 1.jpg', 'img/ساعة 2.jpg', 20, 0],
    ];

    $stmt = $conn->prepare("INSERT IGNORE INTO products (category_id, name, name_ar, description, description_ar, price, old_price, image, hover_image, stock, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        $stmt->bind_param("issssddssii", $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9], $p[10]);
        $stmt->execute();
    }

    // Insert default site settings
    $settings = [
        ['site_name', 'Mohammed Shop'],
        ['site_name_ar', 'متجر محمد'],
        ['site_email', 'info@mohammedshop.com'],
        ['site_phone', '+966 50 000 0000'],
        ['site_address', 'Taiz, Yemen'],
        ['currency', '$'],
    ];
    
    $stmt = $conn->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($settings as $s) {
        $stmt->bind_param("ss", $s[0], $s[1]);
        $stmt->execute();
    }

    $conn->close();

    echo "<!DOCTYPE html><html dir='rtl'><head><meta charset='UTF-8'><title>Setup Complete</title>
    <style>
        body{font-family:'Segoe UI',sans-serif;background:#0f0f23;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
        .box{background:linear-gradient(135deg,#1a1a3e,#2d1b69);padding:3rem;border-radius:20px;text-align:center;box-shadow:0 20px 60px rgba(108,47,255,0.3);max-width:500px}
        h1{color:#a78bfa;margin-bottom:1rem;font-size:2rem}
        p{color:#c4b5fd;font-size:1.1rem;line-height:1.8}
        .check{font-size:4rem;margin-bottom:1rem}
        a{display:inline-block;margin-top:2rem;padding:12px 30px;background:linear-gradient(135deg,#7c3aed,#a78bfa);color:#fff;text-decoration:none;border-radius:10px;font-size:1.1rem;transition:transform 0.3s}
        a:hover{transform:translateY(-3px)}
    </style></head><body>
    <div class='box'>
        <div class='check'>✅</div>
        <h1>تم التثبيت بنجاح!</h1>
        <p>تم إنشاء قاعدة البيانات والجداول بنجاح<br>
        تم إنشاء حساب الأدمن الافتراضي<br>
        تم إضافة الفئات والمنتجات التجريبية</p>
        <a href='index.php'>الذهاب إلى الموقع ←</a>
    </div></body></html>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
