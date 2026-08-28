<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FoodOrder - Đặt Món Ăn Ngon Giao Nhanh Tận Nơi')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #FF5722;
            --primary-hover: #E64A19;
            --primary-light: #FFEDE6;
            --accent: #FF9800;
            --dark: #0F172A;
            --dark-muted: #334155;
            --light-bg: #F8FAFC;
            --card-bg: #FFFFFF;
            --border-color: #E2E8F0;
            --success: #10B981;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 14px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 25px -5px rgba(255, 87, 34, 0.15);
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Container */
        .container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header & Navbar */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .nav-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 76px;
            gap: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--dark);
            font-weight: 800;
            font-size: 24px;
            letter-spacing: -0.5px;
        }

        .logo-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 10px rgba(255, 87, 34, 0.35);
        }

        .logo span {
            color: var(--primary);
        }

        /* Search box in header */
        .nav-search {
            flex: 1;
            max-width: 480px;
            position: relative;
        }

        .nav-search form {
            display: flex;
            align-items: center;
            position: relative;
        }

        .nav-search input {
            width: 100%;
            padding: 12px 18px 12px 44px;
            border-radius: 50px;
            border: 1px solid var(--border-color);
            background: #F1F5F9;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: var(--transition);
        }

        .nav-search input:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255, 87, 34, 0.12);
        }

        .nav-search i {
            position: absolute;
            left: 16px;
            color: #94A3B8;
            font-size: 16px;
        }

        /* Nav actions */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .cart-trigger {
            position: relative;
            background: white;
            border: 1px solid var(--border-color);
            padding: 10px 18px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            color: var(--dark);
            transition: var(--transition);
        }

        .cart-trigger:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        .cart-badge {
            background: var(--primary);
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .hotline-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(255, 87, 34, 0.25);
            transition: var(--transition);
        }

        .hotline-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(255, 87, 34, 0.35);
        }

        /* Main Content */
        main {
            flex: 1;
        }

        /* Cart Drawer */
        .cart-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .cart-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .cart-drawer {
            position: fixed;
            top: 0;
            right: -420px;
            width: 100%;
            max-width: 420px;
            height: 100%;
            background: white;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.15);
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .cart-drawer.active {
            right: 0;
        }

        .cart-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-header h3 {
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .close-cart {
            background: none;
            border: none;
            font-size: 20px;
            color: #64748B;
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .close-cart:hover {
            background: #F1F5F9;
            color: var(--dark);
        }

        .cart-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .cart-empty {
            text-align: center;
            padding: 40px 20px;
            color: #94A3B8;
        }

        .cart-empty i {
            font-size: 56px;
            margin-bottom: 12px;
            color: #CBD5E1;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #F8FAFC;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }

        .cart-item img {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-sm);
            object-fit: cover;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-title {
            font-weight: 700;
            font-size: 14px;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .cart-item-price {
            color: var(--primary);
            font-weight: 700;
            font-size: 13px;
        }

        .cart-qty-ctrl {
            display: flex;
            align-items: center;
            gap: 8px;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 2px 6px;
        }

        .cart-qty-btn {
            background: none;
            border: none;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--dark);
            cursor: pointer;
            border-radius: 50%;
        }

        .cart-qty-btn:hover {
            background: #F1F5F9;
        }

        .cart-qty-num {
            font-weight: 700;
            font-size: 13px;
            min-width: 18px;
            text-align: center;
        }

        .cart-footer {
            padding: 20px;
            border-top: 1px solid var(--border-color);
            background: #FFFFFF;
        }

        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
            color: #64748B;
        }

        .cart-summary-row.total {
            font-size: 18px;
            font-weight: 800;
            color: var(--dark);
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed var(--border-color);
        }

        .cart-summary-row.total span:last-child {
            color: var(--primary);
        }

        .checkout-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(255, 87, 34, 0.3);
            margin-top: 14px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 87, 34, 0.4);
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #0F172A;
            color: white;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 14px;
            z-index: 1001;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast i {
            color: var(--success);
            font-size: 18px;
        }

        /* Footer */
        .footer {
            background: #0F172A;
            color: #94A3B8;
            padding: 60px 0 30px;
            margin-top: 60px;
            border-top: 1px solid #1E293B;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-col h4 {
            color: white;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 10px;
        }

        .footer-col ul li a {
            color: #94A3B8;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-col ul li a:hover {
            color: var(--primary);
            padding-left: 4px;
        }

        .footer-bottom {
            border-top: 1px solid #1E293B;
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        @media (max-width: 992px) {
            .nav-search {
                display: none;
            }
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }
            .nav-wrapper {
                height: 64px;
            }
            .hotline-btn span {
                display: none;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header / Navbar -->
    <header class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="{{ route('home') }}" class="logo">
                    <div class="logo-icon">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    Food<span>Order</span>
                </a>

                <div class="nav-search">
                    <form action="{{ route('home') }}" method="GET">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm món ngon (Phở bò, Cơm tấm, Trà sữa...)">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                    </form>
                </div>

                <div class="nav-actions">
                    <button class="cart-trigger" id="cartTriggerBtn">
                        <i class="fa-solid fa-bag-shopping" style="color: var(--primary); font-size: 16px;"></i>
                        <span>Giỏ hàng</span>
                        <div class="cart-badge" id="cartBadge">0</div>
                    </button>

                    @guest
                        <a href="{{ route('login') }}" style="text-decoration: none; color: var(--dark); font-weight: 700; font-size: 14px; padding: 9px 16px; border-radius: 50px; border: 1px solid var(--border-color); transition: var(--transition);">
                            <i class="fa-solid fa-user" style="color: #64748B; margin-right: 4px;"></i> Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" style="text-decoration: none; background: var(--primary-light); color: var(--primary); font-weight: 700; font-size: 14px; padding: 9px 16px; border-radius: 50px; transition: var(--transition);">
                            Đăng ký
                        </a>
                    @else
                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; padding: 7px 12px; border-radius: 50px; font-size: 13px; font-weight: 800; display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-chart-pie"></i> Quản trị
                                </a>
                            @endif

                            <a href="{{ route('orders.index') }}" style="text-decoration: none; background: #F1F5F9; color: var(--dark); border: 1px solid var(--border-color); padding: 7px 12px; border-radius: 50px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Đơn mua
                            </a>

                            <a href="{{ route('profile') }}" style="text-decoration: none; display: flex; align-items: center; gap: 6px; background: white; border: 1px solid var(--border-color); padding: 7px 14px; border-radius: 50px; font-size: 14px; font-weight: 700; color: var(--dark);">
                                <i class="fa-solid fa-circle-user" style="color: var(--primary); font-size: 18px;"></i>
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline;">
                                @csrf
                                <button type="submit" title="Đăng xuất" style="background: #F1F5F9; border: 1px solid var(--border-color); color: #64748B; cursor: pointer; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; transition: var(--transition);">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </button>
                            </form>
                        </div>
                    @endguest

                    <a href="tel:19008888" class="hotline-btn">
                        <i class="fa-solid fa-phone"></i>
                        <span>1900 8888</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Cart Overlay & Drawer -->
    <div class="cart-overlay" id="cartOverlay"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-header">
            <h3><i class="fa-solid fa-basket-shopping" style="color: var(--primary);"></i> Giỏ Hàng Của Bạn</h3>
            <button class="close-cart" id="closeCartBtn">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="cart-body" id="cartBody">
            <!-- Rendered by JS -->
        </div>
        <div class="cart-footer" id="cartFooter" style="display: none;">
            <div class="cart-summary-row">
                <span>Tạm tính:</span>
                <strong id="cartSubtotal">0 ₫</strong>
            </div>
            <div class="cart-summary-row">
                <span>Phí giao hàng:</span>
                <span style="color: var(--success); font-weight: 600;">Miễn phí (Freeship)</span>
            </div>
            <div class="cart-summary-row total">
                <span>Tổng cộng:</span>
                <span id="cartTotal">0 ₫</span>
            </div>

            @auth
                <div id="checkoutFormSection" style="margin-top: 14px; padding-top: 14px; border-top: 1px dashed var(--border-color);">
                    <div style="font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 8px;">
                        <i class="fa-solid fa-location-dot" style="color: var(--primary);"></i> Thông tin giao hàng (COD):
                    </div>
                    <input type="text" id="checkoutName" value="{{ Auth::user()->name }}" placeholder="Họ và tên người nhận *" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 13px; margin-bottom: 8px;">
                    <input type="text" id="checkoutPhone" value="{{ Auth::user()->phone }}" placeholder="Số điện thoại nhận hàng *" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 13px; margin-bottom: 8px;">
                    <input type="text" id="checkoutAddress" value="{{ Auth::user()->address }}" placeholder="Địa chỉ giao tận nơi *" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 13px; margin-bottom: 8px;">
                    <textarea id="checkoutNote" placeholder="Ghi chú thêm cho nhà hàng (tùy chọn)..." rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 13px; margin-bottom: 12px;"></textarea>

                    <button class="checkout-btn" id="submitOrderBtn" onclick="submitRealOrder()">
                        <i class="fa-solid fa-circle-check"></i> Xác Nhận Đặt Hàng
                    </button>
                </div>
            @else
                <div style="margin-top: 14px; text-align: center;">
                    <p style="font-size: 13px; color: #64748B; margin-bottom: 10px;">Vui lòng đăng nhập để hoàn tất đặt món</p>
                    <a href="{{ route('login') }}" class="checkout-btn" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập Để Đặt Hàng
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <i class="fa-solid fa-circle-check"></i>
        <span id="toastMsg">Đã thêm món vào giỏ hàng!</span>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <a href="{{ route('home') }}" class="logo" style="color: white; margin-bottom: 16px;">
                        <div class="logo-icon">
                            <i class="fa-solid fa-utensils"></i>
                        </div>
                        Food<span>Order</span>
                    </a>
                    <p style="font-size: 14px; line-height: 1.7; margin-top: 14px;">
                        Hệ thống đặt món ăn trực tuyến nhanh chóng, tiện lợi với hàng trăm món ngon hấp dẫn từ các đầu bếp chuyên nghiệp. Giao tận nơi trong vòng 15-30 phút.
                    </p>
                </div>
                <div class="footer-col">
                    <h4>Danh Mục</h4>
                    <ul>
                        <li><a href="{{ route('home') }}">Tất cả món ngon</a></li>
                        <li><a href="{{ route('home', ['category' => 1]) }}">Món Chính Đặc Sắc</a></li>
                        <li><a href="{{ route('home', ['category' => 2]) }}">Khai Vị & Ăn Vặt</a></li>
                        <li><a href="{{ route('home', ['category' => 3]) }}">Đồ Uống & Tráng Miệng</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Chính Sách</h4>
                    <ul>
                        <li><a href="#">Chính sách giao hàng</a></li>
                        <li><a href="#">Quy định thanh toán</a></li>
                        <li><a href="#">Bảo mật thông tin</a></li>
                        <li><a href="#">Điều khoản sử dụng</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Liên Hệ Đặt Bàn / Góp Ý</h4>
                    <p style="font-size: 14px; margin-bottom: 8px;"><i class="fa-solid fa-location-dot" style="color: var(--primary); margin-right: 8px;"></i> 123 Đường Ẩm Thực, Quận 1, TP. Hồ Chí Minh</p>
                    <p style="font-size: 14px; margin-bottom: 8px;"><i class="fa-solid fa-envelope" style="color: var(--primary); margin-right: 8px;"></i> contact@foodorder.vn</p>
                    <p style="font-size: 14px;"><i class="fa-solid fa-clock" style="color: var(--primary); margin-right: 8px;"></i> Mở cửa: 07:00 - 22:30 hàng ngày</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} FoodOrder Laravel. All rights reserved.</p>
                <p>Designed with ❤️ for Delicious Food Lovers</p>
            </div>
        </div>
    </footer>

    <!-- Cart JavaScript -->
    <script>
        let cart = JSON.parse(localStorage.getItem('food_order_cart') || '[]');

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }

        function saveCart() {
            localStorage.setItem('food_order_cart', JSON.stringify(cart));
            updateCartUI();
        }

        function addToCart(id, name, price, image) {
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({ id, name, price: Number(price), image, quantity: 1 });
            }
            saveCart();
            showToast(`Đã thêm "${name}" vào giỏ hàng!`);
        }

        function changeQty(id, delta) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) {
                    cart = cart.filter(i => i.id !== id);
                }
                saveCart();
            }
        }

        function removeItem(id) {
            cart = cart.filter(i => i.id !== id);
            saveCart();
        }

        function updateCartUI() {
            const badge = document.getElementById('cartBadge');
            const body = document.getElementById('cartBody');
            const footer = document.getElementById('cartFooter');
            const subtotalEl = document.getElementById('cartSubtotal');
            const totalEl = document.getElementById('cartTotal');

            const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
            badge.innerText = totalCount;

            if (cart.length === 0) {
                body.innerHTML = `
                    <div class="cart-empty">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <p style="font-weight: 600; color: #475569; margin-bottom: 6px;">Giỏ hàng đang trống</p>
                        <p style="font-size: 13px;">Hãy chọn các món ăn tươi ngon từ thực đơn nhé!</p>
                    </div>
                `;
                footer.style.display = 'none';
                return;
            }

            let totalAmount = 0;
            let html = '';
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                totalAmount += itemTotal;
                html += `
                    <div class="cart-item">
                        <img src="${item.image}" alt="${item.name}" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=150&q=80'">
                        <div class="cart-item-info">
                            <div class="cart-item-title">${item.name}</div>
                            <div class="cart-item-price">${formatCurrency(item.price)}</div>
                        </div>
                        <div class="cart-qty-ctrl">
                            <button class="cart-qty-btn" onclick="changeQty(${item.id}, -1)">-</button>
                            <span class="cart-qty-num">${item.quantity}</span>
                            <button class="cart-qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
                        </div>
                        <button onclick="removeItem(${item.id})" style="background:none;border:none;color:#94A3B8;cursor:pointer;padding:4px;" title="Xóa món">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                `;
            });

            body.innerHTML = html;
            footer.style.display = 'block';
            subtotalEl.innerText = formatCurrency(totalAmount);
            totalEl.innerText = formatCurrency(totalAmount);
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMsg');
            toastMsg.innerText = msg;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        async function submitRealOrder() {
            if (cart.length === 0) {
                alert('Giỏ hàng của bạn đang trống!');
                return;
            }

            const nameEl = document.getElementById('checkoutName');
            const phoneEl = document.getElementById('checkoutPhone');
            const addressEl = document.getElementById('checkoutAddress');
            const noteEl = document.getElementById('checkoutNote');
            const submitBtn = document.getElementById('submitOrderBtn');

            if (!nameEl || !phoneEl || !addressEl) return;

            const name = nameEl.value.trim();
            const phone = phoneEl.value.trim();
            const address = addressEl.value.trim();
            const note = noteEl ? noteEl.value.trim() : '';

            if (!name || !phone || !address) {
                alert('Vui lòng điền đầy đủ Tên, Số điện thoại và Địa chỉ giao hàng!');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/api/v1/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        customer_name: name,
                        customer_phone: phone,
                        delivery_address: address,
                        note: note || null,
                        items: cart.map(i => ({ food_id: i.id, quantity: i.quantity }))
                    })
                });

                const data = await res.json();

                if (res.ok) {
                    cart = [];
                    saveCart();
                    toggleCart(false);
                    alert(`🎉 ĐẶT HÀNG THÀNH CÔNG!\n\nMã đơn hàng: ${data.data.order_code}\nTổng thanh toán: ${formatCurrency(data.data.total_price)}\nPhương thức: Thanh toán khi nhận hàng (COD)\n\nChúng tôi sẽ giao tận nơi trong 15-30 phút!`);
                    window.location.href = '{{ route("orders.index") }}';
                } else {
                    alert(data.message || 'Đặt hàng thất bại. Vui lòng thử lại!');
                }
            } catch (e) {
                alert('Lỗi kết nối tới máy chủ. Vui lòng kiểm tra lại mạng!');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Xác Nhận Đặt Hàng';
                }
            }
        }

        function toggleCart(show) {
            const overlay = document.getElementById('cartOverlay');
            const drawer = document.getElementById('cartDrawer');
            if (show) {
                overlay.classList.add('active');
                drawer.classList.add('active');
            } else {
                overlay.classList.remove('active');
                drawer.classList.remove('active');
            }
        }

        document.getElementById('cartTriggerBtn').addEventListener('click', () => toggleCart(true));
        document.getElementById('closeCartBtn').addEventListener('click', () => toggleCart(false));
        document.getElementById('cartOverlay').addEventListener('click', () => toggleCart(false));

        // Initial UI load
        updateCartUI();
    </script>
    @yield('scripts')
</body>
</html>
