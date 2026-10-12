@extends('layouts.app')

@section('title', $food->name.' - FoodOrder')

@section('styles')
<style>
    .food-detail-wrapper {
        max-width: 1040px;
        margin: 0 auto;
        padding: 36px 20px 60px;
    }

    .back-nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #64748B;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 50px;
        background: white;
        border: 1px solid var(--border-color);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: var(--transition);
        margin-bottom: 22px;
    }

    .back-nav-btn:hover {
        color: var(--primary);
        border-color: var(--primary);
        transform: translateX(-4px);
        box-shadow: var(--shadow-sm);
    }

    .food-detail-card {
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        gap: 36px;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 32px;
        box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.07);
    }

    /* Media / Image */
    .food-media-container {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        background: #F8FAFC;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.08);
        height: 100%;
        min-height: 380px;
    }

    .food-media-img {
        width: 100%;
        height: 100%;
        min-height: 380px;
        object-fit: cover;
        display: block;
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .food-media-container:hover .food-media-img {
        transform: scale(1.05);
    }

    .food-badge-overlay {
        position: absolute;
        top: 16px;
        left: 16px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        color: var(--primary);
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Info */
    .food-info-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .food-category-pill {
        color: var(--primary);
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .food-name-title {
        font-size: 32px;
        font-weight: 800;
        color: var(--dark);
        line-height: 1.25;
        margin-bottom: 14px;
        letter-spacing: -0.5px;
    }

    .food-description-text {
        color: #64748B;
        font-size: 15px;
        line-height: 1.7;
        margin-bottom: 22px;
    }

    /* Price & status block */
    .food-price-badge-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        padding: 16px 22px;
        background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%);
        border: 1px solid #FED7AA;
        border-radius: 16px;
        margin-bottom: 24px;
    }

    .food-price-display {
        display: flex;
        flex-direction: column;
    }

    .food-price-caption {
        font-size: 12px;
        font-weight: 700;
        color: #C2410C;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    .food-price-number {
        font-size: 32px;
        font-weight: 800;
        color: var(--primary);
        letter-spacing: -0.5px;
        line-height: 1;
    }

    .food-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: white;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }

    .food-status-badge.available {
        color: #047857;
        border: 1px solid #A7F3D0;
    }

    .food-status-badge.unavailable {
        color: #B91C1C;
        border: 1px solid #FECACA;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10B981;
        display: inline-block;
        animation: pulseGreen 1.8s infinite;
    }

    @keyframes pulseGreen {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* Purchase Section */
    .food-purchase-panel {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 24px;
    }

    .action-row-controls {
        display: flex;
        gap: 14px;
        align-items: stretch;
    }

    /* Quantity Stepper */
    .qty-stepper-box {
        display: inline-flex;
        align-items: center;
        background: #F8FAFC;
        border: 1.5px solid var(--border-color);
        border-radius: 14px;
        padding: 4px;
        user-select: none;
        transition: var(--transition);
    }

    .qty-stepper-box:hover, .qty-stepper-box:focus-within {
        border-color: #CBD5E1;
        background: #F1F5F9;
    }

    .qty-stepper-btn {
        width: 44px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        color: var(--dark);
        font-size: 14px;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .qty-stepper-btn:hover:not(:disabled) {
        background: var(--primary-light);
        color: var(--primary);
        border-color: rgba(255, 87, 34, 0.3);
        transform: scale(1.06);
    }

    .qty-stepper-btn:active:not(:disabled) {
        transform: scale(0.94);
    }

    .qty-stepper-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: #F1F5F9;
    }

    .qty-stepper-input {
        width: 46px;
        text-align: center;
        font-size: 17px;
        font-weight: 800;
        color: var(--dark);
        border: none;
        background: transparent;
        outline: none;
        font-family: inherit;
    }

    /* Add to Cart Button (The Star!) */
    .btn-detail-add-cart {
        flex: 1;
        background: linear-gradient(135deg, #FF5722 0%, #FF7043 100%);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 12px 20px;
        font-size: 15px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 10px 24px -4px rgba(255, 87, 34, 0.45);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }

    .btn-detail-add-cart::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
        transition: 0.6s;
    }

    .btn-detail-add-cart:hover::before {
        left: 100%;
    }

    .btn-detail-add-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px -4px rgba(255, 87, 34, 0.55);
        background: linear-gradient(135deg, #F4511E 0%, #FF5722 100%);
    }

    .btn-detail-add-cart:active {
        transform: translateY(1px) scale(0.99);
        box-shadow: 0 4px 12px rgba(255, 87, 34, 0.35);
    }

    .btn-cart-main-text {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-cart-icon-wrapper {
        width: 34px;
        height: 34px;
        background: rgba(255, 255, 255, 0.22);
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        transition: transform 0.25s ease;
    }

    .btn-detail-add-cart:hover .btn-cart-icon-wrapper {
        transform: scale(1.12) rotate(-8deg);
    }

    .btn-cart-calc-subtotal {
        background: rgba(0, 0, 0, 0.16);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    /* Added feedback state */
    .btn-detail-add-cart.added {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
        box-shadow: 0 10px 24px -4px rgba(16, 185, 129, 0.45) !important;
    }

    /* Buy Now Button */
    .btn-detail-buy-now {
        width: 100%;
        background: white;
        color: var(--primary);
        border: 2px solid var(--primary);
        border-radius: 14px;
        padding: 12px 20px;
        font-size: 15px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: var(--transition);
    }

    .btn-detail-buy-now:hover {
        background: var(--primary-light);
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(255, 87, 34, 0.18);
    }

    .btn-detail-buy-now:active {
        transform: scale(0.99);
    }

    /* Service Highlights */
    .food-perks-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        padding-top: 20px;
        border-top: 1px dashed var(--border-color);
    }

    .food-perk-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #475569;
        font-weight: 600;
    }

    .food-perk-item i {
        color: var(--primary);
        font-size: 15px;
    }

    @media (max-width: 860px) {
        .food-detail-card {
            grid-template-columns: 1fr;
            gap: 28px;
            padding: 20px;
        }

        .food-media-container, .food-media-img {
            min-height: 280px;
            height: 280px;
        }

        .action-row-controls {
            flex-direction: column;
        }

        .qty-stepper-box {
            width: 100%;
            justify-content: space-between;
        }

        .food-perks-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }
    }
</style>
@endsection

@section('content')
<div class="food-detail-wrapper">
    <!-- Back to menu -->
    <a href="{{ route('home') }}" class="back-nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Quay lại thực đơn</span>
    </a>

    <!-- Main Card -->
    <div class="food-detail-card">
        <!-- Left: Image -->
        <div class="food-media-container">
            <img src="{{ $food->image }}"
                 alt="{{ $food->name }}"
                 class="food-media-img"
                 onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80'">
            @if($food->category)
                <div class="food-badge-overlay">
                    <i class="fa-solid fa-utensils"></i> {{ $food->category->name }}
                </div>
            @endif
        </div>

        <!-- Right: Food Info & Action -->
        <div class="food-info-container">
            @if($food->category)
                <span class="food-category-pill">
                    <i class="fa-solid fa-tag"></i> {{ $food->category->name }}
                </span>
            @endif

            <h1 class="food-name-title">{{ $food->name }}</h1>

            <p class="food-description-text">
                {{ $food->description ?: 'Món ăn thơm ngon chuẩn vị được chế biến từ những nguyên liệu tươi mới, chọn lọc kỹ lưỡng mỗi ngày.' }}
            </p>

            <!-- Price & Status -->
            <div class="food-price-badge-bar">
                <div class="food-price-display">
                    <span class="food-price-caption">Giá bán</span>
                    <span class="food-price-number">{{ number_format($food->price, 0, ',', '.') }} ₫</span>
                </div>

                @if($food->is_available ?? true)
                    <div class="food-status-badge available">
                        <span class="pulse-dot"></span> Đang phục vụ
                    </div>
                @else
                    <div class="food-status-badge unavailable">
                        <i class="fa-solid fa-circle-xmark"></i> Tạm ngưng phục vụ
                    </div>
                @endif
            </div>

            <!-- Purchase Actions -->
            @if($food->is_available ?? true)
                <div class="food-purchase-panel">
                    <div class="action-row-controls">
                        <!-- Quantity Stepper -->
                        <div class="qty-stepper-box">
                            <button type="button" class="qty-stepper-btn" id="detailQtyMinus" onclick="adjustDetailQty(-1)" aria-label="Giảm số lượng" disabled>
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <input type="number" id="detailQtyInput" class="qty-stepper-input" value="1" min="1" max="99" readonly>
                            <button type="button" class="qty-stepper-btn" id="detailQtyPlus" onclick="adjustDetailQty(1)" aria-label="Tăng số lượng">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>

                        <!-- Primary Add to Cart Button -->
                        <button type="button"
                                class="btn-detail-add-cart add-to-cart-btn"
                                id="detailAddToCartBtn"
                                onclick="handleAddDetailToCart()">
                            <div class="btn-cart-main-text">
                                <div class="btn-cart-icon-wrapper" id="detailBtnIconWrapper">
                                    <i class="fa-solid fa-basket-shopping" id="detailBtnIcon"></i>
                                </div>
                                <span id="detailBtnLabel">Thêm vào giỏ hàng</span>
                            </div>
                            <span class="btn-cart-calc-subtotal" id="detailSubtotalDisplay">
                                {{ number_format($food->price, 0, ',', '.') }} ₫
                            </span>
                        </button>
                    </div>

                    <!-- Secondary Buy Now Button -->
                    <button type="button" class="btn-detail-buy-now" onclick="handleBuyNow()">
                        <i class="fa-solid fa-bolt"></i> Mua ngay (Thanh toán nhanh)
                    </button>
                </div>
            @endif

            <!-- Value Highlights -->
            <div class="food-perks-grid">
                <div class="food-perk-item">
                    <i class="fa-solid fa-bolt-lightning"></i>
                    <span>Giao nóng 15-30p</span>
                </div>
                <div class="food-perk-item">
                    <i class="fa-solid fa-shield-heart"></i>
                    <span>Chuẩn sạch ATTP</span>
                </div>
                <div class="food-perk-item">
                    <i class="fa-solid fa-leaf"></i>
                    <span>Tươi mới mỗi ngày</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const foodUnitPrice = {{ (float) $food->price }};
    let detailQuantity = 1;

    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
    }

    function updateDetailPriceUI() {
        const subtotal = foodUnitPrice * detailQuantity;
        const subtotalEl = document.getElementById('detailSubtotalDisplay');
        const inputEl = document.getElementById('detailQtyInput');
        const minusBtn = document.getElementById('detailQtyMinus');

        if (subtotalEl) subtotalEl.textContent = formatVND(subtotal);
        if (inputEl) inputEl.value = detailQuantity;
        if (minusBtn) minusBtn.disabled = detailQuantity <= 1;
    }

    function adjustDetailQty(delta) {
        const next = detailQuantity + delta;
        if (next >= 1 && next <= 99) {
            detailQuantity = next;
            updateDetailPriceUI();
        }
    }

    function handleAddDetailToCart() {
        const foodId = {{ $food->id }};
        const foodName = {{ Js::from($food->name) }};
        const foodPrice = {{ $food->price }};
        const foodImage = {{ Js::from($food->image) }};

        addToCart(foodId, foodName, foodPrice, foodImage, detailQuantity);

        const btn = document.getElementById('detailAddToCartBtn');
        const label = document.getElementById('detailBtnLabel');
        const icon = document.getElementById('detailBtnIcon');

        if (btn && label && icon) {
            btn.classList.add('added');
            const originalText = label.textContent;
            label.textContent = 'Đã thêm vào giỏ!';
            icon.className = 'fa-solid fa-check';

            setTimeout(() => {
                btn.classList.remove('added');
                label.textContent = originalText;
                icon.className = 'fa-solid fa-basket-shopping';
            }, 1400);
        }
    }

    function handleBuyNow() {
        const foodId = {{ $food->id }};
        const foodName = {{ Js::from($food->name) }};
        const foodPrice = {{ $food->price }};
        const foodImage = {{ Js::from($food->image) }};

        addToCart(foodId, foodName, foodPrice, foodImage, detailQuantity);

        if (typeof toggleCart === 'function') {
            toggleCart(true);
        }
    }
</script>
@endsection
