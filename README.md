<div align="center">

# 🛒 Mohammed Shop — متجر محمد

### A Full-Stack Bilingual E-Commerce Platform | منصة تجارة إلكترونية ثنائية اللغة

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![XAMPP](https://img.shields.io/badge/XAMPP-Compatible-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)](https://apachefriends.org)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

</div>

---

## 📌 Overview | نظرة عامة

**Mohammed Shop** is a complete, production-ready e-commerce web application built with **PHP**, **MySQL**, and **Vanilla CSS/JS**.  
It supports both **Arabic (RTL)** and **English (LTR)** languages out of the box, allowing seamless switching between them.

**متجر محمد** هو تطبيق متجر إلكتروني متكامل مبني بـ **PHP** و **MySQL** و **CSS/JS** خالص.  
يدعم اللغتين **العربية (RTL)** و **الإنجليزية (LTR)** مع إمكانية التبديل السلس بينهما.

> 🔥 **Live Demo Categories:** Smartphones · Smartwatches · Headphones · Speakers · Cameras · Televisions · Games

---

## ✨ Features | المميزات

### 🛍️ Customer-Facing Features | ميزات العميل

| Feature | Description |
|---------|-------------|
| 🌐 **Bilingual Support** | Full Arabic (RTL) & English (LTR) toggle |
| 🏠 **Home Page** | Hero section, promotional banners, featured products, category grid |
| 📦 **Products Catalog** | Browse, filter by category, search, pagination |
| 🔍 **Product Details** | Image hover effects, price with discount badge, add to cart |
| 🛒 **Shopping Cart** | Add / update / remove items with real-time quantity control |
| 💳 **Checkout** | Shipping address, phone, payment method (Cash on Delivery), order notes |
| 👤 **User Authentication** | Register, Login, Logout with session management |
| 🔒 **CSRF Protection** | All forms protected with CSRF tokens |
| 📩 **Contact Form** | Message submission with subject and email |
| ℹ️ **About Page** | Store information and brand identity |

### 🔧 Admin Panel | لوحة التحكم

| Feature | Description |
|---------|-------------|
| 📊 **Dashboard** | Stats: total products, orders, users, revenue, pending orders |
| 📦 **Product Management** | Add, edit, delete products with image upload (main + hover) |
| 📁 **Category Management** | Add, edit, delete categories with images |
| 🛒 **Order Management** | View all orders, update status (pending → processing → shipped → delivered) |
| 👥 **User Management** | View registered users and their details |
| 📨 **Messages Center** | Read customer messages, mark as read/unread |
| 🔑 **Admin Login** | Separate, protected admin authentication |

---

## 🗂️ Project Structure | هيكل المشروع

```
store1/
├── index.php                  # Home page
├── products.php               # Products catalog
├── product_details.php        # Single product page
├── cart.php                   # Shopping cart
├── search.php                 # Search results
├── about.php                  # About us page
├── contact.php                # Contact form
├── login.php                  # User login
├── register.php               # User registration
├── setup.php                  # ⚙️ One-click database installer
│
├── admin/                     # 🔐 Admin Panel
│   ├── index.php              # Dashboard (stats & recent activity)
│   ├── login.php              # Admin authentication
│   ├── products.php           # Product list management
│   ├── add_product.php        # Add new product
│   ├── edit_product.php       # Edit existing product
│   ├── categories.php         # Category management
│   ├── orders.php             # Order management
│   ├── users.php              # User management
│   ├── messages.php           # Customer messages
│   └── includes/              # Admin header/sidebar
│
├── auth/                      # 🔑 Authentication Logic
│   ├── login_process.php
│   ├── register_process.php
│   └── logout.php
│
├── cart/                      # 🛒 Cart Operations
│   ├── add_to_cart.php
│   ├── update_cart.php
│   ├── remove_from_cart.php
│   └── checkout.php
│
├── config/
│   └── db.php                 # Database connection
│
├── includes/                  # Shared Components
│   ├── header.php             # Global header + navbar
│   ├── footer.php             # Global footer
│   └── functions.php          # Helper functions & utilities
│
├── css/                       # Stylesheets
├── js/                        # JavaScript files
├── img/                       # Product & category images
└── uploads/                   # Admin-uploaded files
```

---

## 🗄️ Database Schema | هيكل قاعدة البيانات

```
store1_db
├── users          -- Customers & admins (role: user | admin)
├── categories     -- Product categories (bilingual: name, name_ar)
├── products       -- Products (bilingual, price, old_price, stock, featured)
├── cart           -- Shopping cart (user_id → product_id → quantity)
├── orders         -- Customer orders (status workflow)
├── order_items    -- Order line items (order_id → product_id → qty, price)
├── messages       -- Contact form submissions
└── site_settings  -- Key-value store for site configuration
```

**Order Status Flow:**
```
pending → processing → shipped → delivered
                                     ↘ cancelled (at any stage)
```

---

## 🚀 Installation | التثبيت

### Prerequisites | المتطلبات

- **XAMPP** (or Apache + PHP 7.4+ + MySQL 5.7+)
- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher
- A modern web browser

### Steps | خطوات التثبيت

**1. Clone the Repository**
```bash
git clone https://github.com/Mohammedbeshr2090/MohammedShopStore.git
```

**2. Move to XAMPP Web Root**
```bash
# Copy or move the folder to:
C:\xampp\htdocs\store1\           # Windows
/opt/lampp/htdocs/store1/         # Linux
/Applications/XAMPP/htdocs/store1/ # macOS
```

**3. Start XAMPP Services**

Start **Apache** and **MySQL** from the XAMPP Control Panel.

**4. Run the Setup Script**

Open your browser and navigate to:
```
http://localhost/store1/setup.php
```

This will automatically:
- ✅ Create the `store1_db` database
- ✅ Create all required tables
- ✅ Insert default admin account
- ✅ Seed sample categories (7 categories)
- ✅ Seed sample products (12 products)
- ✅ Configure default site settings

**5. Open the Store**
```
http://localhost/store1/
```

---

## 🔑 Default Credentials | بيانات الدخول الافتراضية

### Admin Account | حساب المدير
```
URL:       http://localhost/store1/admin/login.php
Email:     admin0@gmail.com
Password:  admin01
```

> ⚠️ **Important:** Change the default admin credentials immediately after first login in a production environment.

---

## 🛠️ Tech Stack | التقنيات المستخدمة

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8.x (Procedural + OOP with MySQLi) |
| **Database** | MySQL 8.x with InnoDB engine |
| **Frontend** | HTML5, Vanilla CSS3, Vanilla JavaScript |
| **Server** | Apache (via XAMPP) |
| **Security** | Password hashing (`password_hash`), CSRF tokens, input sanitization |
| **i18n** | Custom bilingual helper `t($ar, $en)` + RTL/LTR CSS direction |

---

## 🔐 Security Features | ميزات الأمان

- **Password Hashing** — All passwords stored using `password_hash()` (bcrypt)
- **CSRF Protection** — Token-based protection on all state-changing forms
- **Input Sanitization** — `htmlspecialchars()` applied to all user outputs
- **Prepared Statements** — All database queries use MySQLi prepared statements (SQL injection prevention)
- **Session-based Auth** — Role-based access control (user / admin)
- **Admin Guard** — Every admin page verifies `isAdmin()` before rendering

---

## 📸 Pages Overview | الصفحات

| Page | URL | Description |
|------|-----|-------------|
| Home | `/index.php` | Hero, banners, categories, featured products |
| Products | `/products.php` | Full product listing with category filter |
| Product Detail | `/product_details.php?id=X` | Full product info + add to cart |
| Cart | `/cart.php` | Cart management + subtotal |
| Checkout | `/cart/checkout.php` | Order placement form |
| Search | `/search.php?q=...` | Keyword product search |
| Login | `/login.php` | User login |
| Register | `/register.php` | New user registration |
| About | `/about.php` | About the store |
| Contact | `/contact.php` | Contact form |
| **Admin Dashboard** | `/admin/` | Stats & quick actions |
| **Admin Products** | `/admin/products.php` | Full product management |
| **Admin Orders** | `/admin/orders.php` | Order status management |

---

## 🌍 Bilingual System | نظام ثنائي اللغة

The store includes a custom bilingual translation helper:

```php
// Switch language via URL parameter
?lang=ar   // Switch to Arabic (RTL)
?lang=en   // Switch to English (LTR)

// Usage in templates
echo t('مرحباً', 'Hello');
echo t('أضف للسلة', 'Add to Cart');
```

Language preference is stored in the session and persists across all pages.

---

## 🤝 Contributing | المساهمة

Contributions are welcome! Please feel free to:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License | الرخصة

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.

---

<div align="center">

Made with ❤️ by **Mohammed**  صُنع بـ ❤️ بواسطة **محمد**

</div>
