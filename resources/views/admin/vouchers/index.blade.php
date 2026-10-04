@extends('admin.layout')

@section('title', 'Quản Lý Voucher - FoodOrder Admin')
@section('header_title', 'Mã Giảm Giá')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
        <form method="GET" action="{{ route('admin.vouchers.index') }}" style="display:flex;gap:10px;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tìm mã voucher..." style="padding:8px 14px;border:1px solid var(--border-color);border-radius:var(--radius-md);font-size:14px;">
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-filter"></i> Lọc</button>
        </form>
        <button class="btn btn-primary" onclick="openVoucherModal()"><i class="fa-solid fa-plus"></i> Thêm voucher</button>
    </div>

    <table class="table">
        <thead><tr><th>MÃ</th><th>GIẢM</th><th>ĐƠN TỐI THIỂU</th><th>LƯỢT DÙNG</th><th>HIỆU LỰC</th><th>TRẠNG THÁI</th><th></th></tr></thead>
        <tbody>
        @forelse($vouchers as $voucher)
            <tr>
                <td><strong style="color:var(--primary);">{{ $voucher->code }}</strong><div style="font-size:12px;color:#64748B;">{{ $voucher->description }}</div></td>
                <td>{{ $voucher->discount_type === 'percent' ? $voucher->discount_value.'%' : number_format($voucher->discount_value, 0, ',', '.').' ₫' }}</td>
                <td>{{ number_format($voucher->min_order_value, 0, ',', '.') }} ₫</td>
                <td>{{ $voucher->used_count }} / {{ $voucher->usage_limit ?? '∞' }}</td>
                <td style="font-size:12px;">{{ $voucher->starts_at?->format('d/m/Y H:i') ?? 'Ngay' }}<br>{{ $voucher->ends_at?->format('d/m/Y H:i') ?? 'Không hạn' }}</td>
                <td><span class="badge" style="background:{{ $voucher->is_active ? '#D1FAE5' : '#FEE2E2' }};color:{{ $voucher->is_active ? '#047857' : '#B91C1C' }};">{{ $voucher->is_active ? 'Đang bật' : 'Đã tắt' }}</span></td>
                <td><button class="btn" style="background:#F1F5F9;color:#475569;padding:4px 10px;" onclick='openVoucherModal(@json($voucher))'>Sửa</button></td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#94A3B8;padding:30px;">Chưa có voucher nào.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:20px;">{{ $vouchers->links() }}</div>
</div>

<div id="voucherModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:var(--radius-lg);width:100%;max-width:560px;padding:24px;box-shadow:var(--shadow-lg);">
        <h3 id="voucherModalTitle" style="font-size:18px;font-weight:800;margin-bottom:16px;">Thêm voucher</h3>
        <form id="voucherForm" onsubmit="submitVoucher(event)">
            <input type="hidden" id="voucherId">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <label>Mã voucher<input id="voucherCode" required maxlength="50" style="width:100%;padding:9px;margin-top:5px;"></label>
                <label>Loại giảm<select id="voucherType" style="width:100%;padding:9px;margin-top:5px;"><option value="fixed">Số tiền</option><option value="percent">Phần trăm</option></select></label>
                <label>Giá trị giảm<input id="voucherValue" type="number" min="1" required style="width:100%;padding:9px;margin-top:5px;"></label>
                <label>Đơn tối thiểu<input id="voucherMin" type="number" min="0" value="0" style="width:100%;padding:9px;margin-top:5px;"></label>
                <label>Giảm tối đa<input id="voucherMax" type="number" min="0" style="width:100%;padding:9px;margin-top:5px;"></label>
                <label>Giới hạn lượt dùng<input id="voucherLimit" type="number" min="1" style="width:100%;padding:9px;margin-top:5px;"></label>
                <label>Bắt đầu<input id="voucherStarts" type="datetime-local" style="width:100%;padding:9px;margin-top:5px;"></label>
                <label>Kết thúc<input id="voucherEnds" type="datetime-local" style="width:100%;padding:9px;margin-top:5px;"></label>
            </div>
            <label style="display:block;margin-top:12px;">Mô tả<textarea id="voucherDescription" rows="2" style="width:100%;padding:9px;margin-top:5px;"></textarea></label>
            <label style="display:flex;gap:8px;align-items:center;margin-top:12px;"><input id="voucherActive" type="checkbox" checked> Đang hoạt động</label>
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;"><button type="button" class="btn" onclick="closeVoucherModal()">Hủy</button><button class="btn btn-primary" type="submit">Lưu voucher</button></div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toLocalDate(value) { return value ? new Date(value).toISOString().slice(0, 16) : ''; }
function openVoucherModal(voucher = null) {
    document.getElementById('voucherId').value = voucher?.id || '';
    document.getElementById('voucherCode').value = voucher?.code || '';
    document.getElementById('voucherType').value = voucher?.discount_type || 'fixed';
    document.getElementById('voucherValue').value = voucher?.discount_value || '';
    document.getElementById('voucherMin').value = voucher?.min_order_value || 0;
    document.getElementById('voucherMax').value = voucher?.max_discount_amount || '';
    document.getElementById('voucherLimit').value = voucher?.usage_limit || '';
    document.getElementById('voucherStarts').value = toLocalDate(voucher?.starts_at);
    document.getElementById('voucherEnds').value = toLocalDate(voucher?.ends_at);
    document.getElementById('voucherDescription').value = voucher?.description || '';
    document.getElementById('voucherActive').checked = voucher ? !!voucher.is_active : true;
    document.getElementById('voucherModalTitle').innerText = voucher ? 'Cập nhật voucher' : 'Thêm voucher';
    document.getElementById('voucherModal').style.display = 'flex';
}
function closeVoucherModal() { document.getElementById('voucherModal').style.display = 'none'; }
async function submitVoucher(event) {
    event.preventDefault();
    const id = document.getElementById('voucherId').value;
    const payload = {
        code: document.getElementById('voucherCode').value.trim(),
        discount_type: document.getElementById('voucherType').value,
        discount_value: Number(document.getElementById('voucherValue').value),
        min_order_value: Number(document.getElementById('voucherMin').value || 0),
        max_discount_amount: document.getElementById('voucherMax').value || null,
        usage_limit: document.getElementById('voucherLimit').value || null,
        starts_at: document.getElementById('voucherStarts').value || null,
        ends_at: document.getElementById('voucherEnds').value || null,
        description: document.getElementById('voucherDescription').value || null,
        is_active: document.getElementById('voucherActive').checked
    };
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const response = await fetch(id ? `/api/v1/admin/vouchers/${id}` : '/api/v1/admin/vouchers', {
        method: id ? 'PUT' : 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},
        body: JSON.stringify(payload)
    });
    if (response.ok) window.location.reload();
    else { const data = await response.json(); alert(data.message || 'Không thể lưu voucher.'); }
}
</script>
@endsection
