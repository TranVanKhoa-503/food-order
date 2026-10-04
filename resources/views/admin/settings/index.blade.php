@extends('admin.layout')

@section('title', 'Cấu Hình Cửa Hàng - FoodOrder Admin')
@section('header_title', 'Cấu Hình Cửa Hàng')

@section('content')
<div class="card" style="max-width:720px;">
    <h2 style="font-size:18px;font-weight:800;margin-bottom:20px;">Quy tắc nhận đơn</h2>
    <form method="POST" action="{{ url('/api/v1/admin/settings') }}" id="storeSettingForm">
        @csrf
        @method('PUT')
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <label>Tên cửa hàng<input name="store_name" value="{{ $setting->store_name }}" required style="width:100%;padding:10px;margin-top:6px;"></label>
            <label style="display:flex;align-items:center;gap:8px;margin-top:28px;"><input type="checkbox" name="is_open" value="1" {{ $setting->is_open ? 'checked' : '' }}> Đang nhận đơn</label>
            <label>Giờ mở cửa<input name="opens_at" type="time" value="{{ $setting->opens_at }}" required style="width:100%;padding:10px;margin-top:6px;"></label>
            <label>Giờ đóng cửa<input name="closes_at" type="time" value="{{ $setting->closes_at }}" required style="width:100%;padding:10px;margin-top:6px;"></label>
            <label>Đơn tối thiểu (₫)<input name="min_order_value" type="number" min="0" value="{{ (int) $setting->min_order_value }}" required style="width:100%;padding:10px;margin-top:6px;"></label>
            <label>Phí giao hàng (₫)<input name="shipping_fee" type="number" min="0" value="{{ (int) $setting->shipping_fee }}" required style="width:100%;padding:10px;margin-top:6px;"></label>
            <label>Thời gian giao dự kiến (phút)<input name="estimated_delivery_minutes" type="number" min="1" max="240" value="{{ $setting->estimated_delivery_minutes }}" required style="width:100%;padding:10px;margin-top:6px;"></label>
        </div>
        <button class="btn btn-primary" type="submit" style="margin-top:20px;"><i class="fa-solid fa-save"></i> Lưu cấu hình</button>
    </form>
</div>

<div class="card" style="max-width:720px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:18px;">
        <div>
            <h2 style="font-size:18px;font-weight:800;margin-bottom:4px;">Khu vực giao hàng</h2>
            <p style="font-size:12px;color:#64748B;">Khách sẽ chọn khu vực này khi thanh toán; phí được tính từ server.</p>
        </div>
        <button type="button" class="btn" style="background:#F1F5F9;color:#475569;" onclick="resetDeliveryZoneForm()">Thêm khu vực</button>
    </div>

    <form id="deliveryZoneForm" style="display:grid;grid-template-columns:1fr 150px auto auto;gap:10px;align-items:end;margin-bottom:18px;">
        <input type="hidden" id="deliveryZoneId">
        <label style="font-size:12px;font-weight:700;color:#475569;">Tên khu vực
            <input id="deliveryZoneName" required placeholder="Ví dụ: Quận 1" style="width:100%;padding:9px 10px;margin-top:6px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
        </label>
        <label style="font-size:12px;font-weight:700;color:#475569;">Phí giao (₫)
            <input id="deliveryZoneFee" required type="number" min="0" value="0" style="width:100%;padding:9px 10px;margin-top:6px;border:1px solid var(--border-color);border-radius:var(--radius-md);">
        </label>
        <label style="font-size:12px;font-weight:700;color:#475569;display:flex;align-items:center;gap:6px;height:38px;">
            <input id="deliveryZoneActive" type="checkbox" checked> Hoạt động
        </label>
        <button class="btn btn-primary" type="submit">Lưu</button>
    </form>

    <div style="overflow-x:auto;">
        <table class="table">
            <thead><tr><th>KHU VỰC</th><th>PHÍ GIAO</th><th>TRẠNG THÁI</th><th></th></tr></thead>
            <tbody>
                @forelse($zones as $zone)
                    <tr>
                        <td style="font-weight:700;">{{ $zone->name }}</td>
                        <td style="color:var(--primary);font-weight:700;">{{ $zone->fee > 0 ? number_format($zone->fee, 0, ',', '.').' ₫' : 'Miễn phí' }}</td>
                        <td>
                            <span class="badge" style="background:{{ $zone->is_active ? '#D1FAE5' : '#FEE2E2' }};color:{{ $zone->is_active ? '#047857' : '#B91C1C' }};">{{ $zone->is_active ? 'Đang phục vụ' : 'Tạm ngưng' }}</span>
                        </td>
                        <td style="white-space:nowrap;">
                            <button type="button" class="btn" style="padding:5px 8px;background:#F1F5F9;color:#475569;" onclick='editDeliveryZone(@json($zone->id), @json($zone->name), @json((int) $zone->fee), @json((bool) $zone->is_active))'>Sửa</button>
                            <button type="button" class="btn" style="padding:5px 8px;background:#FEE2E2;color:#B91C1C;" onclick="deleteDeliveryZone({{ $zone->id }})">Xóa</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:#94A3B8;padding:22px;">Chưa cấu hình khu vực giao hàng.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('storeSettingForm').addEventListener('submit', async function (event) {
    event.preventDefault();
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const formData = new FormData(this);
    formData.set('is_open', this.querySelector('[name="is_open"]').checked ? '1' : '0');
    formData.append('_method', 'PUT');
    const response = await fetch('/api/v1/admin/settings', {method:'POST',headers:{'X-CSRF-TOKEN':token,'Accept':'application/json'},body:formData});
    if (response.ok) window.location.reload();
    else { const data = await response.json(); alert(data.message || 'Không thể lưu cấu hình.'); }
});

const deliveryZoneForm = document.getElementById('deliveryZoneForm');
const deliveryZoneId = document.getElementById('deliveryZoneId');
const deliveryZoneName = document.getElementById('deliveryZoneName');
const deliveryZoneFee = document.getElementById('deliveryZoneFee');
const deliveryZoneActive = document.getElementById('deliveryZoneActive');

function resetDeliveryZoneForm() {
    deliveryZoneId.value = '';
    deliveryZoneName.value = '';
    deliveryZoneFee.value = '0';
    deliveryZoneActive.checked = true;
    deliveryZoneName.focus();
}

function editDeliveryZone(id, name, fee, active) {
    deliveryZoneId.value = id;
    deliveryZoneName.value = name;
    deliveryZoneFee.value = fee;
    deliveryZoneActive.checked = active;
    deliveryZoneName.focus();
}

deliveryZoneForm.addEventListener('submit', async function (event) {
    event.preventDefault();
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const id = deliveryZoneId.value;
    const response = await fetch(id ? `/api/v1/admin/delivery-zones/${id}` : '/api/v1/admin/delivery-zones', {
        method: id ? 'PUT' : 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},
        body: JSON.stringify({name: deliveryZoneName.value.trim(), fee: Number(deliveryZoneFee.value), is_active: deliveryZoneActive.checked}),
    });
    if (response.ok) window.location.reload();
    else { const data = await response.json(); alert(data.message || 'Không thể lưu khu vực giao hàng.'); }
});

async function deleteDeliveryZone(id) {
    if (!confirm('Xóa khu vực giao hàng này?')) return;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const response = await fetch(`/api/v1/admin/delivery-zones/${id}`, {method:'DELETE',headers:{'X-CSRF-TOKEN':token,'Accept':'application/json'}});
    if (response.ok) window.location.reload();
    else { const data = await response.json(); alert(data.message || 'Không thể xóa khu vực giao hàng.'); }
}
</script>
@endsection
