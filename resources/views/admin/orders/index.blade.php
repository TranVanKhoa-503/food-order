@extends('admin.layout')

@section('title', 'Quản Lý Đơn Hàng - FoodOrder Admin')
@section('header_title', 'Danh Sách & Xử Lý Đơn Hàng')

@section('content')
<div class="card">
    <form method="GET" action="{{ route('admin.orders.index') }}" style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tìm mã đơn, tên, SĐT..." style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px; width: 260px;">
        <select name="status" style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
            <option value="">Tất cả trạng thái</option>
            <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Chờ xác nhận (pending)</option>
            <option value="confirmed" {{ ($status ?? '') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận (confirmed)</option>
            <option value="delivering" {{ ($status ?? '') === 'delivering' ? 'selected' : '' }}>Đang giao (delivering)</option>
            <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Hoàn tất (completed)</option>
            <option value="cancelled" {{ ($status ?? '') === 'cancelled' ? 'selected' : '' }}>Đã hủy (cancelled)</option>
        </select>
        <input type="date" name="from" value="{{ $from ?? '' }}" title="Từ ngày" style="padding:8px 10px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
        <input type="date" name="to" value="{{ $to ?? '' }}" title="Đến ngày" style="padding:8px 10px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
        <select name="per_page" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 đơn/trang</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 đơn/trang</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 đơn/trang</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Lọc đơn</button>
        <a href="{{ route('admin.orders.export', array_filter(['search' => $search ?? '', 'status' => $status ?? '', 'from' => $from ?? '', 'to' => $to ?? ''])) }}" class="btn" style="background:#ECFDF5;color:#047857;">
            <i class="fa-solid fa-file-csv"></i> Xuất CSV
        </a>
        @if(!empty($search) || !empty($status) || !empty($from) || !empty($to) || request()->has('per_page'))
            <a href="{{ route('admin.orders.index') }}" class="btn" style="background: #F1F5F9; color: #64748B;">Xóa lọc</a>
        @endif
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>MÃ ĐƠN</th>
                <th>KHÁCH HÀNG</th>
                <th>MÓN ĂN</th>
                <th>TỔNG TIỀN</th>
                <th>TRẠNG THÁI</th>
                <th>SHIPPER GIAO HÀNG</th>
                <th>CHUYỂN TRẠNG THÁI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" style="color: var(--primary);font-weight:800;">{{ $order->order_code }}</a>
                        <div style="font-size: 11px; color: #94A3B8;">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 700;">{{ $order->customer_name }}</div>
                        <div style="font-size: 12px; color: #64748B;">{{ $order->customer_phone }}</div>
                        <div style="font-size: 12px; color: #94A3B8; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $order->delivery_address }}</div>
                        @if($order->delivery_zone_name)
                            <div style="font-size: 11px; color: #2563EB;">Khu vực: {{ $order->delivery_zone_name }}</div>
                        @endif
                    </td>
                    <td>
                        @foreach($order->items as $item)
                            <div style="font-size: 13px;">{{ $item->food_name }} <span style="color:#64748B;">x{{ $item->quantity }}</span></div>
                        @endforeach
                    </td>
                    <td>
                        <div style="font-weight: 800; color: var(--dark);">{{ number_format($order->total_price, 0, ',', '.') }} ₫</div>
                        <div style="font-size: 11px; color: #059669; font-weight: 700;">{{ strtoupper($order->payment_method->value) }} ({{ $order->payment_status->value }})</div>
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'pending' => ['bg' => '#FEF3C7', 'color' => '#D97706'],
                                'confirmed' => ['bg' => '#E0E7FF', 'color' => '#4338CA'],
                                'delivering' => ['bg' => '#CFFAFE', 'color' => '#0E7490'],
                                'completed' => ['bg' => '#D1FAE5', 'color' => '#047857'],
                                'cancelled' => ['bg' => '#FEE2E2', 'color' => '#B91C1C'],
                            ];
                            $color = $statusColors[$order->status->value] ?? ['bg' => '#F1F5F9', 'color' => '#475569'];
                        @endphp
                        <span class="badge" style="background: {{ $color['bg'] }}; color: {{ $color['color'] }};">
                            {{ $order->status->value }}
                        </span>
                    </td>
                    <td>
                        @if(in_array($order->status->value, ['completed', 'cancelled']))
                            @if($order->shipper)
                                <span style="font-weight: 600; color: #2563EB; font-size: 13px;">
                                    <i class="fa-solid fa-motorcycle"></i> {{ $order->shipper->name }}
                                </span>
                            @else
                                <span style="color: #94A3B8; font-size: 12px;">Chưa phân công</span>
                            @endif
                        @else
                            <form action="{{ route('admin.orders.assign-shipper', $order) }}" method="POST">
                                @csrf
                                <select name="shipper_id" onchange="this.form.submit()" style="padding: 5px 8px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 12px; background: white; cursor: pointer;">
                                    <option value="">-- Phân công --</option>
                                    @foreach($shippers as $s)
                                        <option value="{{ $s->id }}" {{ $order->shipper_id === $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            @if($order->assigned_at)
                                <div style="font-size: 10px; color: #64748B; margin-top: 3px;">
                                    <i class="fa-solid fa-clock"></i> {{ $order->assigned_at->format('H:i d/m') }}
                                </div>
                            @endif
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            @if($order->status->value === 'pending')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'confirmed')" class="btn" style="background: #E0E7FF; color: #4338CA; padding: 4px 8px; font-size: 11px;">
                                    Xác nhận
                                </button>
                                <button onclick="updateOrderStatus({{ $order->id }}, 'cancelled', true)" class="btn" style="background: #FEE2E2; color: #B91C1C; padding: 4px 8px; font-size: 11px;">
                                    Hủy
                                </button>
                            @elseif($order->status->value === 'confirmed')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'delivering')" class="btn" style="background: #CFFAFE; color: #0E7490; padding: 4px 8px; font-size: 11px;">
                                    Giao hàng
                                </button>
                                <button onclick="updateOrderStatus({{ $order->id }}, 'cancelled', true)" class="btn" style="background: #FEE2E2; color: #B91C1C; padding: 4px 8px; font-size: 11px;">
                                    Hủy
                                </button>
                            @elseif($order->status->value === 'delivering')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'completed')" class="btn" style="background: #D1FAE5; color: #047857; padding: 4px 8px; font-size: 11px;">
                                    Hoàn tất
                                </button>
                                <button onclick="updateOrderStatus({{ $order->id }}, 'cancelled', true)" class="btn" style="background: #FEE2E2; color: #B91C1C; padding: 4px 8px; font-size: 11px;">
                                    Hủy (Thất bại)
                                </button>
                            @else
                                <span style="font-size: 12px; color: #94A3B8; font-style: italic;">Không có hành động</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #94A3B8; padding: 30px;">Không tìm thấy đơn hàng nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function updateOrderStatus(orderId, targetStatus, requireReason = false) {
        let reason = null;
        if (requireReason) {
            reason = prompt('Vui lòng nhập lý do hủy đơn hàng:');
            if (reason === null) return;
            if (!reason.trim()) {
                alert('Lý do hủy không được để trống!');
                return;
            }
        }

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(`/api/v1/admin/orders/${orderId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: targetStatus,
                    reason: reason
                })
            });

            if (res.ok) {
                window.location.reload();
            } else {
                const data = await res.json();
                alert(data.message || 'Lỗi cập nhật trạng thái đơn hàng!');
            }
        } catch (e) {
            alert('Lỗi kết nối máy chủ!');
        }
    }
</script>
@endsection
