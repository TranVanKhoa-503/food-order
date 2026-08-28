@extends('admin.layout')

@section('title', 'Quản Lý Danh Mục - FoodOrder Admin')
@section('header_title', 'Danh Mục Món Ăn')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <form method="GET" action="{{ route('admin.categories.index') }}" style="display: flex; gap: 10px;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tìm tên danh mục..." style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px; width: 220px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Tìm</button>
            @if(!empty($search))
                <a href="{{ route('admin.categories.index') }}" class="btn" style="background: #F1F5F9; color: #64748B;">Xóa</a>
            @endif
        </form>

        <button onclick="openCategoryModal()" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm danh mục mới
        </button>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>TÊN DANH MỤC</th>
                <th>SLUG</th>
                <th>ICON</th>
                <th>SỐ MÓN ĂN</th>
                <th>HÀNH ĐỘNG</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
                <tr>
                    <td>#{{ $cat->id }}</td>
                    <td><strong>{{ $cat->name }}</strong></td>
                    <td style="color: #64748B;">{{ $cat->slug }}</td>
                    <td><i class="fa-solid {{ $cat->icon ?? 'fa-utensils' }}"></i> {{ $cat->icon }}</td>
                    <td><span class="badge" style="background: #EFF6FF; color: #2563EB;">{{ $cat->foods_count }} món</span></td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <button onclick='openEditCategoryModal(@json($cat))' class="btn" style="padding: 4px 10px; font-size: 12px; background: #F1F5F9; color: #475569;">
                                Sửa
                            </button>
                            @if($cat->foods_count == 0)
                                <button onclick="deleteCategory({{ $cat->id }})" class="btn" style="padding: 4px 10px; font-size: 12px; background: #FEE2E2; color: #B91C1C;">
                                    Xóa
                                </button>
                            @else
                                <span title="Không thể xóa danh mục đang có món ăn" style="font-size: 11px; color: #94A3B8; display: inline-flex; align-items: center;">
                                    <i class="fa-solid fa-lock"></i> Có món
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94A3B8; padding: 30px;">Chưa có danh mục nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $categories->links() }}
    </div>
</div>

<!-- Modal Create / Edit Category -->
<div id="categoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: var(--radius-lg); width: 100%; max-width: 450px; padding: 24px; box-shadow: var(--shadow-lg);">
        <h3 id="catModalTitle" style="font-size: 18px; font-weight: 800; margin-bottom: 16px;">Thêm Danh Mục Mới</h3>
        
        <form id="catForm" onsubmit="handleCategorySubmit(event)">
            <input type="hidden" id="catId">
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Tên danh mục:</label>
                <input type="text" id="catName" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Slug (tùy chọn):</label>
                <input type="text" id="catSlug" placeholder="Tự động tạo nếu để trống" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Icon FontAwesome:</label>
                <input type="text" id="catIcon" placeholder="fa-utensils, fa-mug-hot..." style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mô tả:</label>
                <textarea id="catDesc" rows="3" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md);"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeCategoryModal()" class="btn" style="background: #F1F5F9; color: #64748B;">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu danh mục</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openCategoryModal() {
        document.getElementById('catId').value = '';
        document.getElementById('catName').value = '';
        document.getElementById('catSlug').value = '';
        document.getElementById('catIcon').value = 'fa-utensils';
        document.getElementById('catDesc').value = '';
        document.getElementById('catModalTitle').innerText = 'Thêm Danh Mục Mới';
        document.getElementById('categoryModal').style.display = 'flex';
    }

    function openEditCategoryModal(cat) {
        document.getElementById('catId').value = cat.id;
        document.getElementById('catName').value = cat.name;
        document.getElementById('catSlug').value = cat.slug;
        document.getElementById('catIcon').value = cat.icon || '';
        document.getElementById('catDesc').value = cat.description || '';
        document.getElementById('catModalTitle').innerText = 'Cập Nhật Danh Mục';
        document.getElementById('categoryModal').style.display = 'flex';
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').style.display = 'none';
    }

    async function handleCategorySubmit(e) {
        e.preventDefault();
        const id = document.getElementById('catId').value;
        const payload = {
            name: document.getElementById('catName').value,
            slug: document.getElementById('catSlug').value || null,
            icon: document.getElementById('catIcon').value || null,
            description: document.getElementById('catDesc').value || null
        };

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const url = id ? `/api/v1/admin/categories/${id}` : '/api/v1/admin/categories';
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
            alert(data.message || 'Lỗi khi lưu danh mục!');
        }
    }

    async function deleteCategory(id) {
        if (!confirm('Bạn có chắc muốn xóa danh mục này?')) return;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch(`/api/v1/admin/categories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        });

        if (res.ok) {
            window.location.reload();
        } else {
            const data = await res.json();
            alert(data.message || 'Không thể xóa danh mục này!');
        }
    }
</script>
@endsection
