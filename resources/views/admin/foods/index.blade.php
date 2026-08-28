@extends('admin.layout')

@section('title', 'Quản Lý Món Ăn - FoodOrder Admin')
@section('header_title', 'Danh Sách Món Ăn Trong Thực Đơn')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <form method="GET" action="{{ route('admin.foods.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tìm kiếm món ăn..." style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px; width: 220px;">
            <select name="category_id" style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Lọc</button>
            @if(!empty($search) || !empty($categoryId))
                <a href="{{ route('admin.foods.index') }}" class="btn" style="background: #F1F5F9; color: #64748B;">Xóa lọc</a>
            @endif
        </form>

        <button onclick="openFoodModal()" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm món mới
        </button>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>HÌNH ẢNH</th>
                <th>TÊN MÓN</th>
                <th>DANH MỤC</th>
                <th>GIÁ BÁN</th>
                <th>TRẠNG THÁI</th>
                <th>HÀNH ĐỘNG</th>
            </tr>
        </thead>
        <tbody>
            @forelse($foods as $food)
                <tr>
                    <td style="width: 70px;">
                        <img src="{{ $food->image }}" alt="{{ $food->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=150&q=80'">
                    </td>
                    <td>
                        <strong style="font-size: 15px;">{{ $food->name }}</strong>
                        <div style="font-size: 12px; color: #64748B; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $food->description }}</div>
                    </td>
                    <td>
                        <span class="badge" style="background: #F1F5F9; color: #475569;">{{ $food->category?->name ?? 'N/A' }}</span>
                    </td>
                    <td style="font-weight: 800; color: var(--primary);">{{ number_format($food->price, 0, ',', '.') }} ₫</td>
                    <td>
                        @if($food->is_available)
                            <span class="badge" style="background: #D1FAE5; color: #047857;">Đang bán</span>
                        @else
                            <span class="badge" style="background: #FEE2E2; color: #B91C1C;">Tạm hết / Ẩn</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <button onclick="toggleFoodAvailability({{ $food->id }}, {{ $food->is_available ? 'false' : 'true' }})" class="btn" style="padding: 4px 10px; font-size: 12px; background: {{ $food->is_available ? '#FEF3C7' : '#D1FAE5' }}; color: {{ $food->is_available ? '#D97706' : '#047857' }};">
                                {{ $food->is_available ? 'Tạm dừng' : 'Mở bán' }}
                            </button>
                            <button onclick='openEditFoodModal(@json($food))' class="btn" style="padding: 4px 10px; font-size: 12px; background: #F1F5F9; color: #475569;">
                                Sửa
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94A3B8; padding: 30px;">Chưa có món ăn nào trong danh sách.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $foods->links() }}
    </div>
</div>

<!-- Modal Create / Edit Food -->
<div id="foodModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: var(--radius-lg); width: 100%; max-width: 500px; padding: 24px; box-shadow: var(--shadow-lg);">
        <h3 id="foodModalTitle" style="font-size: 18px; font-weight: 800; margin-bottom: 16px;">Thêm Món Ăn Mới</h3>
        
        <form id="foodForm" onsubmit="handleFoodSubmit(event)">
            <input type="hidden" id="foodId">
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Danh mục:</label>
                <select id="foodCategory" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Tên món ăn:</label>
                <input type="text" id="foodName" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Giá bán (VNĐ):</label>
                <input type="number" id="foodPrice" required min="0" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Link hình ảnh:</label>
                <input type="text" id="foodImage" placeholder="https://..." style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mô tả món ăn:</label>
                <textarea id="foodDesc" rows="3" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeFoodModal()" class="btn" style="background: #F1F5F9; color: #64748B;">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu món ăn</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openFoodModal() {
        document.getElementById('foodId').value = '';
        document.getElementById('foodName').value = '';
        document.getElementById('foodPrice').value = '';
        document.getElementById('foodImage').value = '';
        document.getElementById('foodDesc').value = '';
        document.getElementById('foodModalTitle').innerText = 'Thêm Món Ăn Mới';
        document.getElementById('foodModal').style.display = 'flex';
    }

    function openEditFoodModal(food) {
        document.getElementById('foodId').value = food.id;
        document.getElementById('foodCategory').value = food.category_id;
        document.getElementById('foodName').value = food.name;
        document.getElementById('foodPrice').value = food.price;
        document.getElementById('foodImage').value = food.image || '';
        document.getElementById('foodDesc').value = food.description || '';
        document.getElementById('foodModalTitle').innerText = 'Cập Nhật Món Ăn';
        document.getElementById('foodModal').style.display = 'flex';
    }

    function closeFoodModal() {
        document.getElementById('foodModal').style.display = 'none';
    }

    async function handleFoodSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('foodId').value;
        const payload = {
            category_id: parseInt(document.getElementById('foodCategory').value),
            name: document.getElementById('foodName').value,
            price: parseFloat(document.getElementById('foodPrice').value),
            image: document.getElementById('foodImage').value || null,
            description: document.getElementById('foodDesc').value || null,
            is_available: true
        };

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const url = id ? `/api/v1/admin/foods/${id}` : '/api/v1/admin/foods';
        const method = id ? 'PUT' : 'POST';

        const res = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (res.ok) {
            window.location.reload();
        } else {
            const data = await res.json();
            alert(data.message || 'Có lỗi xảy ra khi lưu món ăn!');
        }
    }

    async function toggleFoodAvailability(foodId, newStatus) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch(`/api/v1/admin/foods/${foodId}/availability`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_available: newStatus })
        });

        if (res.ok) {
            window.location.reload();
        } else {
            alert('Lỗi cập nhật trạng thái món ăn!');
        }
    }
</script>
@endsection
