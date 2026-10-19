@extends('admin.layout')

@section('title', 'Chi Tiết Bảng Lương Shipper - FoodOrder')
@section('header_title', 'Chi Tiết Bảng Lương Shipper')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.payrolls.index', ['month' => $payroll->month, 'year' => $payroll->year]) }}" class="btn" style="background: white; border: 1px solid var(--border-color); color: #64748B;">
        <i class="fa-solid fa-arrow-left"></i> Quay lại bảng lương T{{ $payroll->month }}/{{ $payroll->year }}
    </a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 24px;">
    {{-- THÔNG TIN SHIPPER & KỲ LƯƠNG --}}
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fa-solid fa-motorcycle"></i>
            </div>
            <div>
                <h3 style="font-size: 18px; font-weight: 800; color: #1E293B;">{{ $payroll->shipper?->name }}</h3>
                <div style="font-size: 13px; color: #64748B;">{{ $payroll->shipper?->phone ?: 'Chưa cập nhật SĐT' }} &bull; {{ $payroll->shipper?->email }}</div>
            </div>
        </div>

        <div style="border-top: 1px solid var(--border-color); padding-top: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
            <div>
                <span style="color: #64748B;">Kỳ lương:</span>
                <strong style="display: block; color: #1E293B;">Tháng {{ $payroll->month }}/{{ $payroll->year }}</strong>
            </div>
            <div>
                <span style="color: #64748B;">Trạng thái:</span>
                <div>
                    @if($payroll->status === 'draft')
                        <span class="badge" style="background: #FEF3C7; color: #D97706;">Nháp (Draft)</span>
                    @elseif($payroll->status === 'finalized')
                        <span class="badge" style="background: #EFF6FF; color: #2563EB;">Đã chốt (Finalized)</span>
                    @elseif($payroll->status === 'paid')
                        <span class="badge" style="background: #ECFDF5; color: #059669;">Đã thanh toán (Paid)</span>
                    @endif
                </div>
            </div>
            @if($payroll->finalized_at)
                <div>
                    <span style="color: #64748B;">Thời gian chốt:</span>
                    <strong style="display: block; color: #1E293B;">{{ $payroll->finalized_at->format('d/m/Y H:i') }}</strong>
                    <span style="font-size: 11px; color: #94A3B8;">Bởi: {{ $payroll->finalizer?->name }}</span>
                </div>
            @endif
            @if($payroll->paid_at)
                <div>
                    <span style="color: #64748B;">Thời gian thanh toán:</span>
                    <strong style="display: block; color: #059669;">{{ $payroll->paid_at->format('d/m/Y H:i') }}</strong>
                    <span style="font-size: 11px; color: #94A3B8;">Bởi: {{ $payroll->payer?->name }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- CƠ CẤU LƯƠNG & THU NHẬP --}}
    <div class="card" style="margin-bottom: 0; background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);">
        <h4 style="font-size: 14px; font-weight: 800; color: #64748B; text-transform: uppercase; margin-bottom: 16px;">
            <i class="fa-solid fa-calculator" style="color: var(--primary); margin-right: 6px;"></i> Chi Tiết Thu Nhập
        </h4>

        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 14px;">
            <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--border-color);">
                <span style="color: #475569;">1. Lương cứng cố định:</span>
                <strong style="color: #1E293B;">{{ number_format($payroll->base_salary, 0, ',', '.') }} ₫</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--border-color);">
                <span style="color: #475569;">2. Số đơn đã giao thành công:</span>
                <strong style="color: #1E293B;">{{ $payroll->completed_orders_count }} đơn</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--border-color);">
                <span style="color: #475569;">3. Tổng phí ship của các đơn:</span>
                <strong style="color: #1E293B;">{{ number_format($payroll->total_shipping_fee, 0, ',', '.') }} ₫</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed var(--border-color);">
                <span style="color: #475569;">4. Hoa hồng nhận được ({{ (int)($payroll->commission_rate * 100) }}% phí ship):</span>
                <strong style="color: #D97706;">+ {{ number_format($payroll->total_commission, 0, ',', '.') }} ₫</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-top: 8px; font-size: 18px;">
                <span style="font-weight: 800; color: #0F172A;">Tổng thực nhận (1 + 4):</span>
                <span style="font-weight: 800; color: #059669;">{{ number_format($payroll->total_salary, 0, ',', '.') }} ₫</span>
            </div>
        </div>

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            @if($payroll->status === 'draft')
                <form method="POST" action="{{ route('admin.payrolls.finalize', $payroll) }}" data-prevent-double-submit style="flex: 1;" onsubmit="return confirm('Tính lại số liệu mới nhất và chốt bảng lương này?');">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-lock"></i> Chốt bảng lương
                    </button>
                </form>
            @elseif($payroll->status === 'finalized')
                <form method="POST" action="{{ route('admin.payrolls.pay', $payroll) }}" data-prevent-double-submit style="flex: 1;" onsubmit="return confirm('Xác nhận đã thanh toán?');">
                    @csrf
                    <button type="submit" class="btn" style="width: 100%; justify-content: center; background: #059669; color: white;">
                        <i class="fa-solid fa-check-double"></i> Xác nhận thanh toán
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- DANH SÁCH ĐƠN HÀNG HOÀN TẤT ĐÃ GIAO --}}
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 800;">
            <i class="fa-solid fa-receipt" style="color: var(--primary); margin-right: 8px;"></i>
            Danh Sách Đơn Hàng Hoàn Tất Trong Kỳ ({{ $orders->count() }} đơn)
        </h3>
        <span style="font-size: 13px; color: #64748B;">Căn cứ tính hoa hồng 50%</span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>MÃ ĐƠN</th>
                    <th>THỜI GIAN HOÀN TẤT</th>
                    <th>KHÁCH HÀNG</th>
                    <th>ĐỊA CHỈ GIAO</th>
                    <th>TỔNG GIÁ ĐƠN</th>
                    <th>PHÍ SHIP</th>
                    <th>HOA HỒNG (50%)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" style="font-weight: 800; color: var(--primary);">
                                {{ $order->order_code }}
                            </a>
                        </td>
                        <td>
                            <span style="font-size: 13px; color: #1E293B;">
                                {{ $order->completed_at ? $order->completed_at->format('d/m/Y H:i') : $order->created_at->format('d/m/Y H:i') }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700;">{{ $order->customer_name }}</div>
                            <div style="font-size: 12px; color: #64748B;">{{ $order->customer_phone }}</div>
                        </td>
                        <td>
                            <div style="font-size: 12px; color: #64748B; max-width: 250px;">{{ $order->delivery_address }}</div>
                        </td>
                        <td>
                            <span style="font-weight: 700;">{{ number_format($order->total_price, 0, ',', '.') }} ₫</span>
                        </td>
                        <td>
                            <span style="color: #2563EB; font-weight: 700;">{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                        </td>
                        <td>
                            <span style="color: #D97706; font-weight: 800;">
                                + {{ number_format(round($order->shipping_fee * 0.5), 0, ',', '.') }} ₫
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94A3B8; padding: 30px;">
                            Chưa có đơn hàng hoàn tất nào được ghi nhận cho shipper này trong tháng {{ $payroll->month }}/{{ $payroll->year }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
