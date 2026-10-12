@extends('layouts.app')

@section('title', 'Thu Nhập & Bảng Lương Shipper - FoodOrder')

@section('styles')
<style>
    .shipper-container {
        max-width: 1080px;
        margin: 0 auto;
        padding: 30px 16px 60px;
    }

    .shipper-header {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        border-radius: var(--radius-lg);
        padding: 24px 28px;
        color: white;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
    }

    .shipper-title-box h1 {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .shipper-badge-tag {
        background: #10B981;
        color: white;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 50px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .shipper-tabs {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 6px;
        margin-bottom: 20px;
    }

    .shipper-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        background: white;
        color: #475569;
        border: 1px solid var(--border-color);
        transition: var(--transition);
        white-space: nowrap;
    }

    .shipper-tab:hover {
        background: #F8FAFC;
        color: var(--dark);
    }

    .shipper-tab.active {
        background: var(--dark);
        color: white;
        border-color: var(--dark);
        box-shadow: var(--shadow-sm);
    }

    .card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 24px;
        margin-bottom: 24px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .table th {
        text-align: left;
        padding: 12px 14px;
        color: #64748B;
        font-size: 12px;
        font-weight: 700;
        border-bottom: 2px solid var(--border-color);
    }

    .table td {
        padding: 14px;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
    }
</style>
@endsection

@section('content')
<div class="shipper-container">
    {{-- HEADER --}}
    <div class="shipper-header">
        <div class="shipper-title-box">
            <h1>
                <i class="fa-solid fa-wallet" style="color: #34D399;"></i>
                Ví Thu Nhập & Bảng Lương Cá Nhân
                <span class="shipper-badge-tag">Lương Shipper</span>
            </h1>
            <p style="color: #94A3B8; font-size: 14px; margin-top: 4px;">
                Shipper: <strong>{{ Auth::user()->name }}</strong> &bull; Lương cứng: 2.000.000 ₫/tháng &bull; Hoa hồng: 50% phí ship đơn giao thành công
            </p>
        </div>
        <div>
            <a href="{{ route('shipper.orders.index') }}" class="btn" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.2); font-size: 13px; font-weight: 700; border-radius: 50px; padding: 10px 20px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-left"></i> Quay lại cổng giao hàng
            </a>
        </div>
    </div>

    {{-- TABS --}}
    <div class="shipper-tabs">
        <a href="{{ route('shipper.orders.index') }}" class="shipper-tab">
            <i class="fa-solid fa-truck-fast"></i>
            Đơn hàng giao
        </a>
        <a href="{{ route('shipper.salary') }}" class="shipper-tab active">
            <i class="fa-solid fa-wallet"></i>
            Bảng lương & Thu nhập
        </a>
    </div>

    {{-- THỐNG KÊ THÁNG HIỆN TẠI --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px;">
        <div class="card" style="margin-bottom: 0; border-left: 4px solid #10B981; background: linear-gradient(135deg, #FFFFFF 0%, #ECFDF5 100%);">
            <div style="font-size: 12px; font-weight: 700; color: #047857; text-transform: uppercase;">
                <i class="fa-solid fa-coins" style="margin-right: 6px;"></i> Tổng Dự Kiến Nhận (T{{ $currentMonth }}/{{ $currentYear }})
            </div>
            <div style="font-size: 26px; font-weight: 800; color: #059669; margin-top: 8px;">
                {{ number_format($currentEstimate['total_salary'], 0, ',', '.') }} ₫
            </div>
            <div style="font-size: 11px; color: #047857; margin-top: 4px;">= Lương cứng + Hoa hồng tích lũy</div>
        </div>

        <div class="card" style="margin-bottom: 0; border-left: 4px solid #2563EB;">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
                <i class="fa-solid fa-user-tie" style="color: #2563EB; margin-right: 6px;"></i> Lương Cứng Tháng {{ $currentMonth }}
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #1E293B; margin-top: 8px;">
                {{ number_format($currentEstimate['base_salary'], 0, ',', '.') }} ₫
            </div>
            <div style="font-size: 11px; color: #94A3B8; margin-top: 4px;">Định mức cố định hàng tháng</div>
        </div>

        <div class="card" style="margin-bottom: 0; border-left: 4px solid #D97706;">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
                <i class="fa-solid fa-percent" style="color: #D97706; margin-right: 6px;"></i> Hoa Hồng Giao Hàng
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #D97706; margin-top: 8px;">
                + {{ number_format($currentEstimate['total_commission'], 0, ',', '.') }} ₫
            </div>
            <div style="font-size: 11px; color: #94A3B8; margin-top: 4px;">50% x {{ number_format($currentEstimate['total_shipping_fee'], 0, ',', '.') }} ₫ phí ship</div>
        </div>

        <div class="card" style="margin-bottom: 0; border-left: 4px solid #7C3AED;">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
                <i class="fa-solid fa-circle-check" style="color: #7C3AED; margin-right: 6px;"></i> Đơn Đã Giao Tháng Này
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #7C3AED; margin-top: 8px;">
                {{ $currentEstimate['completed_orders_count'] }} đơn
            </div>
            <div style="font-size: 11px; color: #94A3B8; margin-top: 4px;">Tất cả đơn giao hoàn tất</div>
        </div>
    </div>

    {{-- LỊCH SỬ BẢNG LƯƠNG CÁC KỲ TRƯỚC --}}
    <div class="card">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">
            <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-right: 8px;"></i>
            Lịch Sử Bảng Lương Đã Được Chốt
        </h3>

        <table class="table">
            <thead>
                <tr>
                    <th>KỲ LƯƠNG</th>
                    <th>LƯƠNG CỨNG</th>
                    <th>SỐ ĐƠN GIAO</th>
                    <th>TỔNG PHÍ SHIP</th>
                    <th>HOA HỒNG (50%)</th>
                    <th>TỔNG THỰC NHẬN</th>
                    <th>TRẠNG THÁI</th>
                    <th>NGÀY THANH TOÁN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrollHistory as $record)
                    <tr>
                        <td>
                            <strong style="color: #1E293B;">Tháng {{ $record->month }}/{{ $record->year }}</strong>
                        </td>
                        <td>{{ number_format($record->base_salary, 0, ',', '.') }} ₫</td>
                        <td><strong>{{ $record->completed_orders_count }}</strong> đơn</td>
                        <td>{{ number_format($record->total_shipping_fee, 0, ',', '.') }} ₫</td>
                        <td style="color: #D97706; font-weight: 700;">+ {{ number_format($record->total_commission, 0, ',', '.') }} ₫</td>
                        <td>
                            <span style="font-size: 15px; font-weight: 800; color: #059669;">
                                {{ number_format($record->total_salary, 0, ',', '.') }} ₫
                            </span>
                        </td>
                        <td>
                            @if($record->status === 'paid')
                                <span class="badge" style="background: #ECFDF5; color: #059669;">
                                    <i class="fa-solid fa-circle-check"></i> Đã nhận tiền
                                </span>
                            @elseif($record->status === 'finalized')
                                <span class="badge" style="background: #EFF6FF; color: #2563EB;">
                                    <i class="fa-solid fa-lock"></i> Đã chốt (Chờ thanh toán)
                                </span>
                            @else
                                <span class="badge" style="background: #FEF3C7; color: #D97706;">
                                    <i class="fa-solid fa-pencil"></i> Tạm tính (Nháp)
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($record->paid_at)
                                <span style="font-size: 12px; color: #059669; font-weight: 600;">
                                    {{ $record->paid_at->format('d/m/Y H:i') }}
                                </span>
                            @else
                                <span style="font-size: 12px; color: #94A3B8;">Chưa thanh toán</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #94A3B8; padding: 24px;">
                            Chưa có kỳ lương nào được quản trị viên chốt. Số liệu tháng {{ $currentMonth }} hiện đang được tạm tính tự động ở trên.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CÁC ĐƠN HOÀN TẤT THÁNG NÀY --}}
    <div class="card">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">
            <i class="fa-solid fa-list-check" style="color: #2563EB; margin-right: 8px;"></i>
            Đơn Hàng Giao Thành Công Trong Tháng {{ $currentMonth }}/{{ $currentYear }} (Gần Nhất)
        </h3>

        <table class="table">
            <thead>
                <tr>
                    <th>MÃ ĐƠN</th>
                    <th>THỜI GIAN HOÀN TẤT</th>
                    <th>KHÁCH HÀNG</th>
                    <th>ĐỊA CHỈ</th>
                    <th>PHÍ SHIP</th>
                    <th>HOA HỒNG NHẬN ĐƯỢC (50%)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentCompletedOrders as $order)
                    <tr>
                        <td>
                            <strong style="color: var(--primary);">{{ $order->order_code }}</strong>
                        </td>
                        <td>
                            <span style="font-size: 13px; color: #64748B;">
                                {{ $order->completed_at ? $order->completed_at->format('d/m/Y H:i') : $order->created_at->format('d/m/Y H:i') }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700;">{{ $order->customer_name }}</div>
                            <div style="font-size: 12px; color: #64748B;">{{ $order->customer_phone }}</div>
                        </td>
                        <td>
                            <div style="font-size: 12px; color: #64748B; max-width: 260px;">{{ $order->delivery_address }}</div>
                        </td>
                        <td>
                            <span style="font-weight: 700; color: #2563EB;">{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                        </td>
                        <td>
                            <span style="font-weight: 800; color: #059669;">
                                + {{ number_format(round($order->shipping_fee * 0.5), 0, ',', '.') }} ₫
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94A3B8; padding: 24px;">
                            Bạn chưa hoàn thành đơn hàng nào trong tháng này.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
