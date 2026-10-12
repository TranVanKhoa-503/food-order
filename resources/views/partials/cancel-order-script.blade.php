<script>
    const pendingCancellations = new Set();

    async function cancelOrder(orderId, button) {
        if (pendingCancellations.has(orderId)) return;
        if (!confirm('Bạn có chắc muốn hủy đơn hàng này không?')) return;

        pendingCancellations.add(orderId);
        if (button) button.disabled = true;
        let reloading = false;
        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch(`/api/v1/orders/${orderId}/cancel`, {
                method: 'PATCH',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                body: JSON.stringify({reason: 'Khách hàng hủy từ trang web'})
            });

            if (response.ok || response.status === 409) {
                if (response.status === 409) alert('Đơn hàng đã được cập nhật. Trang sẽ tải lại để hiển thị trạng thái mới.');
                reloading = true;
                window.location.reload();
                return;
            }

            if (response.status === 401 || response.status === 419) {
                alert('Phiên đăng nhập đã hết hạn hoặc thay đổi. Trang sẽ tải lại.');
                reloading = true;
                window.location.reload();
                return;
            }

            const data = await response.json().catch(() => ({}));
            alert(data.message || 'Không thể hủy đơn hàng. Vui lòng thử lại.');
        } catch (error) {
            alert('Không thể kết nối máy chủ. Vui lòng thử lại.');
        } finally {
            if (!reloading) {
                pendingCancellations.delete(orderId);
                if (button) button.disabled = false;
            }
        }
    }

    window.addEventListener('pageshow', () => {
        pendingCancellations.clear();
        document.querySelectorAll('[data-cancel-order]').forEach(button => { button.disabled = false; });
    });
</script>
