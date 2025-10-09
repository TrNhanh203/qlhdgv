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
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1>Admin Trường</h1>
        <div style="position:relative; width:250px;">
            <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af;">🔍</span>
            <input type="text" id="searchInput" placeholder="Tìm kiếm admin..." style="width:100%; padding:8px 12px 8px 32px; border:1px solid #d1d5db; border-radius:8px;">
        </div>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn btn-primary" onclick="openModal('add')">+ Thêm mới</button>
        <button id="deleteBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:48px;"><input type="checkbox" id="checkAll"></th>
                <th>Email</th>
                <th>Thuộc Trường</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th style="width:50px;">Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="adminTable">
            @foreach($admins as $admin)
            <tr>
                <td><input type="checkbox" class="row-check" data-id="{{ $admin->id }}"></td>
                <td>{{ $admin->email }}</td>
                <td>{{ $admin->university->university_name ?? 'Chưa cập nhật' }}</td>
                <td>
                    @php
    $statusId = $admin->status_id;
    // Debug
    // dd($statusId); // thử in ra xem giá trị thực tế

        $statusName = match((int)$statusId) { 
        1 => 'Đang hoạt động',
        2 => 'Không hoạt động',
        3 => 'Ngừng hoạt động',
        4 => 'Tạm dừng',
        5 => 'Hoàn thành',
        6 => 'Chưa hoàn thành',
        7 => 'Đã bị khóa',
        default => 'Chưa xác định'
    };

    $badgeClass = match((int)$statusId) {
        1 => 'bg-success',    
        2 => 'bg-danger',     
        3 => 'bg-danger',     
        4 => 'bg-warning',    
        5 => 'bg-blue',       
        6 => 'bg-gray',       
        7 => 'bg-danger',  
        default => 'bg-secondary'
    };
@endphp



                    <span class="badge {{ $badgeClass }}" 
                        style="padding:4px 10px; border-radius:12px; font-size:13px; font-weight:500; color:#fff;">
                        {{ $statusName }}
                    </span>
                </td>

                <td>{{ $admin->created_at?->format('d-m-Y') }}</td>
                <td>{{ $admin->updated_at?->format('d-m-Y') }}</td>
                <td>
                    <button class="action-icon"
                        onclick="openModal('edit','{{ $admin->id }}','{{ $admin->email }}','{{ $admin->university_id }}','{{ $admin->status_id }}')">⚙️</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $admins->links() }}
</div>

<!-- Modal -->
<div id="adminModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle">Thêm Admin</h2>
        <form id="adminForm" onsubmit="event.preventDefault(); saveAdmin();">
            <input type="hidden" id="adminId">
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="adminEmail" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" id="adminPassword" placeholder="Để trống nếu không đổi">
            </div>
            <div class="form-group">
                <label>Thuộc Trường</label>
                <select id="universityId" required>
                    <option value="">-- Chọn trường --</option>
                    @foreach($universities as $university)
                        <option value="{{ $university->id }}">{{ $university->university_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Trạng thái</label>
                <select id="statusId" required>
                    <option value="1">Đang hoạt động</option>
                    <option value="7">Đã bị khóa</option>
                </select>
            </div>

            <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>
<script>
const deleteBtn = document.getElementById('deleteBtn');
const checkAll = document.getElementById('checkAll');

function saveAdmin() {
    const id = document.getElementById('adminId').value;
    const email = document.getElementById('adminEmail').value;
    const password = document.getElementById('adminPassword').value;
    const university_id = document.getElementById('universityId').value;
    const status_id = document.getElementById('statusId').value;

    // 🔥 Dùng FormData thay cho JSON
    const formData = new FormData();
    formData.append('id', id);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('university_id', university_id);
    formData.append('status_id', status_id);

    fetch("{{ route('superadmin.university_admins.store') }}", {
        method:'POST',
        headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
        body: formData
    }).then(r=>r.json())
      .then(d=>{ 
          if(d.success){ 
              closeModal(); 
              location.reload(); 
          } else {
              alert(d.message || 'Không lưu được admin');
          }
      })
      .catch(err=>alert('Có lỗi: '+err.message));
}

function openModal(type,id='',email='',university_id='',status=1){
    document.getElementById('modalTitle').textContent = type==='add'?'Thêm Admin':'Sửa Admin';
    document.getElementById('adminId').value = id;
    document.getElementById('adminEmail').value = email;
    document.getElementById('adminPassword').value = '';
    document.getElementById('universityId').value = university_id;
    document.getElementById('statusId').value = status;
    document.getElementById('adminModal').style.display='flex';
}
function closeModal(){ document.getElementById('adminModal').style.display='none'; }

document.addEventListener('DOMContentLoaded', ()=>{
    document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', updateDeleteButton));
    checkAll.addEventListener('change', function(){
        const checked = this.checked;
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = checked);
        updateDeleteButton();
    });
    updateDeleteButton();
});

deleteBtn.addEventListener('click', ()=>{
    const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(cb=>cb.dataset.id);
    if(ids.length && confirm(`Xóa ${ids.length} admin?`)){
        fetch("{{ route('superadmin.university_admins.destroyMultiple') }}", {
            method:'POST',
            headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
            body: JSON.stringify({ ids })
        }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
    }
});

document.getElementById('searchInput').addEventListener('keyup', function(){
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('#adminTable tr').forEach(row=>{
        const emailCell=row.querySelector('td:nth-child(2)');
        row.style.display=(emailCell && emailCell.textContent.toLowerCase().includes(keyword))?'':'none';
    });
});

function updateDeleteButton(){
    const n = document.querySelectorAll('.row-check:checked').length;
    if(n>0){
        deleteBtn.disabled=false;
        deleteBtn.classList.remove('btn-disabled');
        deleteBtn.classList.add('btn-secondary');
        deleteBtn.textContent=`Xóa ${n} mục`;
    }else{
        deleteBtn.disabled=true;
        deleteBtn.classList.add('btn-disabled');
        deleteBtn.classList.remove('btn-secondary');
        deleteBtn.textContent='Xóa 0 mục';
    }
}
</script>
@endsection