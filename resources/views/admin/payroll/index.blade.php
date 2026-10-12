@extends('admin.layout')

@section('title', 'Quản Lý Bảng Lương Shipper - FoodOrder')
@section('header_title', 'Bảng Lương & Hoa Hồng Shipper')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
        <form method="GET" action="{{ route('admin.payrolls.index') }}" style="display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;">
            <div>
                <label style="display: block; font-size: 12px; color: #64748B; font-weight: 700; margin-bottom: 6px;">Tháng</label>
                <select name="month" style="padding: 9px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; color: #64748B; font-weight: 700; margin-bottom: 6px;">Năm</label>
                <select name="year" style="padding: 9px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; color: #64748B; font-weight: 700; margin-bottom: 6px;">Trạng thái</label>
                <select name="status" style="padding: 9px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
                    <option value="">Tất cả trạng thái</option>
                    <option value="draft" {{ ($status ?? '') === 'draft' ? 'selected' : '' }}>Nháp (draft)</option>
                    <option value="finalized" {{ ($status ?? '') === 'finalized' ? 'selected' : '' }}>Đã chốt (finalized)</option>
                    <option value="paid" {{ ($status ?? '') === 'paid' ? 'selected' : '' }}>Đã thanh toán (paid)</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; color: #64748B; font-weight: 700; margin-bottom: 6px;">Shipper</label>
                <select name="shipper_id" style="padding: 9px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
                    <option value="">Tất cả shipper</option>
                    @foreach($shippers as $s)
                        <option value="{{ $s->id }}" {{ ($shipperId ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Lọc kỳ lương</button>
            @if(!empty($status) || !empty($shipperId) || $month != now()->month || $year != now()->year)
                <a href="{{ route('admin.payrolls.index') }}" class="btn" style="background: #F1F5F9; color: #64748B;">Mặc định</a>
            @endif
        </form>

        <form method="POST" action="{{ route('admin.payrolls.generate') }}" onsubmit="return confirm('Hệ thống sẽ tính toán lại bảng lương tháng {{ $month }}/{{ $year }} cho tất cả shipper đang ở trạng thái Nháp. Tiếp tục?');">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn" style="background: #059669; color: white;">
                <i class="fa-solid fa-arrows-rotate"></i> Tính / Làm mới bảng lương T{{ $month }}/{{ $year }}
            </button>
        </form>
    </div>
</div>

{{-- TỔNG KẾT BẢNG LƯƠNG TRONG KỲ --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
            <i class="fa-solid fa-coins" style="color: #059669; margin-right: 6px;"></i> Tổng Lương Chi Trả
        </div>
        <div style="font-size: 24px; font-weight: 800; color: #059669; margin-top: 8px;">
            {{ number_format($totalSalary, 0, ',', '.') }} ₫
        </div>
        <div style="font-size: 12px; color: #94A3B8; margin-top: 4px;">Kỳ tháng {{ $month }}/{{ $year }}</div>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
            <i class="fa-solid fa-user-tie" style="color: #2563EB; margin-right: 6px;"></i> Tổng Lương Cứng
        </div>
        <div style="font-size: 24px; font-weight: 800; color: #1E293B; margin-top: 8px;">
            {{ number_format($totalBaseSalary, 0, ',', '.') }} ₫
        </div>
        <div style="font-size: 12px; color: #94A3B8; margin-top: 4px;">2.000.000 ₫/tháng</div>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
            <i class="fa-solid fa-percent" style="color: #D97706; margin-right: 6px;"></i> Tổng Hoa Hồng
        </div>
        <div style="font-size: 24px; font-weight: 800; color: #D97706; margin-top: 8px;">
            {{ number_format($totalCommission, 0, ',', '.') }} ₫
        </div>
        <div style="font-size: 12px; color: #94A3B8; margin-top: 4px;">50% phí ship đơn hoàn tất</div>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
            <i class="fa-solid fa-box" style="color: #7C3AED; margin-right: 6px;"></i> Tổng Đơn Đã Giao
        </div>
        <div style="font-size: 24px; font-weight: 800; color: #7C3AED; margin-top: 8px;">
            {{ $totalCompletedOrders }} đơn
        </div>
        <div style="font-size: 12px; color: #94A3B8; margin-top: 4px;">Trong tháng {{ $month }}/{{ $year }}</div>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 16px; font-weight: 800;">
            <i class="fa-solid fa-file-invoice-dollar" style="color: var(--primary); margin-right: 8px;"></i>
            Bảng Kê Chi Tiết Lương Shipper Tháng {{ $month }}/{{ $year }}
        </h3>
        <span style="font-size: 13px; color: #64748B;">Có <strong>{{ $payrolls->count() }}</strong> bản ghi</span>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>SHIPPER</th>
                <th>LƯƠNG CỨNG</th>
                <th>SỐ ĐƠN GIAO</th>
                <th>TỔNG PHÍ SHIP</th>
                <th>HOA HỒNG (50%)</th>
                <th>TỔNG THỰC NHẬN</th>
                <th>TRẠNG THÁI</th>
                <th>HÀNH ĐỘNG</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrolls as $payroll)
                <tr>
                    <td>
                        <a href="{{ route('admin.payrolls.show', $payroll) }}" style="font-weight: 800; color: #2563EB; text-decoration: none;">
                            {{ $payroll->shipper?->name ?? 'Shipper đã xóa' }}
                        </a>
                        <div style="font-size: 12px; color: #94A3B8;">{{ $payroll->shipper?->phone }}</div>
                    </td>
                    <td>
                        <span style="font-weight: 700;">{{ number_format($payroll->base_salary, 0, ',', '.') }} ₫</span>
                    </td>
                    <td>
                        <span style="font-weight: 800; color: #1E293B;">{{ $payroll->completed_orders_count }}</span> đơn
                    </td>
                    <td>
                        <span style="color: #64748B;">{{ number_format($payroll->total_shipping_fee, 0, ',', '.') }} ₫</span>
                    </td>
                    <td>
                        <span style="font-weight: 700; color: #D97706;">{{ number_format($payroll->total_commission, 0, ',', '.') }} ₫</span>
                    </td>
                    <td>
                        <span style="font-size: 15px; font-weight: 800; color: #059669;">
                            {{ number_format($payroll->total_salary, 0, ',', '.') }} ₫
                        </span>
                    </td>
                    <td>
                        @if($payroll->status === 'draft')
                            <span class="badge" style="background: #FEF3C7; color: #D97706;">
                                <i class="fa-solid fa-pencil"></i> Nháp (Draft)
                            </span>
                        @elseif($payroll->status === 'finalized')
                            <span class="badge" style="background: #EFF6FF; color: #2563EB;">
                                <i class="fa-solid fa-lock"></i> Đã chốt (Finalized)
                            </span>
                            @if($payroll->finalized_at)
                                <div style="font-size: 10px; color: #64748B; margin-top: 3px;">
                                    {{ $payroll->finalized_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        @elseif($payroll->status === 'paid')
                            <span class="badge" style="background: #ECFDF5; color: #059669;">
                                <i class="fa-solid fa-circle-check"></i> Đã thanh toán
                            </span>
                            @if($payroll->paid_at)
                                <div style="font-size: 10px; color: #059669; margin-top: 3px;">
                                    {{ $payroll->paid_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            <a href="{{ route('admin.payrolls.show', $payroll) }}" class="btn" style="background: #F8FAFC; border: 1px solid var(--border-color); color: #334155; font-size: 11px; padding: 5px 8px;">
                                Chi tiết
                            </a>

                            @if($payroll->status === 'draft')
                                <form method="POST" action="{{ route('admin.payrolls.finalize', $payroll) }}" onsubmit="return confirm('Bạn có chắc chắn muốn CHỐT bảng lương này? Sau khi chốt, số liệu sẽ được khóa.');">
                                    @csrf
                                    <button type="submit" class="btn" style="background: #2563EB; color: white; font-size: 11px; padding: 5px 8px;">
                                        <i class="fa-solid fa-lock"></i> Chốt lương
                                    </button>
                                </form>
                            @elseif($payroll->status === 'finalized')
                                <form method="POST" action="{{ route('admin.payrolls.pay', $payroll) }}" onsubmit="return confirm('Xác nhận ĐÃ THANH TOÁN tiền lương cho shipper này?');">
                                    @csrf
                                    <button type="submit" class="btn" style="background: #059669; color: white; font-size: 11px; padding: 5px 8px;">
                                        <i class="fa-solid fa-check-double"></i> Đã thanh toán
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #94A3B8; padding: 36px;">
                        <i class="fa-solid fa-file-invoice" style="font-size: 32px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                        Chưa có dữ liệu bảng lương cho tháng {{ $month }}/{{ $year }}.<br>
                        Nhấn nút <strong>"Tính / Làm mới bảng lương"</strong> ở trên để tự động tổng hợp từ dữ liệu đơn hàng đã giao thành công.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
