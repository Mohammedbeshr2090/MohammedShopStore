/* ==========================================
   Mohammed Shop - Main JavaScript
   ========================================== */

// ===== Language Switch =====
function switchLang() {
    const url = new URL(window.location.href);
    const currentLang = document.documentElement.lang || 'ar';
    url.searchParams.set('lang', currentLang === 'ar' ? 'en' : 'ar');
    window.location.href = url.toString();
}

// ===== Detect base path =====
const _basePath = (() => {
    // Detect if we are in a subdirectory
    const scripts = document.querySelectorAll('script[src*="main.js"]');
    for (const s of scripts) {
        if (s.src.includes('../js/main.js')) return '../';
    }
    return '';
})();

const _cartBase = _basePath + 'cart/';

// ===== Header Scroll Effect =====
window.addEventListener('scroll', () => {
    const header = document.getElementById('header');
    if (header) {
        header.classList.toggle('scrolled', window.scrollY > 50);
    }
});

// ===== Mobile Menu Toggle =====
function toggleMenu() {
    const nav = document.getElementById('navLinks');
    if (nav) {
        nav.classList.toggle('active');
    }
}

// Close menu when clicking a link
document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        const nav = document.getElementById('navLinks');
        if (nav) nav.classList.remove('active');
    });
});

// ===== Toast Notification =====
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    if (!toast || !toastMessage) return;

    toast.className = 'toast ' + type;
    toastMessage.textContent = message;
    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// ===== Login Required Modal =====
function showLoginModal() {
    // Remove existing modal if any
    const existing = document.getElementById('loginRequiredModal');
    if (existing) existing.remove();

    const isAr = document.documentElement.lang === 'ar';

    // Find the login page URL relative to current page
    const loginUrl = _basePath + 'login.php';

    const modal = document.createElement('div');
    modal.id = 'loginRequiredModal';
    modal.innerHTML = `
        <div class="lrm-overlay" onclick="closeLoginModal()"></div>
        <div class="lrm-box">
            <div class="lrm-icon">🔐</div>
            <h3>${isAr ? 'تسجيل الدخول مطلوب' : 'Login Required'}</h3>
            <p>${isAr
                ? 'يجب عليك تسجيل الدخول أولاً لإضافة المنتجات إلى سلة التسوق'
                : 'You need to log in first to add products to your shopping cart'}</p>
            <div class="lrm-actions">
                <a href="${loginUrl}?redirect=${encodeURIComponent(window.location.href)}" class="lrm-btn-login">
                    ${isAr ? '🚀 تسجيل الدخول' : '🚀 Login Now'}
                </a>
                <button onclick="closeLoginModal()" class="lrm-btn-cancel">
                    ${isAr ? 'إلغاء' : 'Cancel'}
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    // Inject styles if not already injected
    if (!document.getElementById('loginModalStyle')) {
        const style = document.createElement('style');
        style.id = 'loginModalStyle';
        style.textContent = `
            #loginRequiredModal {
                position: fixed; inset: 0; z-index: 99999;
                display: flex; align-items: center; justify-content: center;
                animation: lrmFadeIn 0.3s ease;
            }
            @keyframes lrmFadeIn { from { opacity:0; } to { opacity:1; } }
            .lrm-overlay {
                position: absolute; inset: 0;
                background: rgba(0,0,0,0.75);
                backdrop-filter: blur(6px);
            }
            .lrm-box {
                position: relative; z-index: 1;
                background: linear-gradient(135deg, #1a1a3e, #2d1b69);
                border: 1px solid rgba(124,58,237,0.4);
                border-radius: 20px;
                padding: 4rem 3rem;
                text-align: center;
                max-width: 42rem;
                width: 90%;
                box-shadow: 0 20px 60px rgba(124,58,237,0.3);
                animation: lrmSlideUp 0.35s cubic-bezier(0.34,1.56,0.64,1);
            }
            @keyframes lrmSlideUp {
                from { opacity:0; transform:translateY(40px) scale(0.9); }
                to   { opacity:1; transform:translateY(0)  scale(1);   }
            }
            .lrm-icon { font-size: 5rem; margin-bottom: 1.5rem; display: block;
                animation: lrmBounce 0.6s 0.3s both; }
            @keyframes lrmBounce {
                0%   { transform: scale(0); }
                60%  { transform: scale(1.2); }
                100% { transform: scale(1); }
            }
            .lrm-box h3 {
                font-size: 2.2rem; font-weight: 800;
                color: #f1f5f9; margin-bottom: 1rem;
                font-family: 'Cairo', 'Inter', sans-serif;
            }
            .lrm-box p {
                font-size: 1.5rem; color: #94a3b8;
                line-height: 1.7; margin-bottom: 2.5rem;
                font-family: 'Cairo', 'Inter', sans-serif;
            }
            .lrm-actions { display: flex; gap: 1.2rem; justify-content: center; flex-wrap: wrap; }
            .lrm-btn-login {
                display: inline-flex; align-items: center; gap: 0.6rem;
                padding: 1.2rem 2.8rem;
                background: linear-gradient(135deg, #7c3aed, #a78bfa);
                color: #fff; border: none; border-radius: 12px;
                font-size: 1.5rem; font-weight: 700;
                text-decoration: none; cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Cairo', 'Inter', sans-serif;
                box-shadow: 0 4px 20px rgba(124,58,237,0.4);
            }
            .lrm-btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(124,58,237,0.5); }
            .lrm-btn-cancel {
                padding: 1.2rem 2rem;
                background: rgba(255,255,255,0.08);
                color: #94a3b8; border: 1px solid rgba(255,255,255,0.15);
                border-radius: 12px; font-size: 1.5rem; font-weight: 600;
                cursor: pointer; transition: all 0.3s ease;
                font-family: 'Cairo', 'Inter', sans-serif;
            }
            .lrm-btn-cancel:hover { background: rgba(255,255,255,0.15); color: #f1f5f9; }
        `;
        document.head.appendChild(style);
    }

    // Close on ESC
    document.addEventListener('keydown', function escHandler(e) {
        if (e.key === 'Escape') { closeLoginModal(); document.removeEventListener('keydown', escHandler); }
    });
}

function closeLoginModal() {
    const modal = document.getElementById('loginRequiredModal');
    if (modal) {
        modal.style.animation = 'lrmFadeIn 0.2s ease reverse forwards';
        setTimeout(() => modal.remove(), 200);
    }
}

function addToCart(productId) {
    fetch(_cartBase + 'add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'product_id=' + productId
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // Update cart badge
            const badge = document.getElementById('cartBadge');
            if (badge) {
                badge.textContent = data.cart_count;
            } else {
                // Create badge if not exists
                const cartLink = document.querySelector('a[href$="cart.php"]');
                if (cartLink) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'cart-badge';
                    newBadge.id = 'cartBadge';
                    newBadge.textContent = data.cart_count;
                    cartLink.appendChild(newBadge);
                }
            }
        } else if (data.needs_login) {
            // Show login required modal
            showLoginModal();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('حدث خطأ!', 'error');
    });
}

// ===== Update Cart Quantity =====
function updateQuantity(cartId, change) {
    const qtyEl = document.getElementById('qty-' + cartId);
    if (!qtyEl) return;

    let newQty = parseInt(qtyEl.textContent) + change;
    if (newQty < 1) newQty = 1;
    if (newQty > 99) newQty = 99;

    fetch(_cartBase + 'update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'cart_id=' + cartId + '&quantity=' + newQty
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            qtyEl.textContent = newQty;
            // Update totals
            if (data.item_total) {
                const itemTotal = document.getElementById('item-total-' + cartId);
                if (itemTotal) itemTotal.textContent = data.item_total;
            }
            updateCartSummary();
            // Update badge
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.cart_count;
        }
    })
    .catch(err => console.error(err));
}

// ===== Remove from Cart =====
function removeFromCart(cartId) {
    fetch(_cartBase + 'remove_from_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'cart_id=' + cartId
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const item = document.getElementById('cart-item-' + cartId);
            if (item) {
                item.style.opacity = '0';
                item.style.transform = 'translateX(50px)';
                setTimeout(() => {
                    item.remove();
                    updateCartSummary();
                    // Check if cart is empty
                    const items = document.querySelectorAll('.cart-item');
                    if (items.length === 0) {
                        location.reload();
                    }
                }, 300);
            }
            showToast(data.message, 'success');
            // Update badge
            const badge = document.getElementById('cartBadge');
            if (badge) {
                if (data.cart_count > 0) {
                    badge.textContent = data.cart_count;
                } else {
                    badge.remove();
                }
            }
        }
    })
    .catch(err => console.error(err));
}

// ===== Update Cart Summary =====
function updateCartSummary() {
    fetch(_cartBase + 'update_cart.php?summary=1')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const subtotal = document.getElementById('cartSubtotal');
            const total = document.getElementById('cartTotal');
            if (subtotal) subtotal.textContent = data.subtotal;
            if (total) total.textContent = data.total;
        }
    })
    .catch(err => console.error(err));
}

// ===== Language Switch =====
document.querySelectorAll('.lang-toggle').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const currentUrl = new URL(window.location.href);
        const newLang = this.textContent.trim() === 'EN' ? 'en' : 'ar';
        currentUrl.searchParams.set('lang', newLang);
        window.location.href = currentUrl.toString();
    });
});

// ===== Flash message auto-dismiss =====
document.addEventListener('DOMContentLoaded', () => {
    const flash = document.querySelector('.flash-message');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateX(100%)';
            setTimeout(() => flash.remove(), 400);
        }, 4000);
    }
});

// ===== Smooth Scroll =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
