@extends('layouts.app')

@section('title', 'Quản lý Phòng thi')

@section('content')
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f9fafb;
        margin: 0;
    }
    .dark-mode { background: #1f2937; color: #f3f4f6; }

    .container {
        max-width: 1200px; margin: 20px auto; background: #fff; border-radius: 12px;
        padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    h1 { font-size: 22px; font-weight: 600; margin-bottom: 16px; color: #333; }
    .dark-mode h1 { color: #f3f4f6; }

    .btn {
        padding: 8px 16px; border: none; border-radius: 8px;
        cursor: pointer; font-weight: 500; transition: 0.2s;
    }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-secondary { background: #f3f4f6; color: #111827; }
    .btn-secondary:hover { background: #e5e7eb; }
    .btn-disabled { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; }

    .status.active {
        display:inline-block;padding:4px 10px;border-radius:12px;font-size:13px;font-weight:500;
        color:#22c55e;background:rgba(34,197,94,0.1);border:1px solid #22c55e;
    }
    .status.inactive {
        display:inline-block;padding:4px 10px;border-radius:12px;font-size:13px;font-weight:500;
        color:#ef4444;background:rgba(239,68,68,0.1);border:1px solid #ef4444;
    }

    table { width:100%; border-collapse:collapse; margin-top:16px; }
    table th, table td {
        padding:12px 10px; border-bottom:1px solid #e5e7eb;
        text-align:center; font-size:14px; vertical-align:middle;
    }
    table th { background:#f1f5f9; font-weight:600; }
    .dark-mode table th { background:#374151; color:#f3f4f6; }
    .dark-mode table td { border-bottom:1px solid #374151; color:#f3f4f6; }

    .action-icon { background:none; border:none; font-size:18px; cursor:pointer; }

    /* Modal */
    .modal {
        display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
        justify-content:center; align-items:center; z-index:1000;
    }
    .modal-content {
        background:#fff; border-radius:12px; padding:20px;
        width:600px; max-width:90%; box-shadow:0 6px 12px rgba(0,0,0,0.15);
        animation:fadeIn 0.3s ease;
    }
    @keyframes fadeIn { from{opacity:0;transform:translateY(-10px);} to{opacity:1;transform:translateY(0);} }
    .dark-mode .modal-content { background:#1f2937; color:#f3f4f6; }
    .modal-content h2 { margin-bottom:16px; font-size:20px; }

    .form-group { display:flex; flex-direction:column; margin-bottom:12px; }
    .form-group label { font-size:14px; font-weight:500; margin-bottom:6px; }
    .form-group input, .form-group select {
        width:100%; padding:8px; border-radius:6px; border:1px solid #d1d5db;
    }
</style>

<div class="container">
    <!-- Thông báo -->
    <div id="notification"
        style="position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
        background:#22c55e; color:#fff; padding:16px 24px; border-radius:12px;
        display:none; font-weight:600; font-size:16px; z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,0.3);">
        <span id="notificationIcon">✔️</span> <span id="notificationText"></span>
    </div>

    <!-- Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1>Phòng thi</h1>
        <div style="position:relative; width:250px;">
            <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af;">🔍</span>
            <input type="text" id="searchInput" placeholder="Tìm kiếm Phòng thi..."
                style="width:100%; padding:8px 12px 8px 32px; border:1px solid #d1d5db; border-radius:8px;">
        </div>
    </div>

    <!-- Buttons -->
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn btn-primary" onclick="openModal('add')">+ Thêm mới</button>
        <button id="deleteBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th style="width:48px;"><input type="checkbox" id="checkAll"></th>
                <th>Tên Phòng</th>
                <th>Loại Phòng</th>
                <th>Sức chứa</th>
                <th>Vị trí</th>
                <th>Trạng thái</th>
                <th>Ngày cập nhật</th>
                <th>Ngày tạo</th>
                <th style="width:80px;">Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="roomTable">
            @foreach($rooms as $room)
            <tr>
                <td><input type="checkbox" class="row-check" data-id="{{ $room->id }}"></td>
                <td>{{ $room->name }}</td>
                <td>{{ $room->type }}</td>
                <td>{{ $room->capacity }}</td>
                <td>{{ $room->location }}</td>
                <td><span class="status {{ $room->status_id==1?'active':'inactive' }}">{{ $room->status_id==1?'Hoạt động':'Không hoạt động' }}</span></td>
                <td>{{ $room->updated_at->format('d-m-Y') }}</td>
                <td>{{ $room->created_at->format('d-m-Y') }}</td>
                <td>
                    <button class="action-icon"
                        onclick="openModal('edit','{{ $room->name }}','{{ $room->type }}','{{ $room->capacity }}','{{ $room->location }}','{{ $room->status_id }}','{{ $room->id }}')">
                        ⚙️
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="roomModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle">Thêm Phòng Thi</h2>
        <form id="roomForm" onsubmit="event.preventDefault(); saveRoom();">
    <input type="hidden" id="roomId">

    <div class="form-group">
        <label for="roomName">Tên Phòng Thi *</label>
        <input type="text" id="roomName" required placeholder="Nhập tên phòng thi">
    </div>

    <div class="form-group">
        <label for="roomType">Loại Phòng *</label>
        <select id="roomType" required>
            <option value="">-- Chọn loại phòng --</option>
            @foreach($roomTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="roomCapacity">Sức chứa *</label>
        <input type="number" id="roomCapacity" required placeholder="Nhập sức chứa phòng thi">
    </div>

    <div class="form-group">
        <label for="roomLocation">Vị trí *</label>
        <input type="text" id="roomLocation" required placeholder="Nhập vị trí phòng thi">
    </div>

    <div class="form-group">
        <label for="roomStatus">Trạng thái *</label>
        <select id="roomStatus" required>
            <option value="1">Hoạt động</option>
            <option value="0">Không hoạt động</option>
        </select>
    </div>
</form>

    </div>
</div>

<script>
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    const text = document.getElementById('notificationText');
    const icon = document.getElementById('notificationIcon');
    text.textContent = message;
    if(type === 'success'){ icon.textContent = '✔️'; notification.style.background = '#22c55e'; }
    else { icon.textContent = '❌'; notification.style.background = '#ef4444'; }
    notification.style.display = 'flex';
    notification.style.alignItems = 'center';
    notification.style.gap = '8px';
    setTimeout(() => { notification.style.display = 'none'; }, 3000);
}

const deleteBtn = document.getElementById('deleteBtn');
const checkAll = document.getElementById('checkAll');

function saveRoom() {
    const id = document.getElementById('roomId').value.trim();
    const name = document.getElementById('roomName').value.trim();
    const type = document.getElementById('roomType').value.trim();
    const capacity = document.getElementById('roomCapacity').value.trim();
    const location = document.getElementById('roomLocation').value.trim();
    const status_id = document.getElementById('roomStatus').value;

    fetch("{{ route('admin.phonghoc.store') }}", {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ id, name, type, capacity, location, status_id })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            closeModal();
            showNotification('Đã thêm/sửa phòng thành công!', 'success');
            window.location.reload(); // reload cho chắc
        } else if(data.errors) {
            const errors = Object.values(data.errors).flat().join(', ');
            showNotification(errors, 'error');
        } else {
            showNotification('Lưu thất bại!', 'error');
        }
    })
    .catch(err => { console.error(err); showNotification('Có lỗi xảy ra!', 'error'); });
}

// Bulk delete
deleteBtn.addEventListener('click', function() {
    const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.dataset.id);
    if(ids.length && confirm(`Bạn có chắc muốn xóa ${ids.length} mục?`)) {
        fetch("{{ route('admin.phonghoc.destroyMultiple') }}", {
            method:'DELETE',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({ ids })
        })
        .then(res=>res.json())
        .then(data=>{ if(data.success) window.location.reload(); });
    }
});

// Checkbox
function selectedCount(){ return document.querySelectorAll('.row-check:checked').length; }
function updateDeleteButton(){
    const n = selectedCount();
    if(n>0){
        deleteBtn.disabled=false;
        deleteBtn.classList.remove('btn-disabled');
        deleteBtn.classList.add('btn-secondary');
        deleteBtn.textContent=`Xóa ${n} mục`;
    } else {
        deleteBtn.disabled=true;
        deleteBtn.classList.add('btn-disabled');
        deleteBtn.classList.remove('btn-secondary');
        deleteBtn.textContent='Xóa 0 mục';
    }
}
document.querySelectorAll('.row-check').forEach(cb=>cb.addEventListener('change', updateDeleteButton));
checkAll.addEventListener('change', function(){
    document.querySelectorAll('.row-check').forEach(cb=>cb.checked=this.checked);
    updateDeleteButton();
});

// Search
document.getElementById('searchInput').addEventListener('keyup', function(){
    const keyword = this.value.toLowerCase().trim();
    document.querySelectorAll('#roomTable tr').forEach(row => {
        const cells = row.querySelectorAll('td'); 
        let match = false;
        cells.forEach(cell => {
            if(cell.textContent.toLowerCase().includes(keyword)) {
                match = true;
            }
        });
        row.style.display = match ? '' : 'none';
    });
});

// Modal
function openModal(type,name='',typeValue='',capacity='',roomLocation='',status_id='',id=''){
    const modal=document.getElementById('roomModal');
    const title=document.getElementById('modalTitle');
    const nameInput=document.getElementById('roomName');
    const typeInput=document.getElementById('roomType');
    const capacityInput=document.getElementById('roomCapacity');
    const locationInput=document.getElementById('roomLocation');
    const statusInput=document.getElementById('roomStatus');
    const hiddenId=document.getElementById('roomId');

    if(type==='add'){
        title.textContent='Thêm Phòng Thi';
        nameInput.value=''; typeInput.value=''; capacityInput.value=''; locationInput.value='';
        statusInput.value='1'; hiddenId.value='';
    } else {
        title.textContent='Sửa Phòng Thi';
        nameInput.value=name; typeInput.value=typeValue; capacityInput.value=capacity;
        locationInput.value=roomLocation; statusInput.value=status_id; hiddenId.value=id;
    }
    modal.style.display='flex';
}
function closeModal(){ document.getElementById('roomModal').style.display='none'; }
window.addEventListener('click', (e) => {
    const modal = document.getElementById('roomModal');
    if(e.target === modal) closeModal();
});
</script>
@endsection
