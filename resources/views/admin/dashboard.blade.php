@extends('admin.layout')

@section('title', 'Dashboard Quản Trị & Tài Chính - FoodOrder')
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

{{-- BÁO CÁO TÀI CHÍNH: DOANH THU - CHI PHÍ - LỢI NHUẬN --}}
<div class="card" style="margin-bottom: 24px; border-left: 4px solid var(--primary); background: linear-gradient(135deg, #FFFFFF 0%, #FFF7ED 100%);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 18px; font-weight: 800; color: #1E293B;">
                <i class="fa-solid fa-scale-balanced" style="color: var(--primary); margin-right: 8px;"></i>
                Báo Cáo Thu Chi & Lợi Nhuận Thực Tế
            </h2>
            <p style="font-size: 13px; color: #64748B; margin-top: 4px;">Doanh thu theo ngày giao thành công; lương theo kỳ tháng, gồm bảng lương đã tạo và ước tính phần còn thiếu.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.payrolls.index') }}" class="btn" style="background: #EEF2FF; color: #4338CA;">
                <i class="fa-solid fa-money-check-dollar"></i> Quản lý Bảng Lương
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="btn" style="background: #FDF2F8; color: #BE185D;">
                <i class="fa-solid fa-file-invoice-dollar"></i> Quản lý Chi Phí
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div style="background: white; border: 1px solid #E2E8F0; border-radius: var(--radius-md); padding: 18px;">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
                <i class="fa-solid fa-arrow-trend-up" style="color: #059669; margin-right: 6px;"></i> Doanh Thu Bán Hàng
            </div>
            <div style="font-size: 22px; font-weight: 800; color: #059669; margin-top: 8px;">
                {{ number_format($totalRevenue, 0, ',', '.') }} ₫
            </div>
            <div style="font-size: 11px; color: #94A3B8; margin-top: 4px;">Từ các đơn hoàn thành</div>
        </div>

        <div style="background: white; border: 1px solid #E2E8F0; border-radius: var(--radius-md); padding: 18px;">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
                <i class="fa-solid fa-user-tie" style="color: #2563EB; margin-right: 6px;"></i> Lương Cứng Shipper
            </div>
            <div style="font-size: 22px; font-weight: 800; color: #1E293B; margin-top: 8px;">
                {{ number_format($totalBaseSalary, 0, ',', '.') }} ₫
            </div>
            <div style="font-size: 11px; color: #94A3B8; margin-top: 4px;">2.000.000 ₫/tháng/shipper</div>
        </div>

        <div style="background: white; border: 1px solid #E2E8F0; border-radius: var(--radius-md); padding: 18px;">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
                <i class="fa-solid fa-percent" style="color: #D97706; margin-right: 6px;"></i> Hoa Hồng Shipper
            </div>
            <div style="font-size: 22px; font-weight: 800; color: #D97706; margin-top: 8px;">
                {{ number_format($totalCommission, 0, ',', '.') }} ₫
            </div>
            <div style="font-size: 11px; color: #94A3B8; margin-top: 4px;">50% phí ship đơn đã giao</div>
        </div>

        <div style="background: white; border: 1px solid #E2E8F0; border-radius: var(--radius-md); padding: 18px;">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
                <i class="fa-solid fa-building" style="color: #BE185D; margin-right: 6px;"></i> Chi Phí Vận Hành
            </div>
            <div style="font-size: 22px; font-weight: 800; color: #BE185D; margin-top: 8px;">
                {{ number_format($totalOperatingExpenses, 0, ',', '.') }} ₫
            </div>
            <div style="font-size: 11px; color: #94A3B8; margin-top: 4px;">Mặt bằng, điện nước, quảng cáo...</div>
        </div>

        <div style="background: white; border: 1px solid #E2E8F0; border-radius: var(--radius-md); padding: 18px;">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
                <i class="fa-solid fa-calculator" style="color: #DC2626; margin-right: 6px;"></i> Tổng Chi Phí
            </div>
            <div style="font-size: 22px; font-weight: 800; color: #DC2626; margin-top: 8px;">
                {{ number_format($totalExpenses, 0, ',', '.') }} ₫
            </div>
            <div style="font-size: 11px; color: #94A3B8; margin-top: 4px;">Lương + Hoa hồng + Chi phí</div>
        </div>

        <div style="background: {{ $netProfit >= 0 ? '#ECFDF5' : '#FEF2F2' }}; border: 1px solid {{ $netProfit >= 0 ? '#A7F3D0' : '#FECACA' }}; border-radius: var(--radius-md); padding: 18px;">
            <div style="font-size: 12px; font-weight: 700; color: {{ $netProfit >= 0 ? '#065F46' : '#991B1B' }}; text-transform: uppercase;">
                <i class="fa-solid {{ $netProfit >= 0 ? 'fa-circle-check' : 'fa-triangle-exclamation' }}" style="margin-right: 6px;"></i>
                Chênh Lệch (Lợi Nhuận)
            </div>
            <div style="font-size: 22px; font-weight: 800; color: {{ $netProfit >= 0 ? '#059669' : '#DC2626' }}; margin-top: 8px;">
                {{ ($netProfit > 0 ? '+' : '') . number_format($netProfit, 0, ',', '.') }} ₫
            </div>
            <div style="font-size: 11px; color: {{ $netProfit >= 0 ? '#047857' : '#B91C1C' }}; margin-top: 4px;">
                = Doanh thu - Tổng chi phí
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
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

    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: var(--radius-md); background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-motorcycle"></i>
        </div>
        <div>
            <div style="font-size: 13px; color: #64748B; font-weight: 600;">Đội Ngũ Shipper</div>
            <div style="font-size: 22px; font-weight: 800; color: var(--dark);">{{ $totalShippers ?? 0 }}</div>
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
                <th>SHIPPER</th>
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
                    <td>
                        @if($order->shipper)
                            <span style="font-weight: 600; color: #2563EB;"><i class="fa-solid fa-motorcycle" style="font-size: 11px;"></i> {{ $order->shipper->name }}</span>
                        @else
                            <span style="color: #94A3B8; font-style: italic;">Chưa phân công</span>
                        @endif
                    </td>
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
