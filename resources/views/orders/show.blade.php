@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng - FoodOrder')

@section('content')
@php
    $labels = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'preparing' => 'Đang chế biến',
        'delivering' => 'Đang giao hàng',
        'completed' => 'Đã hoàn thành',
        'cancelled' => 'Đã hủy',
    ];
    $currentStatus = $order->status->value;
@endphp
<div class="container" style="max-width:1000px;padding:40px 20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px;">
        <div>
            <a href="{{ Auth::user()->isAdmin() ? route('admin.orders.index') : route('orders.index') }}" style="color:#64748B;text-decoration:none;font-size:13px;"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách đơn</a>
            <h1 style="font-size:28px;font-weight:800;margin-top:10px;">Đơn hàng {{ $order->order_code }}</h1>
            <p style="color:#64748B;font-size:13px;">Đặt lúc {{ $order->created_at?->format('d/m/Y H:i') }}</p>
        </div>
        <span style="background:#FFF7ED;color:#C2410C;padding:8px 16px;border-radius:50px;font-weight:800;">{{ $labels[$currentStatus] ?? $currentStatus }}</span>
    </div>

    <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:20px;">
        <div>
            <div style="background:white;border:1px solid var(--border-color);border-radius:var(--radius-lg);padding:24px;margin-bottom:20px;">
                <h2 style="font-size:17px;font-weight:800;margin-bottom:16px;"><i class="fa-solid fa-basket-shopping" style="color:var(--primary);"></i> Món đã đặt</h2>
                @foreach($order->items as $item)
                    <div style="display:flex;justify-content:space-between;gap:16px;padding:12px 0;border-bottom:1px dashed var(--border-color);">
                        <div><strong>{{ $item->food_name }}</strong><span style="color:#64748B;margin-left:8px;">x{{ $item->quantity }}</span>@if($item->note)<div style="font-size:12px;color:#64748B;">Ghi chú: {{ $item->note }}</div>@endif</div>
                        <strong>{{ number_format($item->line_total, 0, ',', '.') }} ₫</strong>
                    </div>
                @endforeach
                <div style="margin-top:16px;display:grid;gap:8px;font-size:14px;">
                    <div style="display:flex;justify-content:space-between;"><span>Tạm tính</span><span>{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span></div>
                    <div style="display:flex;justify-content:space-between;color:#059669;"><span>Giảm giá{{ $order->voucher ? ' ('.$order->voucher->code.')' : '' }}</span><span>-{{ number_format($order->discount_amount, 0, ',', '.') }} ₫</span></div>
                    <div style="display:flex;justify-content:space-between;"><span>Phí giao hàng</span><span>{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span></div>
                    <div style="display:flex;justify-content:space-between;font-size:20px;font-weight:800;color:var(--primary);padding-top:10px;border-top:1px solid var(--border-color);"><span>Tổng cộng</span><span>{{ number_format($order->total_price, 0, ',', '.') }} ₫</span></div>
                </div>
            </div>

            <div style="background:white;border:1px solid var(--border-color);border-radius:var(--radius-lg);padding:24px;">
                <h2 style="font-size:17px;font-weight:800;margin-bottom:16px;"><i class="fa-solid fa-route" style="color:var(--primary);"></i> Lịch sử trạng thái</h2>
                @forelse($order->statusHistories as $history)
                    <div style="display:flex;gap:12px;position:relative;padding-bottom:18px;">
                        <div style="width:12px;height:12px;background:var(--primary);border-radius:50%;margin-top:4px;flex:0 0 auto;"></div>
                        <div><strong>{{ $labels[$history->to_status] ?? $history->to_status }}</strong><div style="font-size:12px;color:#64748B;">{{ $history->created_at?->format('d/m/Y H:i') }}{{ $history->actor ? ' · '.$history->actor->name : '' }}</div>@if($history->reason)<div style="font-size:13px;color:#475569;margin-top:3px;">{{ $history->reason }}</div>@endif</div>
                    </div>
                @empty
                    <p style="color:#64748B;">Chưa có lịch sử trạng thái.</p>
                @endforelse
            </div>
        </div>

        <div>
            <div style="background:white;border:1px solid var(--border-color);border-radius:var(--radius-lg);padding:24px;margin-bottom:20px;">
                <h2 style="font-size:17px;font-weight:800;margin-bottom:16px;"><i class="fa-solid fa-location-dot" style="color:var(--primary);"></i> Thông tin giao hàng</h2>
                <p style="margin-bottom:8px;"><strong>{{ $order->customer_name }}</strong></p>
                <p style="color:#475569;margin-bottom:8px;">{{ $order->customer_phone }}</p>
                <p style="color:#475569;line-height:1.6;">{{ $order->delivery_address }}</p>
                @if($order->delivery_zone_name)<p style="margin-top:8px;color:#2563EB;font-size:13px;">Khu vực giao: {{ $order->delivery_zone_name }}</p>@endif
                @if($order->note)<p style="margin-top:12px;color:#64748B;font-size:13px;">Ghi chú: {{ $order->note }}</p>@endif
                <p style="margin-top:16px;color:#64748B;font-size:13px;">Thanh toán: <strong>{{ strtoupper($order->payment_method->value) }}</strong> · {{ $order->payment_status->value === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}</p>
            </div>

            @if(!Auth::user()->isAdmin() && $currentStatus === 'pending')
                <button onclick="cancelOrder({{ $order->id }})" class="checkout-btn" style="background:#FEE2E2;color:#B91C1C;border:1px solid #FECACA;"><i class="fa-solid fa-xmark"></i> Hủy đơn hàng</button>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function cancelOrder(orderId) {
    if (!confirm('Bạn có chắc muốn hủy đơn hàng này không?')) return;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const response = await fetch(`/api/v1/orders/${orderId}/cancel`, {method:'PATCH',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},body:JSON.stringify({reason:'Khách hàng hủy từ trang chi tiết'})});
    if (response.ok) window.location.reload();
    else { const data = await response.json(); alert(data.message || 'Không thể hủy đơn hàng.'); }
}
</script>
@endsection
