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

        .notification-wrapper {
            position: relative;
        }

        .notification-trigger {
            position: relative;
            width: 38px;
            height: 38px;
            border: 1px solid var(--border-color);
            border-radius: 50%;
            background: white;
            color: #64748B;
            cursor: pointer;
            transition: var(--transition);
        }

        .notification-trigger:hover,
        .notification-trigger.active {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border: 2px solid white;
            border-radius: 50px;
            background: #EF4444;
            color: white;
            font-size: 10px;
            font-weight: 800;
            line-height: 14px;
        }

        .notification-panel {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            z-index: 200;
            width: min(360px, calc(100vw - 32px));
            overflow: hidden;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            background: white;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: var(--transition);
        }

        .notification-panel.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .notification-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .notification-panel-header strong {
            font-size: 14px;
        }

        .notification-panel-header button {
            border: 0;
            background: transparent;
            color: var(--primary);
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
        }

        .notification-list {
            max-height: 360px;
            overflow-y: auto;
        }

        .notification-empty {
            padding: 28px 18px;
            color: #94A3B8;
            font-size: 13px;
            text-align: center;
        }

        .notification-item {
            display: block;
            padding: 12px 16px;
            border-bottom: 1px solid #F1F5F9;
            color: var(--dark);
            text-decoration: none;
        }

        .notification-item:hover,
        .notification-item.unread {
            background: #FFF7ED;
        }

        .notification-item p {
            margin-bottom: 4px;
            font-size: 12px;
            line-height: 1.45;
        }

        .notification-item small {
            color: #94A3B8;
            font-size: 10px;
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

        /* Food recommendation assistant */
        .food-assistant-trigger {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 150;
            width: 58px;
            height: 58px;
            border: 0;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            font-size: 22px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(255, 87, 34, 0.35);
            transition: var(--transition);
        }

        .food-assistant-trigger:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(255, 87, 34, 0.45);
        }

        .food-assistant-panel {
            position: fixed;
            right: 24px;
            bottom: 94px;
            z-index: 150;
            width: min(380px, calc(100vw - 32px));
            max-height: min(720px, calc(100vh - 120px));
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.2);
            opacity: 0;
            visibility: hidden;
            transform: translateY(14px) scale(0.98);
            transition: var(--transition);
        }

        .food-assistant-panel.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .food-assistant-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px;
            color: white;
            background: linear-gradient(135deg, #1E293B, #0F172A);
        }

        .food-assistant-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 800;
        }

        .food-assistant-title i {
            color: #FDBA74;
        }

        .food-assistant-close {
            border: 0;
            background: transparent;
            color: #CBD5E1;
            font-size: 18px;
            cursor: pointer;
        }

        .food-assistant-body {
            overflow-y: auto;
            padding: 16px;
        }

        .food-assistant-intro {
            margin-bottom: 14px;
            color: #64748B;
            font-size: 13px;
            line-height: 1.5;
        }

        .food-assistant-form {
            display: grid;
            gap: 10px;
        }

        .food-assistant-form label {
            color: var(--dark-muted);
            font-size: 12px;
            font-weight: 800;
        }

        .food-assistant-form select,
        .food-assistant-form button {
            width: 100%;
            min-height: 40px;
            padding: 9px 11px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 13px;
        }

        .food-assistant-form select:focus {
            border-color: var(--primary);
            outline: 3px solid rgba(255, 87, 34, 0.12);
        }

        .food-assistant-form button {
            margin-top: 3px;
            border-color: var(--primary);
            background: var(--primary);
            color: white;
            font-weight: 800;
            cursor: pointer;
        }

        .food-assistant-form button:hover {
            background: var(--primary-hover);
        }

        .food-assistant-form button:disabled {
            cursor: wait;
            opacity: 0.65;
        }

        .food-assistant-status {
            min-height: 20px;
            margin: 13px 0 8px;
            color: #64748B;
            font-size: 12px;
        }

        .food-assistant-results {
            display: grid;
            gap: 10px;
        }

        .food-assistant-card {
            display: grid;
            grid-template-columns: 64px 1fr;
            gap: 11px;
            padding: 9px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background: #F8FAFC;
        }

        .food-assistant-card img {
            width: 64px;
            height: 64px;
            border-radius: var(--radius-sm);
            object-fit: cover;
        }

        .food-assistant-card h4 {
            margin-bottom: 3px;
            color: var(--dark);
            font-size: 13px;
            line-height: 1.35;
        }

        .food-assistant-card p {
            margin-bottom: 6px;
            color: var(--primary);
            font-size: 13px;
            font-weight: 800;
        }

        .food-assistant-card a {
            color: #64748B;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
        }

        .food-assistant-card a:hover {
            color: var(--primary);
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
            .food-assistant-trigger {
                right: 16px;
                bottom: 16px;
            }
            .food-assistant-panel {
                right: 16px;
                bottom: 86px;
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
                                <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; padding: 7px 12px; border-radius: 50px; font-size: 13px; font-weight: 800; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                                    <i class="fa-solid fa-chart-pie"></i> Quản trị
                                </a>
                            @endif

                            @if(Auth::user()->isShipper())
                                <a href="{{ route('shipper.orders.index') }}" style="text-decoration: none; background: #E0F2FE; color: #0284C7; border: 1px solid #BAE6FD; padding: 7px 12px; border-radius: 50px; font-size: 13px; font-weight: 800; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                                    <i class="fa-solid fa-motorcycle"></i> Giao hàng
                                </a>
                            @endif

                            <a href="{{ route('orders.index') }}" style="text-decoration: none; background: #F1F5F9; color: var(--dark); border: 1px solid var(--border-color); padding: 7px 12px; border-radius: 50px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                                <i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Đơn mua
                            </a>

                            <div class="notification-wrapper">
                                <button class="notification-trigger" id="notificationTrigger" type="button" aria-label="Thông báo" aria-expanded="false">
                                    <i class="fa-solid fa-bell"></i>
                                    <span class="notification-badge" id="notificationBadge" style="display:none;">0</span>
                                </button>
                                <div class="notification-panel" id="notificationPanel" aria-label="Thông báo đơn hàng">
                                    <div class="notification-panel-header">
                                        <strong>Thông báo đơn hàng</strong>
                                        <button type="button" id="markAllNotificationsRead">Đánh dấu đã đọc</button>
                                    </div>
                                    <div class="notification-list" id="notificationList">
                                        <div class="notification-empty">Đang tải thông báo...</div>
                                    </div>
                                </div>
                            </div>

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
                <span id="cartShippingFee" style="color: var(--success); font-weight: 600;">{{ ($storeSetting?->shipping_fee ?? 0) > 0 ? number_format($storeSetting->shipping_fee, 0, ',', '.').' ₫' : 'Miễn phí' }}</span>
            </div>
            <div class="cart-summary-row">
                <span>Giảm giá:</span>
                <span id="cartDiscount" style="color: var(--success); font-weight: 600;">0 ₫</span>
            </div>
            <div class="cart-summary-row total">
                <span>Tổng cộng:</span>
                <span id="cartTotal">0 ₫</span>
            </div>

            @auth
                <div id="checkoutFormSection" style="margin-top: 14px; padding-top: 14px; border-top: 1px dashed var(--border-color);">
                    @if($storeSetting && ! $storeSetting->is_open)
                        <div style="background:#FEF2F2;color:#B91C1C;border:1px solid #FECACA;padding:10px;border-radius:var(--radius-md);font-size:12px;margin-bottom:10px;">Cửa hàng hiện đang tạm ngừng nhận đơn.</div>
                    @endif
                    <div style="font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 8px;">
                        <i class="fa-solid fa-location-dot" style="color: var(--primary);"></i> Thông tin giao hàng (COD):
                    </div>
                    @if(($storeSetting?->min_order_value ?? 0) > 0)
                        <div style="font-size: 12px; color: #64748B; margin-bottom: 8px;">Đơn tối thiểu: {{ number_format($storeSetting->min_order_value, 0, ',', '.') }} ₫</div>
                    @endif
                    <input type="text" id="checkoutName" value="{{ Auth::user()->name }}" placeholder="Họ và tên người nhận *" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 13px; margin-bottom: 8px;">
                    <input type="text" id="checkoutPhone" value="{{ Auth::user()->phone }}" placeholder="Số điện thoại nhận hàng *" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 13px; margin-bottom: 8px;">
                    <input type="text" id="checkoutAddress" value="{{ Auth::user()->address }}" placeholder="Địa chỉ giao tận nơi *" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 13px; margin-bottom: 8px;">
                    <div id="deliveryZoneField" style="display:none;margin-bottom:8px;">
                        <select id="checkoutDeliveryZone" style="width:100%;padding:8px 12px;border:1px solid var(--border-color);border-radius:var(--radius-md);font-size:13px;">
                            <option value="">Chọn khu vực giao hàng *</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                        <input type="text" id="checkoutVoucher" placeholder="Mã giảm giá (nếu có)" style="flex: 1; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 13px; text-transform: uppercase;">
                        <button type="button" id="applyVoucherBtn" onclick="applyVoucher()" style="padding: 8px 14px; background: var(--primary); color: white; border: none; border-radius: var(--radius-md); font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;">
                            Áp dụng
                        </button>
                    </div>
                    <div id="voucherFeedback" style="font-size: 12px; margin-bottom: 12px; display: none; font-weight: 600;"></div>

                    <button class="checkout-btn" id="submitOrderBtn" onclick="submitRealOrder()" {{ $storeSetting && ! $storeSetting->is_open ? 'disabled' : '' }}>
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

    <!-- Food recommendation assistant -->
    <button class="food-assistant-trigger" id="foodAssistantTrigger" type="button" aria-label="Gợi ý món ăn" aria-expanded="false">
        <i class="fa-solid fa-wand-magic-sparkles"></i>
    </button>
    <section class="food-assistant-panel" id="foodAssistantPanel" aria-label="Trợ lý gợi ý món ăn">
        <div class="food-assistant-header">
            <div class="food-assistant-title">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>Trợ lý chọn món</span>
            </div>
            <button class="food-assistant-close" id="foodAssistantClose" type="button" aria-label="Đóng trợ lý">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="food-assistant-body">
            <p class="food-assistant-intro">Chọn vài tiêu chí, mình sẽ đề xuất những món đang còn phục vụ. Bạn tự quyết định món nào muốn đặt nhé.</p>
            <form class="food-assistant-form" id="foodAssistantForm">
                <div>
                    <label for="recommendationCategory">Bạn muốn ăn nhóm món nào?</label>
                    <select id="recommendationCategory" name="category">
                        <option value="">Tất cả món</option>
                    </select>
                </div>
                <div>
                    <label for="recommendationBudget">Ngân sách tối đa</label>
                    <select id="recommendationBudget" name="max_price">
                        <option value="">Không giới hạn</option>
                        <option value="30000">Dưới 30.000 ₫</option>
                        <option value="50000">Dưới 50.000 ₫</option>
                        <option value="70000">Dưới 70.000 ₫</option>
                        <option value="100000">Dưới 100.000 ₫</option>
                    </select>
                </div>
                <div>
                    <label for="recommendationPreference">Yêu cầu thêm</label>
                    <select id="recommendationPreference" name="preference">
                        <option value="any">Không có</option>
                        <option value="vegetarian">Món chay</option>
                        <option value="not_spicy">Không cay</option>
                    </select>
                </div>
                <button type="submit" id="foodAssistantSubmit">
                    <i class="fa-solid fa-utensils"></i> Xem món được gợi ý
                </button>
            </form>
            <div class="food-assistant-status" id="foodAssistantStatus" role="status" aria-live="polite"></div>
            <div class="food-assistant-results" id="foodAssistantResults"></div>
        </div>
    </section>

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
        let cart = [];
        try {
            const storedCart = JSON.parse(localStorage.getItem('food_order_cart') || '[]');
            cart = Array.isArray(storedCart) ? storedCart : [];
        } catch (error) {
            localStorage.removeItem('food_order_cart');
        }

        const storeShippingFee = Number(@json((int) ($storeSetting?->shipping_fee ?? 0)));
        let deliveryZones = [];
        let appliedVoucher = null;

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }

        function calculateDiscount(subtotal) {
            if (!appliedVoucher || subtotal <= 0) return 0;
            let discount = 0;
            if (appliedVoucher.discount_type === 'percent') {
                discount = Math.floor(subtotal * (Number(appliedVoucher.discount_value) / 100));
            } else {
                discount = Number(appliedVoucher.discount_value);
            }
            if (appliedVoucher.max_discount_amount) {
                discount = Math.min(discount, Number(appliedVoucher.max_discount_amount));
            }
            return Math.min(Math.max(0, discount), subtotal);
        }

        function selectedShippingFee() {
            const zoneId = document.getElementById('checkoutDeliveryZone')?.value;
            const zone = deliveryZones.find(item => String(item.id) === String(zoneId));
            return zone ? Number(zone.fee) : storeShippingFee;
        }

        function updateShippingFeeUI() {
            const fee = selectedShippingFee();
            const feeEl = document.getElementById('cartShippingFee');
            if (feeEl) feeEl.textContent = fee > 0 ? formatCurrency(fee) : 'Miễn phí';
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const totalEl = document.getElementById('cartTotal');
            const discountAmount = calculateDiscount(subtotal);
            if (totalEl && cart.length > 0) totalEl.textContent = formatCurrency(Math.max(0, subtotal - discountAmount + fee));
        }

        async function loadDeliveryZones() {
            const zoneField = document.getElementById('deliveryZoneField');
            const zoneSelect = document.getElementById('checkoutDeliveryZone');
            if (!zoneField || !zoneSelect) return;

            try {
                const response = await fetch('/api/v1/delivery-zones', {headers: {Accept: 'application/json'}});
                if (!response.ok) return;
                const payload = await response.json();
                deliveryZones = payload.data || [];
                if (!deliveryZones.length) return;

                deliveryZones.forEach((zone) => {
                    const option = document.createElement('option');
                    option.value = zone.id;
                    option.textContent = `${zone.name} - ${zone.fee > 0 ? formatCurrency(zone.fee) : 'Miễn phí'}`;
                    zoneSelect.appendChild(option);
                });
                zoneField.style.display = 'block';
                zoneSelect.addEventListener('change', updateShippingFeeUI);
            } catch (error) {
                // The fixed store shipping fee remains available if zones cannot load.
            }
        }

        function saveCart() {
            localStorage.setItem('food_order_cart', JSON.stringify(cart));
            updateCartUI();
        }

        function addToCart(id, name, price, image) {
            const existing = cart.find(item => item.id === id);
            if (existing) {
                if (existing.quantity >= 99) {
                    showToast('Mỗi món chỉ được đặt tối đa 99 phần.');
                    return;
                }
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
                if (delta > 0 && item.quantity >= 99) return;
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
            const discountEl = document.getElementById('cartDiscount');

            const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
            badge.innerText = totalCount;

            if (cart.length === 0) {
                body.replaceChildren();
                const empty = document.createElement('div');
                empty.className = 'cart-empty';
                const icon = document.createElement('i');
                icon.className = 'fa-solid fa-bag-shopping';
                const title = document.createElement('p');
                title.style.cssText = 'font-weight: 600; color: #475569; margin-bottom: 6px;';
                title.textContent = 'Giỏ hàng đang trống';
                const hint = document.createElement('p');
                hint.style.fontSize = '13px';
                hint.textContent = 'Hãy chọn các món ăn tươi ngon từ thực đơn nhé!';
                empty.append(icon, title, hint);
                body.appendChild(empty);
                footer.style.display = 'none';
                return;
            }

            let totalAmount = 0;
            const fragment = document.createDocumentFragment();
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                totalAmount += itemTotal;
                const row = document.createElement('div');
                row.className = 'cart-item';
                const image = document.createElement('img');
                image.src = item.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=150&q=80';
                image.alt = item.name || 'Món ăn';
                image.onerror = () => { image.src = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=150&q=80'; };
                const info = document.createElement('div');
                info.className = 'cart-item-info';
                const itemTitle = document.createElement('div');
                itemTitle.className = 'cart-item-title';
                itemTitle.textContent = item.name || 'Món ăn';
                const itemPrice = document.createElement('div');
                itemPrice.className = 'cart-item-price';
                itemPrice.textContent = formatCurrency(item.price);
                info.append(itemTitle, itemPrice);
                const controls = document.createElement('div');
                controls.className = 'cart-qty-ctrl';
                const minus = document.createElement('button');
                minus.className = 'cart-qty-btn';
                minus.textContent = '-';
                minus.addEventListener('click', () => changeQty(item.id, -1));
                const quantity = document.createElement('span');
                quantity.className = 'cart-qty-num';
                quantity.textContent = item.quantity;
                const plus = document.createElement('button');
                plus.className = 'cart-qty-btn';
                plus.textContent = '+';
                plus.addEventListener('click', () => changeQty(item.id, 1));
                controls.append(minus, quantity, plus);
                const remove = document.createElement('button');
                remove.style.cssText = 'background:none;border:none;color:#94A3B8;cursor:pointer;padding:4px;';
                remove.title = 'Xóa món';
                const removeIcon = document.createElement('i');
                removeIcon.className = 'fa-solid fa-trash-can';
                remove.appendChild(removeIcon);
                remove.addEventListener('click', () => removeItem(item.id));
                row.append(image, info, controls, remove);
                fragment.appendChild(row);
            });

            body.replaceChildren(fragment);
            footer.style.display = 'block';
            subtotalEl.innerText = formatCurrency(totalAmount);
            const discountAmount = calculateDiscount(totalAmount);
            if (discountEl) {
                discountEl.innerText = discountAmount > 0 ? ('-' + formatCurrency(discountAmount)) : '0 ₫';
            }
            totalEl.innerText = formatCurrency(Math.max(0, totalAmount - discountAmount + selectedShippingFee()));
            updateShippingFeeUI();
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

        async function applyVoucher() {
            const voucherEl = document.getElementById('checkoutVoucher');
            const feedbackEl = document.getElementById('voucherFeedback');
            const applyBtn = document.getElementById('applyVoucherBtn');

            if (!voucherEl || !voucherEl.value.trim()) {
                if (feedbackEl) {
                    feedbackEl.style.display = 'block';
                    feedbackEl.style.color = '#DC2626';
                    feedbackEl.innerText = 'Vui lòng nhập mã voucher!';
                }
                return;
            }

            const code = voucherEl.value.trim().toUpperCase();
            voucherEl.value = code;

            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            if (subtotal <= 0) {
                alert('Giỏ hàng của bạn đang trống!');
                return;
            }

            if (applyBtn) {
                applyBtn.disabled = true;
                applyBtn.innerText = '...';
            }

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/api/v1/vouchers/check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        voucher_code: code,
                        subtotal: subtotal
                    })
                });

                const data = await res.json();
                if (res.ok && data.data) {
                    appliedVoucher = data.data;
                    updateCartUI();
                    if (feedbackEl) {
                        feedbackEl.style.display = 'block';
                        feedbackEl.style.color = '#059669';
                        feedbackEl.innerText = `✓ Áp dụng mã ${appliedVoucher.code}: Giảm ${formatCurrency(appliedVoucher.discount_amount)}`;
                    }
                } else {
                    appliedVoucher = null;
                    updateCartUI();
                    if (feedbackEl) {
                        feedbackEl.style.display = 'block';
                        feedbackEl.style.color = '#DC2626';
                        feedbackEl.innerText = '✕ ' + (data.message || 'Mã giảm giá không hợp lệ.');
                    }
                }
            } catch (err) {
                if (feedbackEl) {
                    feedbackEl.style.display = 'block';
                    feedbackEl.style.color = '#DC2626';
                    feedbackEl.innerText = '✕ Lỗi kết nối kiểm tra mã giảm giá.';
                }
            } finally {
                if (applyBtn) {
                    applyBtn.disabled = false;
                    applyBtn.innerText = 'Áp dụng';
                }
            }
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
            const voucherEl = document.getElementById('checkoutVoucher');
            const deliveryZoneEl = document.getElementById('checkoutDeliveryZone');
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
            if (deliveryZones.length > 0 && (!deliveryZoneEl || !deliveryZoneEl.value)) {
                alert('Vui lòng chọn khu vực giao hàng!');
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
                        delivery_zone_id: deliveryZoneEl?.value ? Number(deliveryZoneEl.value) : null,
                        note: note || null,
                        voucher_code: voucherEl ? (voucherEl.value.trim() || null) : null,
                        items: cart.map(i => ({ food_id: i.id, quantity: i.quantity }))
                    })
                });

                const data = await res.json();

                if (res.ok) {
                    cart = [];
                    appliedVoucher = null;
                    saveCart();
                    if (voucherEl) voucherEl.value = '';
                    const feedbackEl = document.getElementById('voucherFeedback');
                    if (feedbackEl) feedbackEl.style.display = 'none';
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
        loadDeliveryZones();
    </script>
    <script>
        const foodAssistantTrigger = document.getElementById('foodAssistantTrigger');
        const foodAssistantPanel = document.getElementById('foodAssistantPanel');
        const foodAssistantClose = document.getElementById('foodAssistantClose');
        const foodAssistantForm = document.getElementById('foodAssistantForm');
        const foodAssistantSubmit = document.getElementById('foodAssistantSubmit');
        const foodAssistantStatus = document.getElementById('foodAssistantStatus');
        const foodAssistantResults = document.getElementById('foodAssistantResults');
        const recommendationCategory = document.getElementById('recommendationCategory');
        let recommendationCategoriesLoaded = false;

        function toggleFoodAssistant(show) {
            foodAssistantPanel.classList.toggle('open', show);
            foodAssistantTrigger.setAttribute('aria-expanded', show ? 'true' : 'false');
            if (show && !recommendationCategoriesLoaded) loadRecommendationCategories();
        }

        async function loadRecommendationCategories() {
            try {
                const response = await fetch('/api/v1/categories?with_foods_count=1', {headers: {Accept: 'application/json'}});
                if (!response.ok) return;

                const payload = await response.json();
                (payload.data || []).forEach((category) => {
                    const option = document.createElement('option');
                    option.value = category.slug;
                    option.textContent = category.name;
                    recommendationCategory.appendChild(option);
                });
                recommendationCategoriesLoaded = true;
            } catch (error) {
                // The assistant still works with the "all foods" option if categories cannot load.
            }
        }

        function renderRecommendationResults(foods) {
            foodAssistantResults.replaceChildren();
            foods.forEach((food) => {
                const card = document.createElement('article');
                card.className = 'food-assistant-card';

                const image = document.createElement('img');
                image.src = food.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=150&q=80';
                image.alt = food.name || 'Món ăn';
                image.onerror = () => { image.src = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=150&q=80'; };

                const content = document.createElement('div');
                const title = document.createElement('h4');
                title.textContent = food.name || 'Món ăn';
                const price = document.createElement('p');
                price.textContent = formatCurrency(Number(food.price || 0));
                const link = document.createElement('a');
                link.href = `/foods/${encodeURIComponent(food.id)}`;
                link.textContent = 'Xem chi tiết món →';
                content.append(title, price, link);
                card.append(image, content);
                foodAssistantResults.appendChild(card);
            });
        }

        foodAssistantTrigger.addEventListener('click', () => toggleFoodAssistant(!foodAssistantPanel.classList.contains('open')));
        foodAssistantClose.addEventListener('click', () => toggleFoodAssistant(false));

        foodAssistantForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            foodAssistantSubmit.disabled = true;
            foodAssistantStatus.textContent = 'Đang tìm món phù hợp...';
            foodAssistantResults.replaceChildren();

            const formData = new FormData(foodAssistantForm);
            const payload = {
                category: formData.get('category') || null,
                max_price: formData.get('max_price') ? Number(formData.get('max_price')) : null,
                preference: formData.get('preference') || 'any',
                limit: 3,
            };

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/api/v1/recommendations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Không thể lấy gợi ý.');

                foodAssistantStatus.textContent = data.message || '';
                renderRecommendationResults(data.data || []);
            } catch (error) {
                foodAssistantStatus.textContent = error.message || 'Không thể lấy gợi ý lúc này.';
            } finally {
                foodAssistantSubmit.disabled = false;
            }
        });
    </script>
    @auth
    <script>
        const notificationTrigger = document.getElementById('notificationTrigger');
        const notificationPanel = document.getElementById('notificationPanel');
        const notificationBadge = document.getElementById('notificationBadge');
        const notificationList = document.getElementById('notificationList');
        const markAllNotificationsRead = document.getElementById('markAllNotificationsRead');

        function formatNotificationDate(value) {
            if (!value) return '';
            return new Date(value).toLocaleString('vi-VN', {dateStyle: 'short', timeStyle: 'short'});
        }

        function renderNotifications(notifications) {
            notificationList.replaceChildren();
            if (!notifications.length) {
                const empty = document.createElement('div');
                empty.className = 'notification-empty';
                empty.textContent = 'Chưa có cập nhật đơn hàng.';
                notificationList.appendChild(empty);
                return;
            }

            notifications.forEach((notification) => {
                const link = document.createElement('a');
                link.className = `notification-item${notification.read_at ? '' : ' unread'}`;
                link.href = notification.order_id ? `/orders/${encodeURIComponent(notification.order_id)}` : '{{ route('orders.index') }}';
                const message = document.createElement('p');
                message.textContent = notification.message;
                const date = document.createElement('small');
                date.textContent = formatNotificationDate(notification.created_at);
                link.append(message, date);
                notificationList.appendChild(link);
            });
        }

        async function loadNotifications() {
            try {
                const response = await fetch('/api/v1/notifications', {headers: {Accept: 'application/json'}});
                if (!response.ok) return;
                const data = await response.json();
                const unreadCount = Number(data.unread_count || 0);
                notificationBadge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                notificationBadge.style.display = unreadCount > 0 ? 'block' : 'none';
                renderNotifications(data.data || []);
            } catch (error) {
                notificationList.replaceChildren();
                const empty = document.createElement('div');
                empty.className = 'notification-empty';
                empty.textContent = 'Không thể tải thông báo lúc này.';
                notificationList.appendChild(empty);
            }
        }

        notificationTrigger?.addEventListener('click', () => {
            const isOpen = notificationPanel.classList.toggle('open');
            notificationTrigger.classList.toggle('active', isOpen);
            notificationTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (isOpen) loadNotifications();
        });

        markAllNotificationsRead?.addEventListener('click', async () => {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            await fetch('/api/v1/notifications/read-all', {
                method: 'PATCH',
                headers: {'X-CSRF-TOKEN': token, Accept: 'application/json'},
            });
            notificationBadge.style.display = 'none';
            notificationList.querySelectorAll('.notification-item.unread').forEach((item) => item.classList.remove('unread'));
        });

        loadNotifications();
    </script>
    @endauth
    @yield('scripts')
</body>
</html>
