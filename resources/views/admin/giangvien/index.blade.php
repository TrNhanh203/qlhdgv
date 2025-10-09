@extends('layouts.app')

@section('title', 'Quản lý Giảng viên')

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
    width: 900px;         /* tăng từ 600px lên 900px */
    max-width: 95%;       /* cho responsive */
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    animation: fadeIn 0.3s ease;
}

    .dark-mode .modal-content { background: #1f2937; color: #f3f4f6; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    .modal-content h2 { margin-bottom: 16px; font-size: 20px; }

    .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr; 
    gap: 30px;            /* tăng khoảng cách cột */
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

    .form-group input,
    .form-group select {
            width: 100%;  
            padding: 12px;        /* padding rộng hơn */
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 15px;      /* chữ to hơn một chút */
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
    <h1>Giảng Viên</h1>
        <div style="position:relative; width:250px;">
            <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af;">🔍</span>
            <input type="text" id="searchInput" placeholder="Tìm kiếm giảng viên..." style="width:100%; padding:8px 12px 8px 32px; border:1px solid #d1d5db; border-radius:8px; transition:all 0.3s;">
        </div>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn btn-success" onclick="openModal('add')">+ Thêm mới</button>
        <button id="deleteBtn" class="btn btn-danger" disabled>Xóa 0 mục</button>
        <form action="{{ route('admin.giangvien.import') }}" method="POST" enctype="multipart/form-data" style="display:inline-flex; gap:5px; align-items:center;">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls" required>
                <button type="submit" class="btn btn-primary">📥 Import Excel</button>
            </form>
            <a href="{{ route('admin.giangvien.export') }}" class="btn btn-secondary">📤 Export Excel</a>
    </div>
    {{-- 🔔 Thêm thông báo lỗi và thành công ở đây --}}
        @if(session('errors') && count(session('errors')) > 0)
            <div class="alert alert-warning" style="margin-top:15px;">
                <h5>⚠️ Một số dòng bị lỗi:</h5>
                <ul>
                    @foreach(session('errors') as $error)
                        @if(is_array($error))
                            <li>Dòng {{ $error['row'] }}: {{ $error['message'] }}</li>
                        @else
                            <li>{{ $error }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif


        @if(session('success'))
            <div class="alert alert-success" style="margin-top: 15px;">
                {{ session('success') }}
            </div>
        @endif

    <!-- Bảng dữ liệu -->
    <table>
        <thead>
            <tr>
                <th style="width:48px;"><input type="checkbox" id="checkAll"></th>
                <th>Mã GV</th>
                <th>Họ tên</th>
                <th>Học vị</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>Bộ môn</th>
                <th>Trạng thái</th>
                <th>Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="GiangvienTable">
            @foreach ($lectures as $lecture)
            <tr>
                <td><input type="checkbox" class="row-check" value="{{ $lecture->id }}"></td>
                <td>{{ $lecture->lecturer_code }}</td>
                <td>{{ $lecture->full_name }}</td>
                <td>{{ $lecture->degree }}</td>
                <td>{{ $lecture->email }}</td>
                <td>{{ $lecture->phone }}</td>
                <td>{{ $lecture->department?->department_name }}</td>
                <td>
                @php
                    $statusId = $lecture->status?->id ?? null;
                @endphp
                <span class="badge 
                    @if($statusId == 8) bg-success       {{-- Đang công tác --}}
                    @elseif($statusId == 9) bg-danger   {{-- Tạm nghỉ --}}
                    @elseif($statusId == 10) bg-danger    {{-- Chuyển công tác --}}
                    @else bg-secondary                    {{-- Các trạng thái khác --}}
                    @endif">
                    {{ $lecture->status?->name ?? 'Chưa xác định' }}
                </span>
            </td>


                <td>
            <button class="action-icon"  
    onclick='openModal("edit", @json($lecture))'>⚙️</button>
        </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-3">
        {{ $lectures->links('pagination::bootstrap-5') }}
    </div>
<!-- Danh sách Trưởng Khoa -->
 




<!-- Modal thêm/sửa -->
<div id="giangvienModal" class="modal" style="display:none;">
    <div class="modal-dialog">
        <form id="giangvienForm" method="POST" action="{{ route('admin.giangvien.store') }}">
            @csrf
            <input type="hidden" name="id" id="giangvienId">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm/Sửa Giảng viên</h5>
                    <button type="button" class="btn-close" onclick="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Mã Giảng viên <span class="required">*</span></label>
                                <input type="text" name="lecturer_code" id="lecturer_code" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Họ tên <span class="required">*</span></label>
                                <input type="text" name="full_name" id="full_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Số điện thoại <span class="required">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Học vị</label>
                                <input type="text" name="degree" id="degree" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label>Bộ môn <span class="required">*</span></label>
                                <select name="department_id" id="department_id" class="form-control" required>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Trạng thái <span class="required">*</span></label>
                                <select name="status_id" id="status_id" class="form-control" required>
                                    <option value="8">Đang công tác</option>
                                    <option value="9">Tạm nghỉ</option>
                                    <option value="10">Chuyển công tác</option>
                                </select>
                            </div>
                        </div>
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

<!-- Script -->
<script>
    const modal = document.getElementById('giangvienModal');
    const form  = document.getElementById('giangvienForm');
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.row-check');
    const deleteBtn = document.getElementById('deleteBtn');

    function openModal(mode, data = null) {
    modal.style.display = 'block';

    // remove any leftover _method input and reset action/method
    const oldMethod = form.querySelector('input[name="_method"]');
    if (oldMethod) oldMethod.remove();
    form.method = "POST";
    form.action = "{{ route('admin.giangvien.store') }}";

    if (mode === 'edit' && data) {
        document.querySelector('.modal-title').innerText = 'Sửa Giảng viên';

        document.getElementById('giangvienId').value = data.id;
        document.getElementById('lecturer_code').value = data.lecturer_code ?? '';
        document.getElementById('full_name').value = data.full_name ?? '';
        document.getElementById('email').value = data.email ?? '';
        document.getElementById('phone').value = data.phone ?? '';
        document.getElementById('degree').value = data.degree ?? '';
        document.getElementById('department_id').value = data.department_id ?? '';
        document.getElementById('status_id').value = data.status_id ?? '';
    } else {
        document.querySelector('.modal-title').innerText = 'Thêm Giảng viên';
        form.reset();
        document.getElementById('giangvienId').value = '';
    }
}



    function closeModal() { modal.style.display = 'none'; }

    // Tìm kiếm
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase().trim();
        document.querySelectorAll('#GiangvienTable tr').forEach(row => {
            const nameCell = row.querySelector('td:nth-child(3)');
            row.style.display = (nameCell && nameCell.textContent.toLowerCase().includes(keyword)) ? '' : 'none';
        });
    });

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
        if(!confirm(`Bạn có chắc muốn xóa ${ids.length} giảng viên?`)) return;

        fetch("{{ route('admin.giangvien.destroyMultiple') }}", {
            method: 'POST',  // đổi từ DELETE sang POST
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
