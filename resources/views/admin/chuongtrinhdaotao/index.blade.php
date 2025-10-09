@extends('layouts.app')

@section('content')
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f9fafb;
        margin: 0;
    }

    .dark-mode {
        background: #1f2937;
        color: #f3f4f6;
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    h1 {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 16px;
        color: #333;
    }

    .dark-mode h1 { color: #f3f4f6; }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: 0.2s;
    }
    .tag {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        color: #fff;
    }

    .btn-primary { background: #2563eb; color: #fff; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-secondary { background: #f3f4f6; color: #111827; }
    .btn-secondary:hover { background: #e5e7eb; }
    .btn-disabled { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; }

    .tag-primary { background-color: #2563eb; }  
    .tag-green { background-color: #22c55e; }   
    .tag-blue { background-color: #3b82f6; }    
    .tag-gray { background-color: #6b7280; } 

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }
    table td {
        vertical-align: middle;
    }
    table th, table td {
        padding: 12px 10px;
        border-bottom: 1px solid #e5e7eb;
        text-align: center;
        font-size: 14px;
    }
    table th {
        background: #f1f5f9;
        font-weight: 600;
        text-align: center;
    }

    .dark-mode table th { background: #374151; color: #f3f4f6; }
    .dark-mode table td { border-bottom: 1px solid #374151; color: #f3f4f6; }

    .action-icon { background: none; border: none; font-size: 18px; cursor: pointer; }

    /* ===== Overlay Modal ===== */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        width: 600px;
        max-width: 90%;
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        animation: fadeIn 0.3s ease;
    }

    .dark-mode .modal-content { background: #1f2937; color: #f3f4f6; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    .modal-content h2 { margin-bottom: 16px; font-size: 20px; }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 12px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .form-group label .required { color: red; margin-left: 2px; }

    .form-group input, .form-group select {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
    }

    .dark-mode .form-group input, .dark-mode .form-group select {
        background: #374151;
        color: #f3f4f6;
        border: 1px solid #6b7280;
    }

    .form-actions {
        margin-top: 16px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    body.dark-mode .container {
        background: #2c2c3e;
        color: #f1f1f1;
        box-shadow: 0 6px 18px rgba(0,0,0,0.35);
    }
    body.dark-mode h1 { color: #f3f4f6; }
    body.dark-mode table { color: #e5e7eb; }
    body.dark-mode table th { background: #1f2937; color: #e5e7eb; border-bottom-color: #374151; }
    body.dark-mode table td { border-bottom-color: #374151; color: #f3f4f6; }
    body.dark-mode table tbody tr:hover { background: #2b3443; }
    tbody tr:hover { background-color: #f3f4f6; }
    body.dark-mode .action-icon { color: #e5e7eb; }
    body.dark-mode .btn-secondary { background: #374151; color: #e5e7eb; }
    body.dark-mode .btn-secondary:hover { background: #4b5563; }
    body.dark-mode .status.active { background: rgba(34,197,94,.15); color: #22c55e; }
    body.dark-mode .status.inactive { background: rgba(239,68,68,.15); color: #ef4444; }
    body.dark-mode .modal-content { background: #2c2c3e; color: #f1f1f1; border: 1px solid #3b3b52; }
    body.dark-mode .form-group label { color: #e5e7eb; }
    body.dark-mode .form-group input, body.dark-mode .form-group select { background: #1f2937; color: #e5e7eb; border-color: #4b5563; }
    body.dark-mode .form-group input::placeholder { color: #9ca3af; }
    body.dark-mode .form-group input:focus, body.dark-mode .form-group select:focus { border-color: #60a5fa; box-shadow: 0 0 0 3px rgba(96,165,250,.2); }
</style>

<div class="container">
    <h1>Chương trình đào tạo</h1>

    <button class="btn btn-primary" onclick="openModal('add')">+ Thêm mới</button>
    <button id="deleteBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>

    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>Mã CTĐT</th>
                <th>Tên CTĐT</th>
                <th>Khoa/Viện</th>
                <th>Mã hệ đào tạo</th>
                <th>Tên hệ đào tạo</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th>Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($programs as $program)
                <tr>
                    <td><input type="checkbox" class="row-check" value="{{ $program->id }}"></td>
                    <td>{{ $program->program_code }}</td>
                    <td>{{ $program->program_name }}</td>
                    <td>{{ $program->faculty->faculty_name ?? '—' }}</td>
                    <td>{{ $program->education_system_code }}</td>
                    <td>{{ $program->education_system_name }}</td>
                    <td>{{ $program->created_at?->format('d-m-Y H:i') }}</td>
                    <td>{{ $program->updated_at?->format('d-m-Y H:i') }}</td>
                    <td>
                    <button class="action-icon"
                        onclick="openModal('edit',
                            '{{ $program->program_code }}',
                            '{{ addslashes($program->program_name) }}',
                            '{{ $program->education_system_code }}',
                            '{{ $program->education_system_name }}',
                            '{{ $program->created_at?->format('d-m-Y H:i') }}',
                            '{{ $program->updated_at?->format('d-m-Y H:i') }}',
                            '{{ $program->id }}'
                        )">⚙️</button>
                </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="programModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle">Thêm Chương trình</h2>
        <form id="programForm">
            <input type="hidden" id="programId">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="programCode">Mã chương trình <span class="required">*</span></label>
                    <input type="text" id="programCode" required>
                </div>

                <div class="form-group">
                    <label for="programName">Tên chương trình <span class="required">*</span></label>
                    <input type="text" id="programName" required>
                </div>

                <div class="form-group">
                    <label for="faculty">Khoa/Viện <span class="required">*</span></label>
                    <select id="faculty" required>
                        <option value="">-- Chọn Khoa/Viện --</option>
                        @foreach($faculties as $f)
                            <option value="{{ $f->id }}">{{ $f->faculty_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="educationSystemCode">Mã hệ đào tạo <span class="required">*</span></label>
                    <select id="educationSystemCode" required onchange="updateEducationSystemName()">
                        <option value="">-- Chọn Mã hệ đào tạo --</option>
                        @foreach($educationSystems as $edu)
                            <option value="{{ $edu['code'] }}">{{ $edu['code'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="educationSystemName">Tên hệ đào tạo <span class="required">*</span></label>
                    <input type="text" id="educationSystemName" readonly required>
                </div>

                <div class="form-group">
                    <label for="createDate">Ngày tạo <span class="required">*</span></label>
                    <input type="text" id="createDate" readonly>
                </div>

                <div class="form-group">
                    <label for="updateDate">Ngày cập nhật <span class="required">*</span></label>
                    <input type="text" id="updateDate" readonly>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
            </div>
        </form>
    </div>
</div>

@php
$educationSystemsMap = [];
foreach($educationSystems as $edu){
    $educationSystemsMap[$edu['code']] = $edu['name'];
}
@endphp
<script>
    /* ================== CẤU HÌNH ================== */
const ROUTE_STORE = "{{ route('admin.chuongtrinhdaotao.store') }}";
const ROUTE_DESTROY_MULTIPLE = "{{ route('admin.chuongtrinhdaotao.destroyMultiple') }}";
const CSRF = "{{ csrf_token() }}";
const educationSystems = @json($educationSystemsMap);

function updateEducationSystemName() {
    const code = document.getElementById('educationSystemCode').value;
    const nameInput = document.getElementById('educationSystemName');
    nameInput.value = code && educationSystems[code] ? educationSystems[code] : '';
}

function openModal(type, code = '', name = '', eduCode = '', eduName = '', createDate = '', updateDate = '', id = '') {
    const modal = document.getElementById('programModal');
    const title = document.getElementById('modalTitle');
    const codeInput = document.getElementById('programCode');
    const nameInput = document.getElementById('programName');
    const eduCodeInput = document.getElementById('educationSystemCode');
    const eduNameInput = document.getElementById('educationSystemName');
    const createInput = document.getElementById('createDate');
    const updateInput = document.getElementById('updateDate');
    const idInput = document.getElementById('programId');

    if(type === 'add') {
        title.textContent = 'Thêm Chương trình';
        codeInput.value = '';
        nameInput.value = '';
        eduCodeInput.value = '';
        eduNameInput.value = '';
        idInput.value = '';
        const now = new Date();
        const formatted = now.toLocaleString('vi-VN');
        createInput.value = formatted;
        updateInput.value = formatted;
    } else {
        title.textContent = 'Sửa Chương trình';
        codeInput.value = code ?? '';
        nameInput.value = name ?? '';
        eduCodeInput.value = eduCode ?? '';
        eduNameInput.value = eduName ?? '';
        createInput.value = createDate ?? '';
        updateInput.value = updateDate ?? '';
        idInput.value = id ?? '';
    }

    modal.style.display = 'flex';
}

function closeModal(){ document.getElementById('programModal').style.display = 'none'; }

window.onclick = function(e){ const modal = document.getElementById('programModal'); if(e.target === modal) closeModal(); };
// ===== Gửi form AJAX tới Controller =====
document.getElementById('programForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const payload = {
    id: document.getElementById('programId').value || null,
    program_code: document.getElementById('programCode').value.trim(),
    program_name: document.getElementById('programName').value.trim(),
    faculty_id: document.getElementById('faculty').value,
    education_system_code: document.getElementById('educationSystemCode').value,
    education_system_name: document.getElementById('educationSystemName').value
};


    try {
        const res = await fetch(ROUTE_STORE, {
            method: "POST",
            credentials: 'same-origin',              
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",       
                "X-CSRF-TOKEN": CSRF
            },
            body: JSON.stringify(payload)
        });

        if (!res.ok) {
            const text = await res.text().catch(()=>null);
            console.error('SAVE FAIL', res.status, text);
            showToast('❌ Lưu thất bại — status: ' + res.status + '\n' + (text || ''), false);
            return;
        }

        const data = await res.json().catch(()=>null);
        if (data && data.success) {
            showToast('Lưu thành công ✅');
            location.reload();
        } else {
            console.error('SAVE RESPONSE', data);
            showToast('❌ Lưu thất bại: ' + (data?.message ?? JSON.stringify(data)), false);
        }
    } catch (err) {
        console.error('SAVE ERROR', err);
        showToast('❌ Gửi request thất bại: ' + err.message, false);
    }
});
document.addEventListener('change', (e) => {
    if (e.target && e.target.matches('.row-check, #selectAll')) {
        if (e.target.id === 'selectAll') {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = e.target.checked);
        }
        updateDeleteButton();
    }
});
// ====== Checkbox select & delete count ======
const deleteBtn = document.getElementById('deleteBtn');
const checkboxes = document.querySelectorAll('.row-check');
const selectAll = document.getElementById('selectAll');

function updateDeleteButton() {
    const n = getSelectedIds().length;
    deleteBtn.textContent = `Xóa ${n} mục`;
    deleteBtn.disabled = n === 0;
    deleteBtn.className = n === 0 ? 'btn btn-disabled' : 'btn btn-primary';
}

checkboxes.forEach(cb => cb.addEventListener('change', updateDeleteCount));
selectAll.addEventListener('change', function() {
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateDeleteCount();
});

// ====== Gửi request xóa nhiều ======
/* ================== XÓA NHIỀU ================== */
deleteBtn.addEventListener('click', async function(){
    const ids = getSelectedIds();
    if (ids.length === 0) { showToast('Vui lòng chọn mục để xóa'); return; }
    if (!confirm(`Bạn có chắc muốn xóa ${ids.length} chương trình?`)) return;

    try {
        const res = await fetch(ROUTE_DESTROY_MULTIPLE, {
            method: "POST",
            credentials: 'same-origin',              // <- GỬI COOKIE SESSION
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": CSRF
            },
            body: JSON.stringify({ ids })
        });

        if (!res.ok) {
            const text = await res.text().catch(()=>null);
            console.error('DELETE MULTI FAIL', res.status, text);
            showToast('❌ Xóa thất bại — status: ' + res.status + '\n' + (text || ''), false);
            return;
        }

        const data = await res.json().catch(()=>null);
        if (data && data.success) {
            showToast('Xóa thành công ✅');
            location.reload();
        } else {
            console.error('DELETE MULTI RESP', data);
            showToast('❌ Xóa thất bại: ' + (data?.message ?? JSON.stringify(data)), false);
        }
    } catch (err) {
        console.error('DELETE MULTI ERROR', err);
        showToast('❌ Gửi request thất bại: ' + err.message, false);
    }
});
function showToast(msg, ok=true){
    console.log('[TOAST]', msg);
    alert(msg);
}
function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
}
function toggleDarkMode() { document.body.classList.toggle("dark-mode"); }
async function deleteSelected() {
    let selectedIds = [...document.querySelectorAll('input[name="ids[]"]:checked')].map(el => el.value);

    if (selectedIds.length === 0) {
        alert("Vui lòng chọn ít nhất 1 chương trình để xoá!");
        return;
    }

    if (!confirm("Bạn có chắc muốn xoá các mục đã chọn?")) return;

    try {
        const res = await fetch("{{ route('admin.chuongtrinhdaotao.destroyMultiple') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}", // ✅ cần thiết
            },
            body: JSON.stringify({ ids: selectedIds }),
        });

        const data = await res.json();
        if (data.success) {
            alert("Xoá thành công!");
            location.reload();
        } else {
            alert("Xoá thất bại: " + (data.message ?? ""));
        }
    } catch (err) {
        alert("❌ Gửi request thất bại: " + err.message);
    }
}
</script>

@endsection
