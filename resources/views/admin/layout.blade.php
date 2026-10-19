<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản Trị Hệ Thống - FoodOrder')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #FF5722;
            --primary-hover: #E64A19;
            --primary-light: #FBE9E7;
            --dark: #0F172A;
            --dark-muted: #334155;
            --light-bg: #F8FAFC;
            --border-color: #E2E8F0;
            --radius-md: 10px;
            --radius-lg: 16px;
            --transition: all 0.2s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 260px;
            background: #0F172A;
            color: white;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            min-height: 100vh;
            position: sticky;
            top: 0;
        }

        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid #1E293B;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: white;
            font-weight: 800;
            font-size: 18px;
        }

        .sidebar-brand span {
            color: var(--primary);
        }

        .sidebar-menu {
            padding: 20px 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            color: #94A3B8;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: var(--transition);
        }

        .menu-item:hover, .menu-item.active {
            color: white;
            background: #1E293B;
        }

        .menu-item.active {
            background: var(--primary);
            color: white;
        }

        .menu-item i {
            font-size: 16px;
            width: 20px;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #1E293B;
        }

        /* Main Content */
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .admin-topbar {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-content {
            padding: 30px;
            flex: 1;
        }

        .card {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            padding: 24px;
            margin-bottom: 24px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table th {
            text-align: left;
            padding: 12px 16px;
            background: #F8FAFC;
            color: #64748B;
            font-weight: 700;
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            color: var(--dark);
        }

        .table tr:hover td {
            background: #FAFAFA;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
        }

        /* Pagination Styling */
        nav[role="navigation"] {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 13px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--border-color);
        }

        nav[role="navigation"] svg {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            display: inline-block !important;
            vertical-align: middle;
        }

        nav[role="navigation"] .hidden {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 12px;
            flex-wrap: wrap;
        }

        nav[role="navigation"] > div:first-child:not(.hidden) {
            display: none !important;
        }

        nav[role="navigation"] span.relative,
        nav[role="navigation"] a.relative {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            text-decoration: none;
            margin: 0 2px;
            transition: var(--transition);
        }

        nav[role="navigation"] a.relative:hover {
            background: #F1F5F9;
            color: var(--primary);
            border-color: #CBD5E1;
        }

        nav[role="navigation"] span[aria-current="page"] span.relative,
        nav[role="navigation"] span[aria-current="page"] > span {
            background: var(--primary) !important;
            color: white !important;
            border-color: var(--primary) !important;
        }

        nav[role="navigation"] p {
            font-size: 13px;
            color: #64748B;
        }

        nav[role="navigation"] p span.font-medium,
        nav[role="navigation"] p span.font-semibold {
            font-weight: 700;
            color: var(--dark);
        }

        /* Mobile Responsive Utilities */
        .admin-menu-toggle {
            display: none;
            background: #F8FAFC;
            border: 1px solid var(--border-color);
            color: var(--dark);
            font-size: 16px;
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .admin-menu-toggle:hover {
            background: #F1F5F9;
            color: var(--primary);
        }

        .sidebar-close-btn {
            display: none;
            background: transparent;
            border: none;
            color: #94A3B8;
            font-size: 18px;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .sidebar-close-btn:hover {
            color: white;
            background: #1E293B;
        }

        .admin-sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .admin-sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 12px;
        }

        .table {
            width: 100%;
            min-width: 520px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .card {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            padding: 24px;
            margin-bottom: 24px;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 992px) {
            .admin-menu-toggle {
                display: inline-flex;
            }

            .sidebar-close-btn {
                display: inline-flex;
            }

            .admin-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                height: 100vh;
                z-index: 1050;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 6px 0 24px rgba(0,0,0,0.3);
                overflow-y: auto;
            }

            .admin-sidebar.open {
                transform: translateX(0);
            }

            .admin-topbar {
                padding: 14px 20px;
            }

            .admin-content {
                padding: 20px;
            }
        }

        @media (max-width: 640px) {
            .admin-topbar {
                padding: 12px 14px;
            }

            .topbar-title {
                font-size: 14px !important;
                max-width: 170px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .topbar-user span {
                display: none;
            }

            .admin-content {
                padding: 14px 10px;
            }

            .card {
                padding: 16px 12px;
                margin-bottom: 16px;
                border-radius: var(--radius-md);
            }

            .table th, .table td {
                padding: 10px 12px;
                font-size: 13px;
            }

            /* Responsive Modals */
            #foodModal > div,
            #categoryModal > div,
            #voucherModal > div {
                width: calc(100% - 24px) !important;
                max-height: 90vh !important;
                overflow-y: auto !important;
                padding: 18px 14px !important;
                margin: 12px !important;
            }

            /* Responsive Admin Form Grids */
            #voucherForm > div[style*="grid-template-columns"],
            #storeSettingForm > div[style*="grid-template-columns"],
            #deliveryZoneForm {
                grid-template-columns: 1fr !important;
            }

            #deliveryZoneForm button[type="submit"] {
                width: 100%;
            }

            .card form input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),
            .card form select {
                max-width: 100%;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>

    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand" style="display: flex; align-items: center; justify-content: space-between;">
            <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: white; font-weight: 800; font-size: 18px;">
                <i class="fa-solid fa-utensils" style="color: var(--primary);"></i>
                <div>Food<span>Order</span> <span style="font-size: 11px; background: #334155; color: #94A3B8; padding: 2px 6px; border-radius: 4px; font-weight: 700; margin-left: 4px;">ADMIN</span></div>
            </a>
            <button type="button" id="adminSidebarCloseBtn" class="sidebar-close-btn" aria-label="Đóng menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="sidebar-menu">
            <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Tổng quan
            </a>
            <a href="{{ route('admin.orders.index') }}" class="menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i> Đơn hàng
            </a>
            <a href="{{ route('admin.foods.index') }}" class="menu-item {{ request()->routeIs('admin.foods.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bowl-food"></i> Món ăn
            </a>
            <a href="{{ route('admin.categories.index') }}" class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Danh mục
            </a>
            <a href="{{ route('admin.users.index') }}" class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Khách hàng
            </a>
            <a href="{{ route('admin.shippers.index') }}" class="menu-item {{ request()->routeIs('admin.shippers.*') ? 'active' : '' }}">
                <i class="fa-solid fa-motorcycle"></i> Đội ngũ Shipper
            </a>
            <a href="{{ route('admin.payrolls.index') }}" class="menu-item {{ request()->routeIs('admin.payrolls.*') ? 'active' : '' }}">
                <i class="fa-solid fa-money-check-dollar"></i> Bảng lương Shipper
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="menu-item {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice-dollar"></i> Chi phí vận hành
            </a>
            <a href="{{ route('admin.vouchers.index') }}" class="menu-item {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                <i class="fa-solid fa-ticket"></i> Voucher
            </a>
            <a href="{{ route('admin.settings.edit') }}" class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-store"></i> Cấu hình cửa hàng
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="{{ route('shipper.orders.index') }}" class="menu-item" target="_blank">
                <i class="fa-solid fa-motorcycle"></i> Cổng giao hàng (Shipper)
            </a>
            <a href="{{ route('home') }}" class="menu-item" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem website
            </a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                <button type="button" id="adminSidebarToggle" class="admin-menu-toggle" aria-label="Mở menu điều hướng">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="topbar-title" style="font-weight: 700; font-size: 16px; color: var(--dark-muted);">
                    @yield('header_title', 'Bảng Điều Khiển Quản Trị')
                </div>
            </div>

            <div class="topbar-user">
                <span style="font-weight: 700; font-size: 14px;">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" data-prevent-double-submit style="margin: 0;">
                    @csrf
                    <button type="submit" title="Đăng xuất" style="background: #F1F5F9; border: 1px solid var(--border-color); color: #64748B; cursor: pointer; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>

        <main class="admin-content">
            @if(session('status') || session('success'))
                <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 14px; font-weight: 600;">
                    <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> {{ session('status') ?? session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 14px; font-weight: 600;">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 14px; font-weight: 600;">
                    <div style="font-weight: 700; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> Vui lòng kiểm tra lại:</div>
                    <ul style="margin-left: 20px; font-size: 13px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @include('partials.submit-guard')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('adminSidebarOverlay');
            const toggleBtn = document.getElementById('adminSidebarToggle');
            const closeBtn = document.getElementById('adminSidebarCloseBtn');

            function openSidebar() {
                sidebar?.classList.add('open');
                overlay?.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar?.classList.remove('open');
                overlay?.classList.remove('active');
                document.body.style.overflow = '';
            }

            toggleBtn?.addEventListener('click', openSidebar);
            closeBtn?.addEventListener('click', closeSidebar);
            overlay?.addEventListener('click', closeSidebar);
        });
    </script>
    @yield('scripts')
</body>
</html>
