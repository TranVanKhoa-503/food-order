@extends('admin.layout')

@section('title', 'Quản Lý Đội Ngũ Shipper - FoodOrder')
@section('header_title', 'Quản Lý Đội Ngũ Shipper')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: var(--radius-md); background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-motorcycle"></i>
        </div>
        <div>
            <div style="font-size: 13px; color: #64748B; font-weight: 600;">Tổng Số Shipper</div>
            <div style="font-size: 22px; font-weight: 800; color: var(--dark);">{{ $shippers->count() }}</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: var(--radius-md); background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div>
            <div style="font-size: 13px; color: #64748B; font-weight: 600;">Đang Hoạt Động</div>
            <div style="font-size: 22px; font-weight: 800; color: #059669;">{{ $shippers->where('is_active', true)->count() }}</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: var(--radius-md); background: #CFFAFE; color: #0891B2; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-box-open"></i>
        </div>
        <div>
            <div style="font-size: 13px; color: #64748B; font-weight: 600;">Đơn Đang Giao</div>
            <div style="font-size: 22px; font-weight: 800; color: #0891B2;">{{ $shippers->sum('delivering_count') }}</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: var(--radius-md); background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <div style="font-size: 13px; color: #64748B; font-weight: 600;">Đơn Đã Giao (Tháng Này)</div>
            <div style="font-size: 22px; font-weight: 800; color: #D97706;">{{ $shippers->sum('completed_this_month_count') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <form method="GET" action="{{ route('admin.shippers.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tìm tên, SĐT, email..." style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px; width: 260px;">
            <select name="is_active" style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ ($isActive ?? '') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="0" {{ ($isActive ?? '') === '0' ? 'selected' : '' }}>Bị khóa</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Lọc</button>
            @if(!empty($search) || !is_null($isActive))
                <a href="{{ route('admin.shippers.index') }}" class="btn" style="background: #F1F5F9; color: #64748B;">Xóa lọc</a>
            @endif
        </form>

        <a href="{{ route('admin.payrolls.index') }}" class="btn" style="background: #EEF2FF; color: #4338CA;">
            <i class="fa-solid fa-money-check-dollar"></i> Xem bảng tính lương
        </a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>SHIPPER</th>
                <th>SỐ ĐIỆN THOẠI</th>
                <th>TRẠNG THÁI</th>
                <th>ĐANG GIAO</th>
                <th>GIAO THÁNG NÀY</th>
                <th>TỔNG ĐÃ GIAO</th>
                <th>LƯƠNG DỰ KIẾN (T{{ now()->month }})</th>
                <th>HÀNH ĐỘNG</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shippers as $shipper)
                <tr>
                    <td>
                        <div style="font-weight: 800; font-size: 14px; color: var(--dark);">{{ $shipper->name }}</div>
                        <div style="font-size: 12px; color: #94A3B8;">{{ $shipper->email }}</div>
                    </td>
                    <td>
                        <span style="font-weight: 600; color: #334155;">{{ $shipper->phone ?: 'Chưa cập nhật' }}</span>
                    </td>
                    <td>
                        @if($shipper->is_active)
                            <span class="badge" style="background: #ECFDF5; color: #059669;">Hoạt động</span>
                        @else
                            <span class="badge" style="background: #FEF2F2; color: #DC2626;">Đã khóa</span>
                        @endif
                    </td>
                    <td>
                        @if($shipper->delivering_count > 0)
                            <span class="badge" style="background: #CFFAFE; color: #0891B2; font-weight: 800;">
                                {{ $shipper->delivering_count }} đơn
                            </span>
                        @else
                            <span style="color: #94A3B8; font-size: 13px;">0 đơn</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight: 700; color: #1E293B;">{{ $shipper->completed_this_month_count }} đơn</span>
                    </td>
                    <td>
                        <span style="color: #64748B;">{{ $shipper->total_completed_count }} đơn</span>
                    </td>
                    <td>
                        <div style="font-weight: 800; color: #059669;">{{ number_format($shipper->estimated_salary, 0, ',', '.') }} ₫</div>
                        <div style="font-size: 11px; color: #64748B;">
                            Cứng: 2M + HH: {{ number_format($shipper->estimated_commission, 0, ',', '.') }} ₫
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('admin.payrolls.index', ['shipper_id' => $shipper->id]) }}" class="btn" style="background: #F8FAFC; border: 1px solid var(--border-color); color: #2563EB; font-size: 12px; padding: 5px 10px;">
                                <i class="fa-solid fa-receipt"></i> Bảng lương
                            </a>
                            <a href="{{ route('admin.orders.index', ['search' => $shipper->name]) }}" class="btn" style="background: #F8FAFC; border: 1px solid var(--border-color); color: #64748B; font-size: 12px; padding: 5px 10px;">
                                <i class="fa-solid fa-list-check"></i> Đơn giao
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #94A3B8; padding: 30px;">Chưa có tài khoản shipper nào trong hệ thống.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
