<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@vite(['resources/js/crud-core.js'])
<script>
    const ROUTE_STORE = "{{ $routes['store'] ?? '' }}";
    const ROUTE_DELETE_MULTI = "{{ $routes['destroyMultiple'] ?? '' }}";

    document.getElementById('crudForm').addEventListener('submit', async e => {
        e.preventDefault();
        const data = {};
        document.querySelectorAll('#crudForm [data-field]').forEach(i => data[i.dataset.field] = i.value);

        // thêm parent_id nếu có
        const parentIdInput = document.getElementById('parentId');
        if (parentIdInput) data.parent_id = parentIdInput.value;

        if (document.getElementById('crudId')) data.id = document.getElementById('crudId').value;

        try {
            const res = await CRUD.postJson(ROUTE_STORE, data);
            CRUD.toast(res.success ? "✅ Lưu thành công" : "❌ Lỗi khi lưu", res.success);
            if (res.success) location.reload();
        } catch (err) {
            CRUD.toast("❌ Request lỗi: " + err);
        }
    });

    document.getElementById('deleteBtn')?.addEventListener('click', async () => {
        const ids = CRUD.getSelectedIds();
        if (ids.length === 0) return CRUD.toast("Chưa chọn mục nào");
        if (!confirm(`Xóa ${ids.length} mục?`)) return;
        try {
            const res = await CRUD.postJson(ROUTE_DELETE_MULTI, {
                ids
            });
            CRUD.toast(res.success ? "🗑️ Đã xóa thành công" : "❌ Xóa thất bại", res.success);
            if (res.success) location.reload();
        } catch (err) {
            CRUD.toast("❌ Request lỗi: " + err);
        }
    });

    // === XỬ LÝ TICK CHECKBOX VÀ CẬP NHẬT NÚT XÓA ===
    document.addEventListener('DOMContentLoaded', () => {
        const deleteBtn = document.getElementById('deleteBtn');
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-check');

        function getSelectedIds() {
            return Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
        }

        function updateDeleteButton() {
            const count = getSelectedIds().length;
            deleteBtn.textContent = count > 0 ? `Xóa ${count} mục` : 'Xóa';
            deleteBtn.disabled = count === 0;
            deleteBtn.className = count === 0 ? 'btn btn-disabled' : 'btn btn-primary';
        }

        // tick từng dòng
        checkboxes.forEach(cb => cb.addEventListener('change', updateDeleteButton));

        // tick chọn tất cả
        selectAll?.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateDeleteButton();
        });

        // cập nhật ngay khi load lại DOM (phòng khi có sẵn tick)
        updateDeleteButton();
    });
</script>
