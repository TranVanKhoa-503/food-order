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
    </style>
    @yield('styles')
</head>
<body>
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="fa-solid fa-utensils" style="color: var(--primary);"></i>
            <div>Food<span>Order</span> <span style="font-size: 11px; background: #334155; color: #94A3B8; padding: 2px 6px; border-radius: 4px; font-weight: 700; margin-left: 4px;">ADMIN</span></div>
        </a>

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
        </div>

        <div class="sidebar-footer">
            <a href="{{ route('home') }}" class="menu-item" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem website
            </a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div style="font-weight: 700; font-size: 16px; color: var(--dark-muted);">
                @yield('header_title', 'Bảng Điều Khiển Quản Trị')
            </div>

            <div class="topbar-user">
                <span style="font-weight: 700; font-size: 14px;">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" title="Đăng xuất" style="background: #F1F5F9; border: 1px solid var(--border-color); color: #64748B; cursor: pointer; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>

        <main class="admin-content">
            @if(session('status'))
                <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 14px; font-weight: 600;">
                    <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
