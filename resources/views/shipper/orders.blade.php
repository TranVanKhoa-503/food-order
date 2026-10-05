@extends('layouts.app')

@section('title', 'Cổng Giao Hàng - Shipper Portal - FoodOrder')

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
        background: #0284C7;
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

    .tab-badge {
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 50px;
        font-weight: 800;
    }

    .tab-badge.badge-delivering {
        background: #0284C7;
        color: white;
    }

    .tab-badge.badge-success {
        background: #10B981;
        color: white;
    }

    .tab-badge.badge-danger {
        background: #EF4444;
        color: white;
    }

    .order-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        padding: 20px;
        margin-bottom: 18px;
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .order-card:hover {
        box-shadow: var(--shadow-md);
    }

    .order-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .customer-info-box {
        background: #F8FAFC;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 16px;
        margin-bottom: 16px;
    }

    .call-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #10B981;
        color: white;
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 800;
        transition: var(--transition);
        margin-top: 8px;
    }

    .call-btn:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    .cod-banner {
        background: #FEF3C7;
        border: 1px solid #FCD34D;
        color: #92400E;
        padding: 12px 16px;
        border-radius: var(--radius-md);
        font-size: 15px;
        font-weight: 800;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .cod-banner.paid {
        background: #ECFDF5;
        border-color: #A7F3D0;
        color: #065F46;
    }

    .btn-action-complete {
        background: #10B981;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: var(--radius-md);
        font-weight: 800;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    .btn-action-complete:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    .btn-action-fail {
        background: #FEE2E2;
        color: #DC2626;
        border: 1px solid #FECACA;
        padding: 12px 20px;
        border-radius: var(--radius-md);
        font-weight: 800;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
    }

    .btn-action-fail:hover {
        background: #FCA5A5;
        color: #991B1B;
    }

    /* Modal styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-card {
        background: white;
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 500px;
        padding: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        animation: modalIn 0.2s ease-out;
    }

    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
@endsection

@section('content')
<div class="shipper-container">
    <!-- Header -->
    <div class="shipper-header">
        <div class="shipper-title-box">
            <h1>
                <i class="fa-solid fa-motorcycle" style="color: #38BDF8;"></i>
                Cổng Giao Hàng
                <span class="shipper-badge-tag">Shipper</span>
            </h1>
            <p style="color: #94A3B8; font-size: 14px; margin-top: 4px;">
                Nhân viên giao hàng: <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->phone ?? Auth::user()->email }})
            </p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('shipper.orders.index') }}" class="btn" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.2); font-size: 13px; font-weight: 700; border-radius: 50px; padding: 8px 16px;">
                <i class="fa-solid fa-rotate-right"></i> Làm mới
            </a>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #6D28D9; color: white; font-size: 13px; font-weight: 700; border-radius: 50px; padding: 8px 16px;">
                    <i class="fa-solid fa-chart-pie"></i> Về Quản Trị
                </a>
            @endif
        </div>
    </div>

    <!-- Flash message -->
    @if(session('success'))
        <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #10B981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px; color: #EF4444;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Status Tabs -->
    <div class="shipper-tabs">
        <a href="{{ route('shipper.orders.index', ['status' => 'delivering']) }}" class="shipper-tab {{ ($status ?? '') === 'delivering' ? 'active' : '' }}">
            <i class="fa-solid fa-truck-fast"></i>
            Đang cần giao
            <span class="tab-badge badge-delivering">{{ $deliveringCount }}</span>
        </a>
        <a href="{{ route('shipper.orders.index', ['status' => 'completed_today']) }}" class="shipper-tab {{ ($status ?? '') === 'completed_today' ? 'active' : '' }}">
            <i class="fa-solid fa-circle-check"></i>
            Đã giao hôm nay
            <span class="tab-badge badge-success">{{ $completedTodayCount }}</span>
        </a>
        <a href="{{ route('shipper.orders.index', ['status' => 'cancelled_today']) }}" class="shipper-tab {{ ($status ?? '') === 'cancelled_today' ? 'active' : '' }}">
            <i class="fa-solid fa-circle-xmark"></i>
            Thất bại hôm nay
            <span class="tab-badge badge-danger">{{ $cancelledTodayCount }}</span>
        </a>
        <a href="{{ route('shipper.orders.index', ['status' => 'all']) }}" class="shipper-tab {{ ($status ?? '') === 'all' ? 'active' : '' }}">
            <i class="fa-solid fa-list-check"></i>
            Tất cả đơn
        </a>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('shipper.orders.index') }}" style="display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap;">
        <input type="hidden" name="status" value="{{ $status ?? 'delivering' }}">
        <div style="flex: 1; min-width: 260px; position: relative;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tìm mã đơn, tên khách, số điện thoại, địa chỉ..." style="width: 100%; padding: 12px 16px 12px 42px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px; outline: none; background: white;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 14px; color: #94A3B8;"></i>
        </div>
        <button type="submit" class="btn" style="background: var(--dark); color: white; padding: 12px 20px; font-weight: 700; border-radius: var(--radius-md); cursor: pointer;">
            <i class="fa-solid fa-filter"></i> Lọc
        </button>
        @if(!empty($search))
            <a href="{{ route('shipper.orders.index', ['status' => $status]) }}" class="btn" style="background: #F1F5F9; color: #64748B; padding: 12px 18px; font-weight: 700; border-radius: var(--radius-md); text-decoration: none; display: flex; align-items: center;">
                Xóa tìm kiếm
            </a>
        @endif
    </form>

    <!-- Orders List -->
    @if($orders->count() > 0)
        <div>
            @foreach($orders as $order)
                <div class="order-card" id="order-card-{{ $order->id }}">
                    <!-- Card Top -->
                    <div class="order-card-header">
                        <div>
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <span style="font-size: 18px; font-weight: 800; color: var(--dark); letter-spacing: 0.5px;">
                                    #{{ $order->order_code }}
                                </span>
                                @php
                                    $st = match($order->status->value ?? $order->status) {
                                        'delivering' => ['bg' => '#E0F2FE', 'color' => '#0284C7', 'text' => 'Đang giao hàng', 'icon' => 'fa-truck-fast'],
                                        'completed' => ['bg' => '#D1FAE5', 'color' => '#047857', 'text' => 'Giao thành công', 'icon' => 'fa-circle-check'],
                                        'cancelled' => ['bg' => '#FEE2E2', 'color' => '#B91C1C', 'text' => 'Giao thất bại / Hủy', 'icon' => 'fa-circle-xmark'],
                                        'confirmed' => ['bg' => '#EDE9FE', 'color' => '#6D28D9', 'text' => 'Đã xác nhận', 'icon' => 'fa-clipboard-check'],
                                        default => ['bg' => '#F1F5F9', 'color' => '#475569', 'text' => $order->status->value ?? $order->status, 'icon' => 'fa-clock'],
                                    };
                                @endphp
                                <span style="background: {{ $st['bg'] }}; color: {{ $st['color'] }}; font-size: 12px; font-weight: 800; padding: 4px 12px; border-radius: 50px; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid {{ $st['icon'] }}"></i> {{ $st['text'] }}
                                </span>
                            </div>
                            <div style="font-size: 13px; color: #64748B; margin-top: 4px;">
                                <i class="fa-regular fa-clock"></i> Đặt lúc: {{ $order->created_at->format('H:i d/m/Y') }}
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <div style="font-size: 12px; color: #64748B;">Tổng thanh toán:</div>
                            <div style="font-size: 20px; font-weight: 800; color: var(--primary);">
                                {{ number_format($order->total_price, 0, ',', '.') }} ₫
                            </div>
                        </div>
                    </div>

                    <!-- Customer & Address Information -->
                    <div class="customer-info-box">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                            <div>
                                <div style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">
                                    <i class="fa-solid fa-user" style="color: var(--primary); margin-right: 6px;"></i>
                                    {{ $order->customer_name }}
                                </div>
                                <div style="font-size: 14px; color: #334155; margin-bottom: 6px;">
                                    <i class="fa-solid fa-location-dot" style="color: #EF4444; margin-right: 6px;"></i>
                                    <strong>{{ $order->delivery_address }}</strong>
                                    @if($order->delivery_zone_name)
                                        <span style="display: inline-block; background: #DBEAFE; color: #1E40AF; padding: 1px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; margin-left: 6px;">
                                            {{ $order->delivery_zone_name }}
                                        </span>
                                    @endif
                                </div>
                                @if($order->note)
                                    <div style="background: #FFFBEB; border-left: 3px solid #F59E0B; padding: 6px 12px; border-radius: 4px; font-size: 13px; color: #92400E; margin-top: 6px;">
                                        <i class="fa-solid fa-message"></i> Ghi chú của khách: <em>{{ $order->note }}</em>
                                    </div>
                                @endif
                            </div>

                            @if($order->customer_phone)
                                <div>
                                    <a href="tel:{{ $order->customer_phone }}" class="call-btn">
                                        <i class="fa-solid fa-phone"></i> Gọi ngay: {{ $order->customer_phone }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- COD / Payment Collection Banner -->
                    @php
                        $isCod = ($order->payment_method->value ?? $order->payment_method) === 'cod';
                        $isPaid = ($order->payment_status->value ?? $order->payment_status) === 'paid';
                    @endphp
                    @if($isCod && !$isPaid)
                        <div class="cod-banner">
                            <span>
                                <i class="fa-solid fa-hand-holding-dollar" style="font-size: 18px; margin-right: 8px;"></i>
                                ĐƠN COD - CẦN THU TIỀN MẶT CỦA KHÁCH:
                            </span>
                            <span style="font-size: 18px;">
                                {{ number_format($order->total_price, 0, ',', '.') }} ₫
                            </span>
                        </div>
                    @else
                        <div class="cod-banner paid">
                            <span>
                                <i class="fa-solid fa-circle-check" style="font-size: 18px; margin-right: 8px;"></i>
                                ĐÃ THANH TOÁN (Không thu tiền mặt):
                            </span>
                            <span>0 ₫</span>
                        </div>
                    @endif

                    <!-- Items summary (Collapsible or compact list) -->
                    <div style="border-top: 1px dashed var(--border-color); padding-top: 12px; margin-bottom: 16px;">
                        <div style="font-size: 13px; font-weight: 700; color: #64748B; margin-bottom: 8px;">
                            CHI TIẾT MÓN ĂN ({{ $order->items->sum('quantity') }} phần):
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 6px;">
                            @foreach($order->items as $item)
                                <div style="display: flex; justify-content: space-between; font-size: 14px;">
                                    <div>
                                        <strong style="color: var(--dark);">{{ $item->food_name }}</strong>
                                        <span style="color: #64748B; margin-left: 6px;">x{{ $item->quantity }}</span>
                                    </div>
                                    <div style="font-weight: 700; color: #475569;">
                                        {{ number_format($item->line_total, 0, ',', '.') }} ₫
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Status History Info if Completed or Cancelled -->
                    @if(($order->status->value ?? $order->status) === 'completed')
                        <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: var(--radius-sm); padding: 10px 14px; font-size: 13px; color: #166534; margin-bottom: 14px;">
                            <i class="fa-solid fa-circle-check"></i>
                            Giao thành công vào lúc: <strong>{{ optional($order->completed_at)->format('H:i d/m/Y') }}</strong>
                        </div>
                    @elseif(($order->status->value ?? $order->status) === 'cancelled')
                        <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: var(--radius-sm); padding: 10px 14px; font-size: 13px; color: #991B1B; margin-bottom: 14px;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            Giao thất bại vào lúc: <strong>{{ optional($order->cancelled_at)->format('H:i d/m/Y') }}</strong>
                            <div style="margin-top: 4px;">Lý do: <em>{{ $order->cancel_reason ?: 'Không có ghi chú' }}</em></div>
                        </div>
                    @endif

                    <!-- Action Buttons for Delivering Orders -->
                    @if(($order->status->value ?? $order->status) === 'delivering')
                        <div style="display: flex; gap: 12px; justify-content: flex-end; flex-wrap: wrap; padding-top: 10px; border-top: 1px solid var(--border-color);">
                            <!-- Nút báo thất bại -->
                            <button type="button" onclick="openFailModal({{ $order->id }}, '{{ $order->order_code }}')" class="btn-action-fail">
                                <i class="fa-solid fa-xmark"></i> Báo giao thất bại
                            </button>

                            <!-- Form xác nhận hoàn thành -->
                            <form action="{{ route('shipper.orders.status', $order) }}" method="POST" onsubmit="return confirm('Xác nhận bạn đã giao thành công đơn hàng #{{ $order->order_code }} và thu đủ tiền (nếu có)?');" style="margin: 0;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn-action-complete">
                                    <i class="fa-solid fa-check"></i> Giao thành công
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Pagination -->
            <div style="margin-top: 24px;">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        <div style="background: white; border-radius: var(--radius-lg); border: 1px dashed var(--border-color); padding: 60px 20px; text-align: center;">
            <i class="fa-solid fa-box-open" style="font-size: 54px; color: #CBD5E1; margin-bottom: 16px;"></i>
            <h3 style="font-size: 18px; font-weight: 800; color: var(--dark); margin-bottom: 6px;">Không có đơn hàng nào</h3>
            <p style="color: #64748B; font-size: 14px;">
                @if(($status ?? '') === 'delivering')
                    Hiện tại chưa có đơn nào đang cần giao. Hãy kiểm tra lại sau!
                @else
                    Không tìm thấy đơn hàng nào phù hợp với bộ lọc hiện tại.
                @endif
            </p>
        </div>
    @endif
</div>

<!-- Modal Báo Giao Thất Bại -->
<div class="modal-overlay" id="failModal">
    <div class="modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 18px; font-weight: 800; color: #DC2626; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Báo Giao Thất Bại
            </h3>
            <button type="button" onclick="closeFailModal()" style="border: none; background: none; font-size: 20px; color: #94A3B8; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <p style="font-size: 14px; color: #475569; margin-bottom: 16px;">
            Đơn hàng: <strong id="modalOrderCode" style="color: var(--dark);"></strong>
        </p>

        <form id="failForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="cancelled">

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 8px;">
                    Chọn lý do nhanh:
                </label>
                <select id="quickReasonSelect" onchange="applyQuickReason()" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px; background: #F8FAFC;">
                    <option value="">-- Chọn lý do có sẵn --</option>
                    <option value="Khách không nghe máy (đã gọi 3 lần)">Khách không nghe máy (đã gọi 3 lần)</option>
                    <option value="Khách từ chối nhận món / bom hàng">Khách từ chối nhận món / bom hàng</option>
                    <option value="Sai số điện thoại hoặc địa chỉ nhận">Sai số điện thoại hoặc địa chỉ nhận</option>
                    <option value="Khách hẹn giao lại vào khung giờ khác">Khách hẹn giao lại vào khung giờ khác</option>
                    <option value="custom">Lý do khác...</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 8px;">
                    Chi tiết lý do thất bại <span style="color: #EF4444;">*</span>:
                </label>
                <textarea name="reason" id="modalReasonText" rows="3" required placeholder="Nhập lý do cụ thể không thể giao đơn hàng..." style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-family: inherit; font-size: 14px;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeFailModal()" class="btn" style="background: #F1F5F9; color: #475569; padding: 10px 18px; border-radius: var(--radius-md); font-weight: 700;">
                    Hủy bỏ
                </button>
                <button type="submit" class="btn" style="background: #DC2626; color: white; padding: 10px 20px; border-radius: var(--radius-md); font-weight: 800;">
                    Xác nhận hủy giao
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openFailModal(orderId, orderCode) {
        document.getElementById('modalOrderCode').innerText = '#' + orderCode;
        document.getElementById('failForm').action = '/shipper/orders/' + orderId + '/status';
        document.getElementById('quickReasonSelect').value = '';
        document.getElementById('modalReasonText').value = '';
        document.getElementById('failModal').classList.add('active');
    }

    function closeFailModal() {
        document.getElementById('failModal').classList.remove('active');
    }

    function applyQuickReason() {
        const select = document.getElementById('quickReasonSelect');
        const textarea = document.getElementById('modalReasonText');
        if (select.value && select.value !== 'custom') {
            textarea.value = select.value;
        } else if (select.value === 'custom') {
            textarea.value = '';
            textarea.focus();
        }
    }

    // Close modal on overlay click
    document.getElementById('failModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFailModal();
        }
    });
</script>
@endsection
