@extends('layouts.app')

@section('title', 'Quản lý Bộ môn & Trưởng Bộ Môn')

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

    .btn-primary:hover { background: #1d4ed8; }
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
        vertical-align: middle;
    }
    table th { background: #f1f5f9; font-weight: 600; }

    .dark-mode table th { background: #374151; color: #f3f4f6; }
    .dark-mode table td { border-bottom: 1px solid #374151; color: #f3f4f6; }

    .action-icon { background: none; border: none; font-size: 18px; cursor: pointer; }

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
        margin-bottom: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-actions {
        text-align: right;
        margin-top: 12px;
    }

    .btn {
        padding: 6px 14px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-primary {
        background: #2563eb;
        color: #fff;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #111827;
        margin-right: 6px;
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

    .modal-actions {
        margin-top: 16px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    body.dark-mode .container { background: #2c2c3e; color: #f1f1f1; box-shadow: 0 6px 18px rgba(0,0,0,0.35); }
    body.dark-mode h1 { color: #f3f4f6; }
    body.dark-mode table tbody tr:hover { background: #2b3443; }
</style>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

{{-- ===================== BỘ MÔN ===================== --}}
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1>Bộ môn</h1>
        <input type="text" id="searchDepartment" placeholder="Tìm kiếm bộ môn..." style="padding:8px 12px; border-radius:8px; border:1px solid #d1d5db;">
    </div>
    <div style="display:flex; gap:10px; margin-bottom:10px;">
        <button class="btn btn-primary" onclick="openDepartmentModal('add')">+ Thêm mới</button>
        <button id="deleteDepartmentBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>
    </div>
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="checkAllDepartment"></th>
                <th>Mã Bộ môn</th>
                <th>Tên Bộ môn</th>
                <th>Thuộc Khoa</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th>Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="departmentTable">
            @foreach($departments as $dep)
            <tr>
                <td><input type="checkbox" class="row-check-department" data-id="{{ $dep->id }}"></td>
                <td>{{ $dep->department_code }}</td>
                <td>{{ $dep->department_name }}</td>
                <td>{{ $dep->faculty->faculty_name ?? '' }}</td>
                <td>
                    <span class="badge 
                        @if($dep->status_id == 1) bg-success
                        @elseif($dep->status_id == 2) bg-danger
                        @else bg-secondary
                        @endif">
                        {{ $dep->status_id == 1 ? 'Đang Hoạt động' : ($dep->status_id == 2 ? 'Không hoạt động' : 'Chưa xác định') }}
                    </span>
                </td>
                <td>{{ $dep->created_at?->format('d-m-Y') }}</td>
                <td>{{ $dep->updated_at?->format('d-m-Y') }}</td>
                <td><button class="action-icon" onclick="openDepartmentModal('edit','{{ $dep->id }}','{{ $dep->department_code }}','{{ $dep->department_name }}','{{ $dep->faculty_id }}','{{ $dep->status_id }}')">⚙️</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $departments->links('pagination::bootstrap-5') }}</div>
</div>

{{-- ===================== TRƯỞNG BỘ MÔN ===================== --}}
<div class="container mt-5">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1 style="color:red;">Trưởng Bộ Môn</h1>
        <input type="text" id="searchHead" placeholder="Tìm kiếm Trưởng Bộ Môn..." style="padding:8px 12px; border-radius:8px; border:1px solid #d1d5db;">
    </div>
    <div style="display:flex; gap:10px; margin-bottom:10px;">
        <button class="btn btn-primary" onclick="openHeadModal('add')">+ Thêm mới</button>
        <button id="deleteHeadBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>
    </div>
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="checkAllHead"></th>
                <th>Họ và tên</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>Bộ môn</th>
                <th>Khoa</th>
                <th>Trạng thái</th>
                <th>Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="headTable">
            @foreach($truongBoMon as $tbm)
            <tr>
                <td><input type="checkbox" class="row-check-head" data-id="{{ $tbm->id }}"></td>
                <td>{{ $tbm->lecture->full_name }}</td>
                <td>{{ $tbm->lecture->email }}</td>
                <td>{{ $tbm->lecture->phone }}</td>
                <td>{{ $tbm->department->department_name ?? '' }}</td>
                <td>{{ $tbm->faculty->faculty_name ?? ($tbm->department->faculty->faculty_name ?? '') }}</td>
                <td>
                    <span class="badge 
                        @if($tbm->status_id == 11) bg-success
                        @elseif($tbm->status_id == 12) bg-danger
                        @else bg-success
                        @endif">
                        {{ $tbm->status_id == 11 ? 'Đã duyệt' : ($tbm->status_id == 12 ? 'Chưa duyệt' : 'Chưa xác định') }}
                    </span>
                </td>

                
                <td><button class="action-icon" onclick="openHeadModal('edit','{{ $tbm->id }}')">⚙️</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $truongBoMon->links('pagination::bootstrap-5') }}</div>
</div>

{{-- ===================== MODAL BỘ MÔN ===================== --}}
<div id="departmentModal" class="modal">
    <div class="modal-content">
        <h2 id="departmentModalTitle">Thêm Bộ môn</h2>
        <form id="departmentForm" onsubmit="event.preventDefault(); saveDepartment();">
            <input type="hidden" id="departmentId">
            <div class="form-group">
                <label>Mã Bộ môn</label>
                <input type="text" id="departmentCode" required>
            </div>
            <div class="form-group">
                <label>Tên Bộ môn</label>
                <input type="text" id="departmentName" required>
            </div>
            <div class="form-group">
                <label>Thuộc Khoa</label>
                <select id="departmentFaculty" required>
                    <option value="">-- Chọn Khoa --</option>
                    @foreach($faculties as $f)
                        <option value="{{ $f->id }}">{{ $f->faculty_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Trạng thái</label>
                <select id="departmentStatus" required>
                    <option value="1">Đang hoạt động</option>
                    <option value="2">Không hoạt động</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeDepartmentModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>
{{-- ===================== MODAL TRƯỞNG BỘ MÔN ===================== --}}
<div id="headModal" class="modal">
    <div class="modal-content">
        <h2 id="headModalTitle">Thêm Trưởng Bộ Môn</h2>
        <form id="headForm" onsubmit="event.preventDefault(); saveHead();">
            <input type="hidden" id="headId">
            <input type="hidden" id="headLectureId">
            <div class="form-grid">

                {{-- Dropdown chọn giảng viên --}}
                <div class="form-group" id="lectureSelectGroup">
                    <label>Chọn Giảng Viên <span class="required">*</span></label>
                    <select id="headLecture">
                        <option value="">-- Chọn Giảng Viên --</option>
                        @foreach($lectures as $lec)
                            <option value="{{ $lec->id }}" 
                                data-department="{{ $lec->department_id ?? '' }}"
                                data-faculty="{{ $lec->faculty_id ?? '' }}">
                                {{ $lec->full_name }} - {{ $lec->email }}
                            </option>
                        @endforeach
                    </select>

                </div>

                {{-- Input readonly hiển thị khi edit --}}
                <div class="form-group" id="lectureInputGroup" style="display:none;">
                    <label>Giảng Viên</label>
                    <input type="text" id="headLectureName" readonly>
                </div>

                {{-- Bộ môn dropdown (disabled) --}}
                <div class="form-group" id="departmentSelectGroup">
                    <label>Bộ môn</label>
                    <select id="headDepartmentDisabled">
                        <option value="">-- Chọn Bộ môn --</option>
                        @foreach($departments as $dep)
                            <option value="{{ $dep->id }}" data-faculty="{{ $dep->faculty_id }}">{{ $dep->department_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Input readonly hiển thị khi edit --}}
                <div class="form-group" id="departmentInputGroup" style="display:none;">
                    <label>Bộ môn</label>
                    <input type="text" id="headDepartmentName" readonly>
                </div>

                {{-- Khoa dropdown (disabled) --}}
                <div class="form-group" id="facultySelectGroup">
                    <label>Khoa</label>
                    <select id="headFaculty" disabled>
                        <option value="">-- Chọn Khoa --</option>
                        @foreach($faculties as $f)
                            <option value="{{ $f->id }}">{{ $f->faculty_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Input readonly hiển thị khi edit --}}
                <div class="form-group" id="facultyInputGroup" style="display:none;">
                    <label>Khoa</label>
                    <input type="text" id="headFacultyName" readonly>
                </div>

                {{-- Trạng thái --}}
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select id="headStatus" required>
                        <option value="11">Đã duyệt</option>
                        <option value="12">Chưa duyệt</option>
                    </select>
                </div>

            </div>

            {{-- Hidden input để lưu --}}
            <input type="hidden" id="headDepartment">

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeHeadModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>



<script>
/* ===================== BỘ MÔN ===================== */
const checkAllDepartment = document.getElementById('checkAllDepartment');
const deleteDepartmentBtn = document.getElementById('deleteDepartmentBtn');
function updateDeleteDepartmentBtn(){
    const n = document.querySelectorAll('.row-check-department:checked').length;
    deleteDepartmentBtn.disabled = n===0;
    deleteDepartmentBtn.classList.toggle('btn-disabled', n===0);
    deleteDepartmentBtn.classList.toggle('btn-secondary', n>0);
    deleteDepartmentBtn.textContent = `Xóa ${n} mục`;
}
document.querySelectorAll('.row-check-department').forEach(cb=>cb.addEventListener('change', updateDeleteDepartmentBtn));
checkAllDepartment.addEventListener('change', function(){
    document.querySelectorAll('.row-check-department').forEach(cb=>cb.checked=this.checked);
    updateDeleteDepartmentBtn();
});
deleteDepartmentBtn.addEventListener('click', function(){
    const ids = Array.from(document.querySelectorAll('.row-check-department:checked')).map(cb=>cb.dataset.id);
    if(!ids.length || !confirm(`Xóa ${ids.length} bộ môn?`)) return;
    fetch("{{ route('admin.bomon.destroyMultiple') }}", {
    method:'POST',
    headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN':'{{ csrf_token() }}'
    },
    body: JSON.stringify({ ids })
})

    .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
});
function openDepartmentModal(type,id='',code='',name='',faculty='',status='1'){
    document.getElementById('departmentModalTitle').textContent = type==='add'?'Thêm Bộ môn':'Sửa Bộ môn';
    document.getElementById('departmentId').value=id;
    document.getElementById('departmentCode').value=code;
    document.getElementById('departmentName').value=name;
    document.getElementById('departmentFaculty').value=faculty;
    document.getElementById('departmentStatus').value=status;
    document.getElementById('departmentModal').style.display='flex';
}
function closeDepartmentModal(){ document.getElementById('departmentModal').style.display='none'; }
function saveDepartment(){
    const id=document.getElementById('departmentId').value;
    const code=document.getElementById('departmentCode').value;
    const name=document.getElementById('departmentName').value;
    const faculty_id=document.getElementById('departmentFaculty').value;
    const status_id=document.getElementById('departmentStatus').value;
    fetch("{{ route('admin.bomon.store') }}", { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}, body:JSON.stringify({id,department_code:code,department_name:name,faculty_id,status_id}) })
    .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); else alert(d.message||'Lưu thất bại'); });
}
/* Search */
// ==== Tìm kiếm realtime Bộ môn ====
document.getElementById('searchDepartment').addEventListener('keyup', function(){
    const keyword = this.value.toLowerCase();
    const rows = document.querySelectorAll('#departmentTable tr');

    rows.forEach(row => {
        const codeCell = row.querySelector('td:nth-child(2)');
        const nameCell = row.querySelector('td:nth-child(3)');
        const facultyCell = row.querySelector('td:nth-child(4)');

        const text = (
            (codeCell?.textContent || '') + ' ' +
            (nameCell?.textContent || '') + ' ' +
            (facultyCell?.textContent || '')
        ).toLowerCase();

        row.style.display = text.includes(keyword) ? '' : 'none';
    });
});



</script>

<script>
/* ===================== TRƯỞNG BỘ MÔN ===================== */
const checkAllHead = document.getElementById('checkAllHead');
const deleteHeadBtn = document.getElementById('deleteHeadBtn');

/* Cập nhật nút Xóa */
function updateDeleteHeadBtn() {
    const n = document.querySelectorAll('.row-check-head:checked').length;
    deleteHeadBtn.disabled = n === 0;
    deleteHeadBtn.classList.toggle('btn-disabled', n === 0);
    deleteHeadBtn.classList.toggle('btn-secondary', n > 0);
    deleteHeadBtn.textContent = `Xóa ${n} mục`;
}

/* Checkbox chọn từng row */
document.querySelectorAll('.row-check-head').forEach(cb => cb.addEventListener('change', updateDeleteHeadBtn));

/* Checkbox chọn tất cả */
checkAllHead.addEventListener('change', function() {
    document.querySelectorAll('.row-check-head').forEach(cb => cb.checked = this.checked);
    updateDeleteHeadBtn();
});

/* Xóa nhiều Trưởng Bộ Môn */
deleteHeadBtn.addEventListener('click', function() {
    const ids = Array.from(document.querySelectorAll('.row-check-head:checked')).map(cb => cb.dataset.id);
    if (!ids.length || !confirm(`Xóa ${ids.length} Trưởng Bộ Môn?`)) return;

    fetch("{{ route('admin.truongbomon.destroyMultiple') }}", {
        method: 'POST', // route phải nhận POST
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ ids })
    })
    .then(res => res.json())
    .then(data => { 
        if (data.success) location.reload(); 
        else alert('Không thể xóa!'); 
    })
    .catch(err => console.error(err));
});

/* Mở modal Add / Edit */
function openHeadModal(type, id = '') {
    const isEdit = type === 'edit' && id;

    // Reset form
    document.getElementById('headModalTitle').textContent = isEdit ? 'Sửa Trưởng Bộ Môn' : 'Thêm Trưởng Bộ Môn';
    document.getElementById('headId').value = '';
    document.getElementById('headLecture').value = '';
    document.getElementById('headLectureId').value = ''; // reset
    document.getElementById('headFaculty').value = '';
    document.getElementById('headDepartment').value = '';
    document.getElementById('headDepartmentDisabled').innerHTML = '';
    document.getElementById('headStatus').value = '11';

    document.getElementById('headModal').style.display = 'flex';

    if (!isEdit) {
        // ADD mới
        document.getElementById('lectureSelectGroup').style.display = 'block';
        document.getElementById('departmentSelectGroup').style.display = 'block';
        document.getElementById('facultySelectGroup').style.display = 'block';
        document.getElementById('lectureInputGroup').style.display = 'none';
        document.getElementById('departmentInputGroup').style.display = 'none';
        document.getElementById('facultyInputGroup').style.display = 'none';
    } else {
        // EDIT: load dữ liệu từ server
        fetch(`/admin/bomon/getTruongBoMon/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(d => {
            if (!d.success) { alert(d.message || 'Không load dữ liệu'); return; }
            const h = d.data;

            document.getElementById('headId').value = h.id;
            document.getElementById('headLectureName').value = h.lecture?.full_name ?? '';
            document.getElementById('headLectureId').value = h.lecture_id; // **lecture_id hidden**
            document.getElementById('headStatus').value = h.status_id;

            // Hiển thị dropdown Bộ môn và Khoa
            document.getElementById('lectureSelectGroup').style.display = 'none';
            document.getElementById('lectureInputGroup').style.display = 'block';
            document.getElementById('departmentSelectGroup').style.display = 'block';
            document.getElementById('departmentInputGroup').style.display = 'none';
            document.getElementById('facultySelectGroup').style.display = 'block';
            document.getElementById('facultyInputGroup').style.display = 'none';

            // Load danh sách Bộ môn thuộc cùng Khoa
            const depSelect = document.getElementById('headDepartmentDisabled');
            depSelect.innerHTML = '';
            const facultyId = h.faculty_id || h.department?.faculty_id;
            const departmentsOfFaculty = @json($departments->groupBy('faculty_id'));

            if(departmentsOfFaculty[facultyId]){
                departmentsOfFaculty[facultyId].forEach(dep => {
                    const opt = document.createElement('option');
                    opt.value = dep.id;
                    opt.textContent = dep.department_name;
                    if(dep.id === h.department_id) opt.selected = true;
                    depSelect.appendChild(opt);
                });
            }

            document.getElementById('headDepartment').value = h.department_id;
            document.getElementById('headFaculty').value = facultyId;
        })
        .catch(err => alert("Không load được dữ liệu: " + err.message));
    }
}

document.getElementById('headDepartmentDisabled').addEventListener('change', function(){
    document.getElementById('headDepartment').value = this.value;
});


/* Khi chọn giảng viên (chỉ dùng add mới) */
document.getElementById('headLecture').addEventListener('change', function() {
    const sel = this.selectedOptions[0];
    const dep = sel?.dataset?.department || '';
    const fac = sel?.dataset?.faculty || '';
    document.getElementById('headFaculty').value = fac;
    document.getElementById('headDepartment').value = dep;
    document.getElementById('headDepartmentDisabled').value = dep;
});

/* Đóng modal */
function closeHeadModal() { document.getElementById('headModal').style.display = 'none'; }

/* Lưu Add / Edit Trưởng Bộ Môn */
function saveHead() {
    const id = document.getElementById('headId').value;

    // Nếu edit, lấy lecture_id từ hidden; nếu add, lấy từ select
    let lecture_id = document.getElementById('headLecture').value || document.getElementById('headLectureId').value;

    const department_id = document.getElementById('headDepartment').value;
    const faculty_id = document.getElementById('headFaculty').value;
    const status_id = document.getElementById('headStatus').value;

    if (!lecture_id) {
        alert('Vui lòng chọn giảng viên.');
        return;
    }

    fetch("{{ route('admin.truongbomon.store') }}", {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json', 
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id, lecture_id, department_id, faculty_id, status_id })
    })
    .then(res => res.json())
    .then(d => {
        if(d.success) location.reload();
        else alert(d.message || 'Lưu thất bại');
    })
    .catch(err => alert("Lỗi: " + err.message));
}


/* Search realtime */
document.getElementById('searchHead').addEventListener('keyup', function() {
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('#headTable tr').forEach(row => {
        const text = Array.from(row.querySelectorAll('td')).slice(1, 6)
            .map(td => td.textContent.toLowerCase()).join(' ');
        row.style.display = text.includes(keyword) ? '' : 'none';
    });
});
</script>



@endsection
