@extends('admin.layout')

@section('title', 'Dashboard Quản Trị - FoodOrder')
@section('header_title', 'Tổng Quan Hoạt Động Cửa Hàng')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <form method="GET" action="{{ route('admin.dashboard') }}" style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;">
        <div>
            <label style="display:block;font-size:12px;color:#64748B;font-weight:700;margin-bottom:6px;">Từ ngày</label>
            <input type="date" name="from" value="{{ $from ?? '' }}" style="padding:9px 10px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
        </div>
        <div>
            <label style="display:block;font-size:12px;color:#64748B;font-weight:700;margin-bottom:6px;">Đến ngày</label>
            <input type="date" name="to" value="{{ $to ?? '' }}" style="padding:9px 10px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
        </div>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-chart-line"></i> Xem thống kê</button>
        @if(!empty($from) || !empty($to))
            <a class="btn" href="{{ route('admin.dashboard') }}" style="background:#F1F5F9;color:#64748B;">Xóa lọc</a>
        @endif
        <a class="btn" href="{{ route('admin.orders.export', array_filter(['from' => $from, 'to' => $to])) }}" style="background:#ECFDF5;color:#047857;margin-left:auto;">
            <i class="fa-solid fa-file-csv"></i> Xuất báo cáo đơn
        </a>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: var(--radius-md); background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-coins"></i>
        </div>
        <div>
            <div style="font-size: 13px; color: #64748B; font-weight: 600;">Tổng Doanh Thu</div>
            <div style="font-size: 22px; font-weight: 800; color: #059669;">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: var(--radius-md); background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div>
            <div style="font-size: 13px; color: #64748B; font-weight: 600;">Tổng Đơn Hàng</div>
            <div style="font-size: 22px; font-weight: 800; color: var(--dark);">{{ $totalOrders }}</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: var(--radius-md); background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div>
            <div style="font-size: 13px; color: #64748B; font-weight: 600;">Đơn Chờ Xử Lý</div>
            <div style="font-size: 22px; font-weight: 800; color: #D97706;">{{ $pendingOrders }}</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: var(--radius-md); background: #F3E8FF; color: #7C3AED; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div style="font-size: 13px; color: #64748B; font-weight: 600;">Khách Hàng</div>
            <div style="font-size: 22px; font-weight: 800; color: var(--dark);">{{ $totalUsers }}</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:24px;margin-bottom:24px;">
    <div class="card" style="margin-bottom:0;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:12px;">
            <h3 style="font-size:16px;font-weight:800;"><i class="fa-solid fa-fire" style="color:var(--primary);margin-right:6px;"></i> Món bán chạy</h3>
            <span style="font-size:12px;color:#64748B;">Đơn hoàn tất</span>
        </div>
        <table class="table">
            <thead><tr><th>MÓN ĂN</th><th>SL</th><th>DOANH THU</th></tr></thead>
            <tbody>
                @forelse($topFoods as $food)
                    <tr>
                        <td style="font-weight:700;">{{ $food->food_name }}</td>
                        <td>{{ $food->quantity }}</td>
                        <td style="color:var(--primary);font-weight:700;">{{ number_format($food->revenue, 0, ',', '.') }} ₫</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;color:#94A3B8;padding:22px;">Chưa có dữ liệu bán hàng.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card" style="margin-bottom:0;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:12px;">
            <h3 style="font-size:16px;font-weight:800;"><i class="fa-solid fa-calendar-days" style="color:#2563EB;margin-right:6px;"></i> Doanh thu theo ngày</h3>
            <span style="font-size:12px;color:#64748B;">Tối đa 31 ngày</span>
        </div>
        <table class="table">
            <thead><tr><th>NGÀY</th><th>DOANH THU</th></tr></thead>
            <tbody>
                @forelse($dailyRevenue as $date => $revenue)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                        <td style="color:#059669;font-weight:700;">{{ number_format($revenue, 0, ',', '.') }} ₫</td>
                    </tr>
                @empty
                    <tr><td colspan="2" style="text-align:center;color:#94A3B8;padding:22px;">Chưa có dữ liệu doanh thu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 800;"><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-right: 6px;"></i> Đơn Hàng Gần Đây</h3>
        <a href="{{ route('admin.orders.index') }}" style="color: var(--primary); font-size: 13px; font-weight: 700; text-decoration: none;">Xem tất cả &rarr;</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>MÃ ĐƠN</th>
                <th>KHÁCH HÀNG</th>
                <th>SỐ ĐIỆN THOẠI</th>
                <th>TỔNG TIỀN</th>
                <th>TRẠNG THÁI</th>
                <th>THỜI GIAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentOrders as $order)
                <tr>
                    <td><strong>{{ $order->order_code }}</strong></td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->customer_phone }}</td>
                    <td style="font-weight: 700; color: var(--primary);">{{ number_format($order->total_price, 0, ',', '.') }} ₫</td>
                    <td>
                        <span class="badge" style="background: #FEF3C7; color: #D97706;">
                            {{ $order->status->value }}
                        </span>
                    </td>
                    <td style="color: #64748B; font-size: 13px;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94A3B8; padding: 30px;">Chưa có đơn hàng nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
