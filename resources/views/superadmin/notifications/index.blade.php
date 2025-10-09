@extends('layouts.appsuperadmin')

@section('title', 'Thông báo hệ thống')

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
    <h1>Thông báo hệ thống</h1>

    <div style="margin-bottom: 15px;">
        <button class="btn btn-success" onclick="openModal('add')">+ Thêm mới</button>
        <button id="deleteBtn" class="btn btn-danger" disabled>Xóa 0 mục</button>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th><input type="checkbox" id="checkAll"></th>
                <th>Tiêu đề</th>
                <th>Nội dung</th>
                <th>Thời gian tạo</th>
                <th>Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notifications as $notification)
                <tr>
                    <td><input type="checkbox" class="row-check" value="{{ $notification->id }}"></td>
                    <td>{{ $notification->title }}</td>
                    <td>{{ $notification->message }}</td>
                    <td>{{ $notification->created_at }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="openModal('edit', @json($notification))">Sửa</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal thêm/sửa -->
<div id="notificationModal" class="modal" style="display:none;">
    <div class="modal-dialog">
        <form id="notificationForm" method="POST" action="{{ route('superadmin.notifications.store') }}">
            @csrf
            <input type="hidden" name="id" id="notificationId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm/Sửa Thông báo</h5>
                    <button type="button" class="btn-close" onclick="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tiêu đề <span class="required">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nội dung <span class="required">*</span></label>
                        <textarea name="message" id="message" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('notificationModal');
const form  = document.getElementById('notificationForm');
const checkAll = document.getElementById('checkAll');
const deleteBtn = document.getElementById('deleteBtn');

function openModal(mode, data = null) {
    modal.style.display = 'block';
    if (mode === 'edit' && data) {
        document.querySelector('.modal-title').innerText = 'Sửa Thông báo';
        form.action = "{{ route('superadmin.notifications.update') }}";
        document.getElementById('notificationId').value = data.id;
        document.getElementById('title').value = data.title;
        document.getElementById('message').value = data.message;
    } else {
        document.querySelector('.modal-title').innerText = 'Thêm Thông báo';
        form.action = "{{ route('superadmin.notifications.store') }}";
        form.reset();
        document.getElementById('notificationId').value = '';
    }
}

function closeModal() { modal.style.display = 'none'; }

const checkboxes = document.querySelectorAll('.row-check');

// Chọn tất cả
checkAll.addEventListener('change', function(){
    checkboxes.forEach(cb => cb.checked = this.checked);
    toggleDeleteBtn();
});

checkboxes.forEach(cb => cb.addEventListener('change', toggleDeleteBtn));

function toggleDeleteBtn(){
    const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
    deleteBtn.disabled = !anyChecked;
    deleteBtn.textContent = anyChecked 
        ? `Xóa ${Array.from(checkboxes).filter(cb=>cb.checked).length} mục` 
        : "Xóa 0 mục";
}

// Xóa nhiều
deleteBtn.addEventListener('click', () => {
    const ids = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
    if(ids.length === 0) return;
    if(!confirm(`Bạn có chắc muốn xóa ${ids.length} thông báo?`)) return;

    fetch("{{ route('superadmin.notifications.destroyMultiple') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids })
    })
    .then(res => res.json())
    .then(r => {
        if(r.success) location.reload();
        else alert(r.message || 'Xóa thất bại');
    })
    .catch(err => {
        console.error("Lỗi fetch:", err);
        alert("Có lỗi xảy ra khi gọi API xóa.");
    });
});
</script>
@endsection
