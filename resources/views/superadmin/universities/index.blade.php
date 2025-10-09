@extends('layouts.appsuperadmin')

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
body.dark-mode .dashboard-card {
    background: #1f2937;
    color: #f3f4f6;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
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
.status.active {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 500;
    color: #22c55e;
    background: rgba(34,197,94,0.1);
    border: 1px solid #22c55e;
}

.status.inactive {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 500;
    color: #ef4444;
    background: rgba(239,68,68,0.1);
    border: 1px solid #ef4444;
}
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
    vertical-align: middle;
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
    table tbody tr {
    transition: background 0.25s ease, transform 0.2s ease;
    }
    table tbody tr:hover {
        background: #e0f2fe;  /* xanh nhạt */
        transform: scale(1.01);
    }
    .action-icon {
    transition: transform 0.3s, color 0.3s;
    }
    .action-icon:hover {
        transform: rotate(20deg) scale(1.2);
        color: #2563eb;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge.bg-success::before { content: "✅"; }
    .badge.bg-danger::before { content: "⛔"; }
    .badge.bg-info::before   { content: "⏸"; }
    .badge.bg-secondary::before { content: "❓"; }

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
{{-- ===== PHẦN 1: GRID CARD TRƯỜNG ===== --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    
    <h1 style="font-size:26px; font-weight:700; color:#2563eb; margin-bottom:12px; letter-spacing:0.5px;">
        🎓 Trường Đại học
    </h1>
    <div style="position:relative; width:300px; margin:0 auto;">
        <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af;">🔍</span>
        <input type="text" id="searchInput" placeholder="Tìm kiếm trường..."
               style="width:100%; padding:10px 12px 10px 34px; border:1px solid #d1d5db; border-radius:8px; transition:all 0.3s;">
    </div>

</div>
<div class="row g-4 mb-4">
    @foreach($universities as $uni)
        <div class="col-md-3">
            <div class="dashboard-card p-3 text-center"
                 style="border-radius:16px; 
                        background:linear-gradient(135deg,#f0f9ff,#e0f2fe); 
                        color:#0f172a;
                        box-shadow:0 4px 10px rgba(0,0,0,0.08);
                        transition:all .3s; cursor:pointer;"
                 onmouseover="this.style.transform='scale(1.03)'; this.style.boxShadow='0 6px 14px rgba(0,0,0,0.15)';"
                 onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.08)';">
                <img src="{{ $uni->logo ? asset($uni->logo) : asset('logos/default.png') }}"
                     style="width:60px; height:60px; object-fit:cover; border-radius:50%; margin-bottom:10px; border:2px solid #fff;">
                <h5 style="margin:0; font-size:15px; font-weight:600;">{{ $uni->university_name }}</h5>
                <p style="margin:4px 0 0; font-size:12px; opacity:0.8;">Mã: {{ $uni->code_short }}</p>
            </div>
        </div>
    @endforeach
</div>


{{-- ===== PHẦN 2: BẢNG + FORM BULK DELETE ===== --}}


<div style="display:flex; gap:10px; flex-wrap:wrap;">
    <button class="btn btn-primary" onclick="openModal('add')">+ Thêm mới</button>
    <button id="deleteBtn" type="button" class="btn btn-disabled" disabled>Xóa 0 mục</button>
</div>

{{-- FORM bulk delete CHỈ BỌC BẢNG --}}
<form id="bulkDeleteForm" action="{{ route('superadmin.university.destroyMultiple') }}" method="POST" style="margin-top:12px;">
    @csrf
    @method('DELETE')

    <table>
        <thead>
            <tr>
                <th style="width:48px;"><input type="checkbox" id="checkAll"></th>
                <th>Tên trường</th>
                <th>Loại</th>
                <th>Địa chỉ</th>
                <th>Email</th>
                <th>Website</th>
                <th>Trạng thái</th> 
                <th style="width:50px;">Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="universityTable">
            @foreach($universities as $uni)
            <tr>
                <td><input type="checkbox" class="row-check" name="ids[]" value="{{ $uni->id }}"></td>
                <td>{{ $uni->university_name }}</td>
                <td>{{ $uni->university_type }}</td>
                <td>{{ $uni->address }}</td>
                <td>{{ $uni->email }}</td>
                <td>{{ $uni->website }}</td>
                <td>
                    @php
                        $statusId = $uni->status_id;
                        $statusName = match($statusId) {
                            1 => 'Đang hoạt động',
                            2 => 'Không hoạt động',
                            3 => 'Ngừng hoạt động',
                            4 => 'Tạm dừng',
                            default => 'Chưa xác định'
                        };
                        $badgeClass = match($statusId) {
                            1 => 'bg-success',
                            2 => 'bg-danger',
                            3 => 'bg-danger',
                            4 => 'bg-info',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}" style="padding:4px 10px; border-radius:12px; font-size:13px; font-weight:500; color:#fff;">
                        {{ $statusName }}
                    </span>
                </td>
                <td>
                    <button class="action-icon" type="button" onclick="openModal('edit',
                        '{{ $uni->id }}',
                        `{{ addslashes($uni->university_name) }}`,
                        `{{ addslashes($uni->university_type) }}`,
                        `{{ addslashes($uni->address) }}`,
                        `{{ addslashes($uni->phone) }}`,
                        `{{ addslashes($uni->email) }}`,
                        `{{ addslashes($uni->website) }}`,
                        '{{ $uni->status_id }}')">⚙️</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</form>

{{ $universities->links() }}

</div>

<!-- Modal -->
<div id="universityModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle">Thêm Trường</h2>
        <form id="universityForm" onsubmit="event.preventDefault(); saveUniversity();">
            <input type="hidden" id="universityId">
            <!--<input type="hidden" id="statusId">-->
            <div class="form-group">
                <label>Tên trường <span class="required">*</span></label>
                <input type="text" id="universityName" required>
            </div>
            <div class="form-group">
                <label>Loại trường</label>
                <input type="text" id="universityType">
            </div>
            <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" id="address">
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" id="phone">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="email">
            </div>
            <div class="form-group">
                <label>Website</label>
                <input type="text" id="website">
            </div>
            <div class="form-group">
                <label>Trạng thái</label>
                <select id="statusId">
                    <option value="1">Đang hoạt động</option>
                    <option value="3">Ngừng hoạt động</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
// ========== Modal add/edit ==========
function openModal(type, id='', name='', typeVal='', address='', phone='', email='', website='',statusId='1') {
    document.getElementById('modalTitle').textContent = type === 'add' ? 'Thêm Trường' : 'Sửa Trường';
    document.getElementById('universityId').value = id;
    document.getElementById('universityName').value = name;
    document.getElementById('universityType').value = typeVal;
    document.getElementById('address').value = address;
    document.getElementById('phone').value = phone;
    document.getElementById('email').value = email;
    document.getElementById('website').value = website;
    document.getElementById('statusId').value = statusId;
    document.getElementById('universityModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('universityModal').style.display = 'none';
}

function saveUniversity() {
    const id = document.getElementById('universityId').value;
    const data = {
        id,
        university_name: document.getElementById('universityName').value,
        university_type: document.getElementById('universityType').value,
        address: document.getElementById('address').value,
        phone: document.getElementById('phone').value,
        email: document.getElementById('email').value,
        website: document.getElementById('website').value,
        status_id: document.getElementById('statusId').value
    };

    fetch("{{ route('superadmin.university.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(async res => {
        // hỗ trợ cả redirect (HTML) lẫn JSON
        const ct = res.headers.get('content-type') || '';
        if (ct.includes('application/json')) return res.json();
        // nếu controller trả redirect, coi như thành công:
        return { success: res.ok };
    })
    .then(resp => {
        if (resp && resp.success) {
            closeModal();
            location.reload();
        } else {
            alert('Lưu thất bại');
        }
    })
    .catch(err => alert('Có lỗi: ' + err.message));
}

// ========== Xóa nhiều + đếm số mục ==========
const deleteBtn = document.getElementById('deleteBtn');
const checkAll  = document.getElementById('checkAll');
const tableBody = document.getElementById('universityTable');
const formBulk  = document.getElementById('bulkDeleteForm');

function updateDeleteButton() {
    const rowChecks = tableBody.querySelectorAll('.row-check');
    const checked   = tableBody.querySelectorAll('.row-check:checked').length;

    // cập nhật nút
    deleteBtn.textContent = `Xóa ${checked} mục`;
    deleteBtn.disabled = checked === 0;
    deleteBtn.classList.toggle('btn-disabled', checked === 0);
    deleteBtn.classList.toggle('btn-secondary', checked > 0);

    // sync trạng thái checkAll
    checkAll.checked = checked > 0 && checked === rowChecks.length;
    checkAll.indeterminate = checked > 0 && checked < rowChecks.length;
}

checkAll.addEventListener('change', function () {
    tableBody.querySelectorAll('.row-check').forEach(cb => { cb.checked = checkAll.checked; });
    updateDeleteButton();
});

tableBody.addEventListener('change', function(e){
    if (e.target.classList.contains('row-check')) updateDeleteButton();
});

deleteBtn.addEventListener('click', function () {
    const n = tableBody.querySelectorAll('.row-check:checked').length;
    if (n === 0) return;
    if (confirm(`Xóa ${n} trường đã chọn?`)) {
        formBulk.submit(); // gửi DELETE với ids[]
    }
});

// khởi tạo
updateDeleteButton();

// ========== Tìm kiếm bỏ dấu, không cần khớp chính xác ==========
function normalizeVN(str) {
    return (str || '')
        .toString()
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, ''); // bỏ dấu
}

document.getElementById('searchInput').addEventListener('input', function () {
    const keyword = normalizeVN(this.value.trim());
    const rows = tableBody.querySelectorAll('tr');

    rows.forEach(row => {
        const haystack = normalizeVN(row.innerText);
        row.style.display = haystack.includes(keyword) ? '' : 'none';
    });
});
</script>
@endsection