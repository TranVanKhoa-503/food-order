@extends('layouts.app')

@section('title', 'Lịch Sử Đơn Hàng - FoodOrder')

@section('styles')
<style>
    @media (max-width: 600px) {
        .order-index-container {
            padding: 20px 14px 40px !important;
        }
        .order-history-card {
            padding: 16px 14px !important;
            border-radius: 14px !important;
        }
        .order-card-footer {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 14px !important;
        }
        .order-card-pay-action {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container order-index-container" style="padding: 40px 20px; max-width: 1000px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark); margin-bottom: 6px;">
                <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Lịch Sử Đơn Hàng
            </h1>
            <p style="color: #64748B; font-size: 14px;">Theo dõi tiến trình và trạng thái các đơn giao tận nơi của bạn</p>
        </div>
        <a href="{{ route('home') }}" style="background: white; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 50px; text-decoration: none; color: var(--dark); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus" style="color: var(--primary);"></i> Đặt thêm món
        </a>
    </div>

    <form method="GET" action="{{ route('orders.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:20px;">
        <label style="font-size:13px;font-weight:700;color:#64748B;">Lọc trạng thái</label>
        <select name="status" style="padding:8px 12px;border:1px solid var(--border-color);border-radius:var(--radius-md);font-size:13px;">
            <option value="">Tất cả</option>
            @foreach(['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'delivering' => 'Đang giao', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'] as $value => $label)
                <option value="{{ $value }}" {{ ($status ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn" style="background:#F1F5F9;color:#475569;padding:8px 14px;border:1px solid var(--border-color);border-radius:var(--radius-md);font-weight:700;font-size:13px;cursor:pointer;" type="submit"><i class="fa-solid fa-filter"></i> Lọc</button>
    </form>

    @if(session('status'))
        <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 24px; font-size: 14px; font-weight: 600;">
            <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> {{ session('status') }}
        </div>
    @endif

    @if($orders->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($orders as $order)
                <div class="order-history-card" style="background: white; border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 24px; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 14px; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <div style="font-size: 12px; color: #64748B;">MÃ ĐƠN HÀNG</div>
                            <a href="{{ route('orders.show', $order) }}" style="font-size: 17px; font-weight: 800; color: var(--primary); letter-spacing: 0.5px; text-decoration:none;">{{ $order->order_code }}</a>
                            <div style="font-size: 12px; color: #94A3B8; margin-top: 2px;">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 10px;">
                            @php
                                $statusColors = [
                                    'pending' => ['bg' => '#FEF3C7', 'color' => '#D97706', 'text' => 'Chờ xác nhận'],
                                    'confirmed' => ['bg' => '#E0E7FF', 'color' => '#4338CA', 'text' => 'Đã xác nhận'],
                                    'delivering' => ['bg' => '#CFFAFE', 'color' => '#0E7490', 'text' => 'Đang giao hàng'],
                                    'completed' => ['bg' => '#D1FAE5', 'color' => '#047857', 'text' => 'Giao thành công'],
                                    'cancelled' => ['bg' => '#FEE2E2', 'color' => '#B91C1C', 'text' => 'Đã hủy'],
                                ];
                                $st = $statusColors[$order->status->value] ?? ['bg' => '#F1F5F9', 'color' => '#475569', 'text' => $order->status->value];
                            @endphp
                            <span style="background: {{ $st['bg'] }}; color: {{ $st['color'] }}; padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-circle" style="font-size: 7px;"></i> {{ $st['text'] }}
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 14px;">
                        @foreach($order->items as $item)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #F1F5F9; font-size: 14px;">
                                <div>
                                    <span style="font-weight: 700; color: var(--dark);">{{ $item->food_name }}</span>
                                    <span style="color: #64748B; margin-left: 8px;">x {{ $item->quantity }}</span>
                                    @if($item->note)
                                        <div style="font-size: 12px; color: #94A3B8; font-style: italic;">Ghi chú: {{ $item->note }}</div>
                                    @endif
                                </div>
                                <div style="font-weight: 700; color: var(--dark);">
                                    {{ number_format($item->line_total, 0, ',', '.') }} ₫
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="order-card-footer" style="display: flex; align-items: center; justify-content: space-between; padding-top: 12px; flex-wrap: wrap; gap: 12px;">
                        <div style="font-size: 13px; color: #64748B;">
                            Giao tới: <strong style="color: var(--dark);">{{ $order->customer_name }}</strong> ({{ $order->customer_phone }}) - {{ $order->delivery_address }}
                            @if($order->delivery_zone_name)<span style="display:block;color:#2563EB;margin-top:3px;">Khu vực: {{ $order->delivery_zone_name }}</span>@endif
                        </div>

                        <div class="order-card-pay-action" style="display: flex; align-items: center; gap: 14px;">
                            <div>
                                <div style="font-size: 12px; color: #64748B;">Tổng thanh toán (COD):</div>
                                <div style="font-size: 18px; font-weight: 800; color: var(--primary);">{{ number_format($order->total_price, 0, ',', '.') }} ₫</div>
                            </div>

                            @if($order->status->value === 'pending')
                                <button type="button" data-cancel-order onclick="cancelOrder({{ $order->id }}, this)" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 8px 16px; border-radius: 50px; font-size: 13px; font-weight: 700; cursor: pointer; transition: var(--transition);">
                                    <i class="fa-solid fa-xmark"></i> Hủy đơn
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <div style="margin-top: 20px;">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        <div style="text-align: center; background: white; padding: 60px 20px; border-radius: var(--radius-lg); border: 1px dashed var(--border-color);">
            <i class="fa-solid fa-bag-shopping" style="font-size: 50px; color: #CBD5E1; margin-bottom: 16px;"></i>
            <h3 style="font-size: 18px; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Bạn chưa có đơn hàng nào</h3>
            <p style="color: #64748B; font-size: 14px; margin-bottom: 20px;">Hãy thưởng thức các món ăn tươi ngon chuẩn vị của nhà hàng ngay hôm nay!</p>
            <a href="{{ route('home') }}" class="add-to-cart-btn" style="display: inline-flex; padding: 12px 24px; text-decoration: none;">
                <i class="fa-solid fa-utensils"></i> Xem thực đơn ngay
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
@include('partials.cancel-order-script')
@endsection
