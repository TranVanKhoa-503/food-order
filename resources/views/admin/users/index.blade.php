@extends('admin.layout')

@section('title', 'Quản Lý Người Dùng - FoodOrder Admin')
@section('header_title', 'Danh Sách Người Dùng Hệ Thống')

@section('content')
<div class="card">
    <form method="GET" action="{{ route('admin.users.index') }}" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tìm tên, email, SĐT..." style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px; width: 220px;">
        <select name="role" style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
            <option value="">Tất cả vai trò</option>
            <option value="user" {{ ($role ?? '') === 'user' ? 'selected' : '' }}>User (Khách hàng)</option>
            <option value="admin" {{ ($role ?? '') === 'admin' ? 'selected' : '' }}>Admin (Quản trị)</option>
        </select>
        <select name="is_active" style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
            <option value="">Tất cả trạng thái</option>
            <option value="1" {{ ($isActive ?? '') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
            <option value="0" {{ ($isActive ?? '') === '0' ? 'selected' : '' }}>Bị khóa</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Lọc</button>
        @if(!empty($search) || !empty($role) || !is_null($isActive))
            <a href="{{ route('admin.users.index') }}" class="btn" style="background: #F1F5F9; color: #64748B;">Xóa lọc</a>
        @endif
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>HỌ VÀ TÊN</th>
                <th>EMAIL</th>
                <th>SỐ ĐIỆN THOẠI</th>
                <th>VAI TRÒ</th>
                <th>TRẠNG THÁI</th>
                <th>HÀNH ĐỘNG</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
                <tr>
                    <td>#{{ $u->id }}</td>
                    <td><strong>{{ $u->name }}</strong></td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->phone ?? '---' }}</td>
                    <td>
                        @if($u->isAdmin())
                            <span class="badge" style="background: #EDE9FE; color: #6D28D9;">ADMIN</span>
                        @else
                            <span class="badge" style="background: #F1F5F9; color: #475569;">USER</span>
                        @endif
                    </td>
                    <td>
                        @if($u->is_active)
                            <span class="badge" style="background: #D1FAE5; color: #047857;">Hoạt động</span>
                        @else
                            <span class="badge" style="background: #FEE2E2; color: #B91C1C;">Đã khóa</span>
                        @endif
                    </td>
                    <td>
                        @if($u->id !== Auth::id())
                            <button onclick="toggleUserStatus({{ $u->id }}, {{ $u->is_active ? 'false' : 'true' }})" class="btn" style="padding: 4px 10px; font-size: 12px; background: {{ $u->is_active ? '#FEE2E2' : '#D1FAE5' }}; color: {{ $u->is_active ? '#B91C1C' : '#047857' }};">
                                {{ $u->is_active ? 'Khóa tài khoản' : 'Mở khóa' }}
                            </button>
                        @else
                            <span style="font-size: 12px; color: #94A3B8; font-style: italic;">(Chính bạn)</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #94A3B8; padding: 30px;">Không tìm thấy người dùng nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $users->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function toggleUserStatus(userId, newStatus) {
        const actionText = newStatus ? 'mở khóa' : 'khóa';
        if (!confirm(`Bạn có chắc muốn ${actionText} tài khoản này?`)) return;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch(`/api/v1/admin/users/${userId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_active: newStatus })
        });

        if (res.ok) {
            window.location.reload();
        } else {
            const data = await res.json();
            alert(data.message || 'Không thể thay đổi trạng thái tài khoản!');
        }
    }
</script>
@endsection
