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
            <option value="preparing" {{ ($status ?? '') === 'preparing' ? 'selected' : '' }}>Đang chế biến (preparing)</option>
            <option value="delivering" {{ ($status ?? '') === 'delivering' ? 'selected' : '' }}>Đang giao (delivering)</option>
            <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Hoàn tất (completed)</option>
            <option value="cancelled" {{ ($status ?? '') === 'cancelled' ? 'selected' : '' }}>Đã hủy (cancelled)</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Lọc đơn</button>
        @if(!empty($search) || !empty($status))
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
                <th>CHUYỂN TRẠNG THÁI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>
                        <strong style="color: var(--primary);">{{ $order->order_code }}</strong>
                        <div style="font-size: 11px; color: #94A3B8;">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 700;">{{ $order->customer_name }}</div>
                        <div style="font-size: 12px; color: #64748B;">{{ $order->customer_phone }}</div>
                        <div style="font-size: 12px; color: #94A3B8; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $order->delivery_address }}</div>
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
                                'preparing' => ['bg' => '#EDE9FE', 'color' => '#6D28D9'],
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
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            @if($order->status->value === 'pending')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'confirmed')" class="btn" style="background: #E0E7FF; color: #4338CA; padding: 4px 8px; font-size: 11px;">
                                    Xác nhận
                                </button>
                                <button onclick="updateOrderStatus({{ $order->id }}, 'cancelled', true)" class="btn" style="background: #FEE2E2; color: #B91C1C; padding: 4px 8px; font-size: 11px;">
                                    Hủy
                                </button>
                            @elseif($order->status->value === 'confirmed')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'preparing')" class="btn" style="background: #EDE9FE; color: #6D28D9; padding: 4px 8px; font-size: 11px;">
                                    Chế biến
                                </button>
                                <button onclick="updateOrderStatus({{ $order->id }}, 'cancelled', true)" class="btn" style="background: #FEE2E2; color: #B91C1C; padding: 4px 8px; font-size: 11px;">
                                    Hủy
                                </button>
                            @elseif($order->status->value === 'preparing')
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
                                    Hủy
                                </button>
                            @else
                                <span style="font-size: 12px; color: #94A3B8; font-style: italic;">Không có hành động</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94A3B8; padding: 30px;">Không tìm thấy đơn hàng nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $orders->links() }}
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
