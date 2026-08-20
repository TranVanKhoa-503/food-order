@extends('layouts.app')

@section('title', 'FoodOrder - Thực Đơn Món Ăn Tươi Ngon Chuẩn Vị')

@section('styles')
<style>
    /* Hero Banner */
    .hero-section {
        background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
        color: white;
        border-radius: var(--radius-lg);
        padding: 48px 40px;
        margin: 28px 0 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2);
    }

    .hero-pattern {
        position: absolute;
        top: -50px;
        right: -50px;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255, 87, 34, 0.25) 0%, rgba(255, 152, 0, 0) 70%);
        border-radius: 50%;
        filter: blur(40px);
        pointer-events: none;
    }

    .hero-content {
        max-width: 640px;
        position: relative;
        z-index: 2;
    }

    .hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 87, 34, 0.2);
        color: #FF8A65;
        border: 1px solid rgba(255, 87, 34, 0.3);
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 16px;
        letter-spacing: 0.5px;
    }

    .hero-title {
        font-size: 40px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 16px;
        letter-spacing: -1px;
    }

    .hero-title span {
        color: var(--primary);
        background: linear-gradient(135deg, #FF5722, #FFA726);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-desc {
        font-size: 16px;
        color: #CBD5E1;
        margin-bottom: 28px;
        line-height: 1.6;
    }

    .hero-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .hero-badge-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #E2E8F0;
    }

    .hero-badge-item i {
        color: var(--accent);
        font-size: 16px;
    }

    /* Section Title */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .section-title {
        font-size: 26px;
        font-weight: 800;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: var(--primary);
    }

    /* Category Filter Pills */
    .categories-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 12px;
        margin-bottom: 32px;
        scrollbar-width: none;
    }

    .categories-bar::-webkit-scrollbar {
        display: none;
    }

    .category-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 22px;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 50px;
        text-decoration: none;
        color: var(--dark-muted);
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
    }

    .category-pill:hover {
        border-color: var(--primary);
        color: var(--primary);
        transform: translateY(-2px);
    }

    .category-pill.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 4px 14px rgba(255, 87, 34, 0.35);
    }

    .category-pill .count {
        background: rgba(0, 0, 0, 0.08);
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 12px;
    }

    .category-pill.active .count {
        background: rgba(255, 255, 255, 0.25);
        color: white;
    }

    /* Food Grid */
    .food-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 60px;
    }

    /* Food Card */
    .food-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .food-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: #FFCCBC;
    }

    .food-img-wrapper {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
        background: #F1F5F9;
    }

    .food-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .food-card:hover .food-img {
        transform: scale(1.06);
    }

    .food-category-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(8px);
        color: white;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .food-rating-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        background: rgba(255, 255, 255, 0.95);
        color: var(--dark);
        padding: 4px 8px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .food-rating-badge i {
        color: #F59E0B;
        font-size: 11px;
    }

    .food-content {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .food-name {
        font-size: 17px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 8px;
        line-height: 1.35;
    }

    .food-desc {
        font-size: 13px;
        color: #64748B;
        margin-bottom: 18px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }

    .food-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 14px;
        border-top: 1px dashed var(--border-color);
    }

    .food-price {
        font-size: 19px;
        font-weight: 800;
        color: var(--primary);
    }

    .add-to-cart-btn {
        background: var(--primary-light);
        color: var(--primary);
        border: none;
        padding: 10px 16px;
        border-radius: var(--radius-md);
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
    }

    .add-to-cart-btn:hover {
        background: var(--primary);
        color: white;
        transform: scale(1.03);
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: var(--radius-lg);
        border: 1px dashed var(--border-color);
        margin-bottom: 60px;
    }

    .empty-state i {
        font-size: 60px;
        color: #CBD5E1;
        margin-bottom: 16px;
    }

    .empty-state h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #64748B;
        font-size: 14px;
        margin-bottom: 18px;
    }

    /* Features Section */
    .features-section {
        background: white;
        border-radius: var(--radius-lg);
        padding: 40px;
        border: 1px solid var(--border-color);
        margin-bottom: 40px;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 30px;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .feature-icon {
        width: 50px;
        height: 50px;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .feature-text h4 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .feature-text p {
        font-size: 13px;
        color: #64748B;
        line-height: 1.4;
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 30px 20px;
        }
        .hero-title {
            font-size: 28px;
        }
    }
</style>
@endsection

@section('content')
<div class="container">

    <!-- Hero Banner -->
    <section class="hero-section">
        <div class="hero-pattern"></div>
        <div class="hero-content">
            <div class="hero-tag">
                <i class="fa-solid fa-fire"></i> GIAO NHANH 15-30 PHÚT
            </div>
            <h1 class="hero-title">
                Thưởng Thức Ẩm Thực <span>Tươi Ngon Chuẩn Vị</span>
            </h1>
            <p class="hero-desc">
                Hơn 50+ món ăn truyền thống và hiện đại được chế biến từ nguyên liệu tươi sạch mỗi ngày. Đặt món dễ dàng, giao tận tay thơm phức nóng hổi!
            </p>
            <div class="hero-badges">
                <div class="hero-badge-item">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>Freeship mọi đơn hàng</span>
                </div>
                <div class="hero-badge-item">
                    <i class="fa-solid fa-star"></i>
                    <span>4.9/5 Đánh giá hài lòng</span>
                </div>
                <div class="hero-badge-item">
                    <i class="fa-solid fa-shield-heart"></i>
                    <span>100% Vệ sinh an toàn thực phẩm</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Filter Bar -->
    <div class="categories-bar">
        <a href="{{ route('home', array_filter(['search' => request('search')])) }}" 
           class="category-pill {{ !$categoryId ? 'active' : '' }}">
            <i class="fa-solid fa-layer-group"></i>
            <span>Tất cả món</span>
            <span class="count">{{ $categories->sum('foods_count') }}</span>
        </a>

        @foreach($categories as $cat)
            <a href="{{ route('home', array_filter(['category' => $cat->id, 'search' => request('search')])) }}" 
               class="category-pill {{ $categoryId == $cat->id ? 'active' : '' }}">
                <i class="fa-solid {{ $cat->icon ?? 'fa-utensils' }}"></i>
                <span>{{ $cat->name }}</span>
                <span class="count">{{ $cat->foods_count }}</span>
            </a>
        @endforeach
    </div>

    <!-- Section Header / Search Notification -->
    <div class="section-header">
        <h2 class="section-title">
            <i class="fa-solid fa-fire-burner"></i>
            @if($search)
                Kết quả tìm kiếm cho: "<span style="color: var(--primary);">{{ $search }}</span>"
            @elseif($categoryId)
                {{ $categories->firstWhere('id', $categoryId)->name ?? 'Thực đơn' }}
            @else
                Món Ngon Nổi Bật Hôm Nay
            @endif
        </h2>

        @if($search || $categoryId)
            <a href="{{ route('home') }}" style="color: #64748B; text-decoration: none; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-rotate-left"></i> Xem tất cả món
            </a>
        @endif
    </div>

    <!-- Food Items Grid -->
    @if($foods->count() > 0)
        <div class="food-grid">
            @foreach($foods as $food)
                <div class="food-card">
                    <div class="food-img-wrapper">
                        <img src="{{ $food->image }}" 
                             alt="{{ $food->name }}" 
                             class="food-img"
                             loading="lazy"
                             onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80'">
                        
                        @if($food->category)
                            <div class="food-category-badge">{{ $food->category->name }}</div>
                        @endif

                        <div class="food-rating-badge">
                            <i class="fa-solid fa-star"></i> 4.9
                        </div>
                    </div>

                    <div class="food-content">
                        <h3 class="food-name">{{ $food->name }}</h3>
                        <p class="food-desc">{{ $food->description }}</p>

                        <div class="food-footer">
                            <div class="food-price">
                                {{ number_format($food->price, 0, ',', '.') }} ₫
                            </div>

                            <button class="add-to-cart-btn" 
                                    onclick="addToCart({{ $food->id }}, '{{ addslashes($food->name) }}', {{ $food->price }}, '{{ addslashes($food->image) }}')">
                                <i class="fa-solid fa-plus"></i> Thêm món
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fa-solid fa-magnifying-glass"></i>
            <h3>Không tìm thấy món ăn phù hợp</h3>
            <p>Rất tiếc, chúng tôi không tìm thấy món nào với từ khóa hoặc danh mục đã chọn.</p>
            <a href="{{ route('home') }}" class="add-to-cart-btn" style="display: inline-flex; padding: 12px 24px;">
                <i class="fa-solid fa-utensils"></i> Quay lại thực đơn chính
            </a>
        </div>
    @endif

    <!-- Why Choose Us Features Section -->
    <section class="features-section">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div class="feature-text">
                    <h4>Giao Hàng Siêu Tốc</h4>
                    <p>Đội ngũ shipper phục vụ giao nhanh chỉ trong 15-30 phút.</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-carrot"></i>
                </div>
                <div class="feature-text">
                    <h4>Nguyên Liệu Tươi Mới</h4>
                    <p>Thực phẩm chọn lọc kỹ càng, chuẩn vệ sinh an toàn 100%.</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-tag"></i>
                </div>
                <div class="feature-text">
                    <h4>Giá Cả Cạnh Tranh</h4>
                    <p>Nhiều ưu đãi giảm giá và chương trình freeship mỗi ngày.</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div class="feature-text">
                    <h4>Hỗ Trợ 24/7</h4>
                    <p>Hotline 1900 8888 sẵn sàng tư vấn và giải đáp tận tình.</p>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
