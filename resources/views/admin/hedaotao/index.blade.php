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

        .btn-primary { background: #2563eb; color: #fff; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-secondary { background: #f3f4f6; color: #111827; }
    .btn-secondary:hover { background: #e5e7eb; }
    .btn-disabled { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; }


    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }

    table th, table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
    }

    table th {
        background: #f1f5f9;
        font-weight: 600;
        font-size: 14px;
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
    body.dark-mode table th {
        background: #1f2937;
        color: #e5e7eb;
        border-bottom-color: #374151;
    }
    body.dark-mode table td {
        border-bottom-color: #374151;
        color: #f3f4f6;
    }
    body.dark-mode table tbody tr:hover { background: #2b3443; }

    body.dark-mode .action-icon { color: #e5e7eb; }

    body.dark-mode .btn-secondary { background: #374151; color: #e5e7eb; }
    body.dark-mode .btn-secondary:hover { background: #4b5563; }
    /* giữ .btn-primary như cũ để nổi bật trong nền tối */

    body.dark-mode .status.active { background: rgba(34,197,94,.15); color: #22c55e; }
    body.dark-mode .status.inactive { background: rgba(239,68,68,.15); color: #ef4444; }

    body.dark-mode .modal-content {
        background: #2c2c3e;
        color: #f1f1f1;
        border: 1px solid #3b3b52;
    }
    body.dark-mode .form-group label { color: #e5e7eb; }
    body.dark-mode .form-group input,
    body.dark-mode .form-group select {
        background: #1f2937;
        color: #e5e7eb;
        border-color: #4b5563;
    }
    body.dark-mode .form-group input::placeholder { color: #9ca3af; }
    body.dark-mode .form-group input:focus,
    body.dark-mode .form-group select:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96,165,250,.2);
    }


</style>
<div class="container">
    <h1>Hệ đào tạo</h1>

    <button class="btn btn-primary" onclick="openModal('add')">+ Thêm mới</button>
    <button id="deleteBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>

    <table id="educationTable">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>STT</th>
                <th>Mã hệ đào tạo</th>
                <th>Tên hệ đào tạo</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th>Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($systems as $system)
            <tr>
                <td><input type="checkbox" class="row-check"></td>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $system['code'] }}</td>
                <td>{{ $system['name'] }}</td>
                <td>{{ $system['created_at'] }}</td>
                <td>{{ $system['updated_at'] }}</td>
                <td>
                    <button class="action-icon" onclick="openModal('edit','{{ $system['code'] }}','{{ $system['name'] }}')">⚙️</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="educationSystemModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle">Thêm Hệ đào tạo</h2>
        <form id="educationSystemForm">
            <div class="form-group">
                <label>Mã hệ đào tạo</label>
                <input type="text" id="educationSystemCode" required>
            </div>
            <div class="form-group">
                <label>Tên hệ đào tạo</label>
                <input type="text" id="educationSystemName" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
            </div>
        </form>
    </div>
</div>

<script>
let modal = document.getElementById('educationSystemModal');
let editCode = null;

function openModal(type, code='', name='') {
    document.getElementById('modalTitle').textContent = type==='add'?'Thêm Hệ đào tạo':'Sửa Hệ đào tạo';
    document.getElementById('educationSystemCode').value = code;
    document.getElementById('educationSystemName').value = name;
    modal.style.display='flex';
    editCode = type==='edit'?code:null;
}

function closeModal() { modal.style.display='none'; }

window.onclick = function(e){ if(e.target===modal) closeModal(); }

document.getElementById('educationSystemForm').addEventListener('submit', function(e){
    e.preventDefault();
    const code = document.getElementById('educationSystemCode').value;
    const name = document.getElementById('educationSystemName').value;

    if(editCode){
        fetch(`/admin/hedaotao/${editCode}`,{
            method:'PUT',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
            body: JSON.stringify({code,name})
        }).then(()=>location.reload());
    } else {
        fetch(`/admin/hedaotao`,{
            method:'POST',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
            body: JSON.stringify({code,name})
        }).then(()=>location.reload());
    }
});

const deleteBtn = document.getElementById('deleteBtn');
function attachCheckboxListeners() {
    const checkboxes = document.querySelectorAll('.row-check');
    const selectAll = document.getElementById('selectAll');

    function updateDeleteCount(){
        const selected = document.querySelectorAll('.row-check:checked').length;
        deleteBtn.textContent = `Xóa ${selected} mục`;
        deleteBtn.disabled = selected===0;
        deleteBtn.className = selected===0?'btn btn-disabled':'btn btn-primary';
    }

    checkboxes.forEach(cb=>cb.addEventListener('change',updateDeleteCount));
    selectAll.addEventListener('change',function(){
        checkboxes.forEach(cb=>cb.checked=this.checked);
        updateDeleteCount();
    });
}

deleteBtn.addEventListener('click', function(){
    const checkedBoxes = document.querySelectorAll('.row-check:checked');
    checkedBoxes.forEach(cb=>{
        const code = cb.closest('tr').children[2].textContent;
        fetch(`/admin/hedaotao/${code}`,{
            method:'DELETE',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
        }).then(()=>location.reload());
    });
});

attachCheckboxListeners();
</script>
@endsection