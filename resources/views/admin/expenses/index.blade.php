@extends('admin.layout')

@section('title', 'Quản Lý Chi Phí Vận Hành - FoodOrder')
@section('header_title', 'Quản Lý Chi Phí Vận Hành')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
            <i class="fa-solid fa-file-invoice-dollar" style="color: #BE185D; margin-right: 6px;"></i> Tổng Chi Phí Vận Hành
        </div>
        <div style="font-size: 24px; font-weight: 800; color: #BE185D; margin-top: 8px;">
            {{ number_format($totalExpense, 0, ',', '.') }} ₫
        </div>
        <div style="font-size: 12px; color: #94A3B8; margin-top: 4px;">{{ $expenses->total() }} khoản chi được ghi nhận</div>
    </div>

    @foreach($byCategory->take(3) as $catItem)
        <div class="card" style="margin-bottom: 0;">
            <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase;">
                {{ $categories[$catItem->category] ?? $catItem->category }}
            </div>
            <div style="font-size: 20px; font-weight: 800; color: #1E293B; margin-top: 8px;">
                {{ number_format($catItem->total_amount, 0, ',', '.') }} ₫
            </div>
            <div style="font-size: 11px; color: #94A3B8; margin-top: 4px;">{{ $catItem->count }} giao dịch</div>
        </div>
    @endforeach
</div>

<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <h3 style="font-size: 16px; font-weight: 800;">
            <i class="fa-solid fa-plus-circle" style="color: var(--primary); margin-right: 6px;"></i> Thêm Khoản Chi Phí Mới
        </h3>
    </div>

    <form method="POST" action="{{ route('admin.expenses.store') }}" data-prevent-double-submit style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
        @csrf
        <input type="hidden" name="submission_token" value="{{ old('submission_token', (string) \Illuminate\Support\Str::uuid()) }}">
        <div>
            <label style="display: block; font-size: 12px; color: #64748B; font-weight: 700; margin-bottom: 6px;">Danh mục chi phí *</label>
            <select name="category" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 12px; color: #64748B; font-weight: 700; margin-bottom: 6px;">Số tiền (VNĐ) *</label>
            <input type="number" name="amount" value="{{ old('amount') }}" required min="1000" step="1000" placeholder="Ví dụ: 500000" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
        </div>

        <div>
            <label style="display: block; font-size: 12px; color: #64748B; font-weight: 700; margin-bottom: 6px;">Ngày phát sinh chi phí *</label>
            <input type="date" name="expense_date" value="{{ old('expense_date', today()->toDateString()) }}" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
        </div>

        <div>
            <label style="display: block; font-size: 12px; color: #64748B; font-weight: 700; margin-bottom: 6px;">Ghi chú / Diễn giải chi tiết</label>
            <input type="text" name="description" value="{{ old('description') }}" placeholder="Ví dụ: Tiền điện nước tháng này, mua túi đựng..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 14px;">
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px 16px;">
                <i class="fa-solid fa-floppy-disk"></i> Lưu chi phí
            </button>
        </div>
    </form>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <form method="GET" action="{{ route('admin.expenses.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <select name="category" style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 13px;">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ ($category ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ $from ?? '' }}" title="Từ ngày" style="padding: 8px 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 13px;">
            <input type="date" name="to" value="{{ $to ?? '' }}" title="Đến ngày" style="padding: 8px 10px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 13px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Lọc</button>
            @if(!empty($category) || !empty($from) || !empty($to))
                <a href="{{ route('admin.expenses.index') }}" class="btn" style="background: #F1F5F9; color: #64748B;">Xóa lọc</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>NGÀY CHI</th>
                    <th>DANH MỤC</th>
                    <th>SỐ TIỀN</th>
                    <th>DIỄN GIẢI</th>
                    <th>NGƯỜI TẠO</th>
                    <th>HÀNH ĐỘNG</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>
                            <strong style="color: #1E293B;">{{ $expense->expense_date->format('d/m/Y') }}</strong>
                        </td>
                        <td>
                            <span class="badge" style="background: #FDF2F8; color: #BE185D;">
                                {{ $expense->category_label }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size: 15px; font-weight: 800; color: #DC2626;">
                                - {{ number_format($expense->amount, 0, ',', '.') }} ₫
                            </span>
                        </td>
                        <td>
                            <div style="font-size: 13px; color: #475569;">{{ $expense->description ?: 'Không có diễn giải' }}</div>
                        </td>
                        <td>
                            <span style="font-size: 12px; color: #64748B;">{{ $expense->creator?->name ?? 'Hệ thống' }}</span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" onsubmit="return confirm('Bạn có chắc muốn xóa khoản chi này?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="background: #FEF2F2; color: #DC2626; padding: 4px 8px; font-size: 11px;">
                                    <i class="fa-solid fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94A3B8; padding: 30px;">Chưa có khoản chi phí nào được ghi nhận.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $expenses->links() }}
    </div>
</div>
@endsection
