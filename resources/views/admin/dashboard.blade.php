@extends('admin.layout')

@section('title', 'Dashboard Quản Trị - FoodOrder')
@section('header_title', 'Tổng Quan Hoạt Động Cửa Hàng')

@section('content')
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
