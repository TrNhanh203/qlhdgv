@extends('layouts.app')

@section('title', 'Quản lý Khoa/Viện')

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

<div class="container">
    <div id="toastFaculty" style="position:fixed; bottom:20px; right:20px; background:#16a34a; color:#fff; padding:12px 20px; border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.2); display:none; z-index:9999;"></div>
    <div style="margin-bottom:20px; text-align:center;">
        <h1 style="color:red; margin:0;">Khoa / Viện</h1>
    </div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        
        
    </div>
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn btn-primary" onclick="openModal('add')">+ Thêm mới</button>
            <button id="deleteBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>
        </div>
        <div style="position:relative; width:250px;">
            <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af;">🔍</span>
            <input type="text" id="searchInput" placeholder="Tìm kiếm khoa/viện..." style="width:100%; padding:8px 12px 8px 32px; border:1px solid #d1d5db; border-radius:8px; transition:all 0.3s;">
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:48px;"><input type="checkbox" id="checkAll" aria-label="Chọn tất cả"></th>
                <th>Tên Khoa / Viện</th>
                <th>Trạng thái</th>
                <th>Ngày cập nhật gần nhất</th>
                <th>Ngày tạo</th>
                <th style="width:80px;">Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="facultyTable">
            @foreach($faculties as $faculty)
            <tr>
                <td><input type="checkbox" class="row-check" aria-label="Chọn bản ghi" data-id="{{ $faculty->id }}"></td>
                <td>{{ $faculty->faculty_name }}</td>
                <td>
                    <span class="badge 
                        @if($faculty->status_id == 1) bg-success
                        @elseif($faculty->status_id == 2) bg-danger
                        @else bg-secondary
                        @endif">
                        {{ $faculty->status_id == 1 ? 'Đang Hoạt động' : ($faculty->status_id == 2 ? 'Không hoạt động' : 'Chưa xác định') }}
                    </span>
                </td>
                <td>{{ $faculty->updated_at->format('d-m-Y') }}</td>
                <td>{{ $faculty->created_at->format('d-m-Y') }}</td>
                <td>
                    <button class="action-icon" onclick="openModal('edit','{{ $faculty->faculty_name }}','{{ $faculty->status_id == 1 ? 'active':'inactive' }}','{{ $faculty->id }}')" title="Sửa">⚙️</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="container">
    
<div id="toastTk" style="position:fixed; bottom:20px; right:20px; background:#16a34a; color:#fff; padding:12px 20px; border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.2); display:none; z-index:9999;"></div>
    <div style="margin-bottom:20px; text-align:center;">
        <h1 style="color:red; margin:0;">🏫Trưởng Khoa</h1>
    </div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn btn-primary" onclick="openTkModal('add')">+ Thêm mới</button>
            <button id="deleteTkBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>
        </div>
        <div style="position:relative; width:250px;">
            <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af;">🔍</span>
            <input type="text" id="searchTkInput" placeholder="Tìm kiếm Trưởng Khoa/Viện..." style="width:100%; padding:8px 12px 8px 32px; border:1px solid #d1d5db; border-radius:8px; transition:all 0.3s;">
        </div>
    </div>

    

    <table>
        <thead>
            <tr>
                <th style="width:48px;"><input type="checkbox" id="checkAllTk"></th>
                <th>Họ và tên</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>Khoa phụ trách</th>
                <th>Trạng thái</th>
                <th style="width:80px;">Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="tkTable">
            @foreach($truongKhoa as $tk)
            <tr>
                <td><input type="checkbox" class="row-check-tk" data-id="{{ $tk->id }}"></td>
                <td>{{ $tk->lecture->full_name }}</td>
                <td>{{ $tk->lecture->email }}</td>
                <td>{{ $tk->lecture->phone }}</td>
                <td>{{ $tk->faculty->faculty_name ?? '---' }}</td>
                <td>
                    <span class="badge 
                        @if($tk->status_id == 11) bg-success
                        @elseif($tk->status_id == 12) bg-danger
                        @else bg-success
                        @endif">
                        {{ $tk->status_id == 11 ? 'Đã duyệt' : ($tk->status_id == 12 ? 'Chưa duyệt' : 'Chưa xác định') }}
                    </span>
                </td>

                <td>
                    <button class="action-icon" onclick="openTkModal('edit','{{ $tk->lecture_id }}','{{ $tk->faculty_id }}','{{ $tk->id }}')" 
    title="Sửa">⚙️</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Khoa -->
<div id="facultyModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-content">
        <h2 id="modalTitle">Thêm Khoa/Viện</h2>
        <form id="facultyForm" onsubmit="event.preventDefault(); saveFaculty();">
            <input type="hidden" id="facultyId">
            <div class="form-group">
                <label for="facultyName">Tên Khoa/Viện</label>
                <input type="text" id="facultyName" required placeholder="Nhập tên khoa/viện">
            </div>
            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" required>
                    <option value="1">Đang Hoạt động</option>
                    <option value="2">Không hoạt động</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Trưởng Khoa -->
<div id="tkModal" class="modal">
    <div class="modal-content">
        <h2 id="tkModalTitle">Thêm Trưởng Khoa</h2>
        <form id="tkForm" onsubmit="event.preventDefault(); saveTk();">
            <input type="hidden" id="tkId">
            <div class="form-group">
    <label for="tkLecture">Chọn Giảng Viên</label>
    <select id="tkLecture" class="select2" required onchange="updateFacultyByLecture()">
        <option value="">Chọn Giảng Viên</option>
        @foreach($lectures as $lecture)
            <option value="{{ $lecture->id }}" data-faculty="{{ $lecture->department->faculty_id ?? '' }}">
                {{ $lecture->full_name }} - {{ $lecture->email }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="tkFaculty">Khoa phụ trách</label>
    <select id="tkFaculty" required disabled>
        <option value="">Chọn Khoa</option>
        @foreach($faculties as $faculty)
            <option value="{{ $faculty->id }}">{{ $faculty->faculty_name }}</option>
        @endforeach
    </select>
</div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeTkModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
    // ==== Modal Trưởng Khoa ====
    const deleteTkBtn = document.getElementById('deleteTkBtn');
    const checkAllTk = document.getElementById('checkAllTk');
    function updateFacultyByLecture() {
    const lectureSelect = document.getElementById('tkLecture');
    const facultySelect = document.getElementById('tkFaculty');
    
    const selectedOption = lectureSelect.selectedOptions[0];
    if(selectedOption && selectedOption.dataset.faculty) {
        facultySelect.value = selectedOption.dataset.faculty;
    } else {
        facultySelect.value = '';
    }
}

    function openTkModal(type, name='', facultyId='', id=''){
        const modal = document.getElementById('tkModal');
        const title = document.getElementById('tkModalTitle');
        const lectureSelect = document.getElementById('tkLecture');
        const facultySelect = document.getElementById('tkFaculty');
        const hiddenId = document.getElementById('tkId');

        if(type==='add'){
            title.textContent='Thêm Trưởng Khoa';
            lectureSelect.value='';
            facultySelect.value='';
            hiddenId.value='';
        } else {
            title.textContent='Sửa Trưởng Khoa';
            lectureSelect.value = name; // chính là lecture_id
            facultySelect.value = facultyId;
            hiddenId.value = id;
            filterLecturesByFaculty(facultyId, parseInt(id));
        }

        modal.style.display='flex';
    }

     function closeTkModal(){ document.getElementById('tkModal').style.display='none'; }
    function filterLecturesByFaculty(facultyId, lectureId = null){
    $('#tkLecture option').each(function(){
        const isOriginal = $(this).data('original') === "1";
        if(!isOriginal) return;
        const optVal = parseInt($(this).val());
        if(optVal === lectureId) $(this).show();
        else $(this).hide();
    });
}

    function saveTk(){
        const id=document.getElementById('tkId').value;
        const lecture_id=document.getElementById('tkLecture').value;
        const faculty_id=document.getElementById('tkFaculty').value;
        if(!lecture_id || !faculty_id) return alert('Vui lòng điền đầy đủ thông tin!');
        const payload={lecture_id, faculty_id};
        if(id) payload.id=parseInt(id);

        fetch("{{ route('admin.truongkhoa.store') }}", {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body:JSON.stringify(payload)
        })
        .then(res=>res.json())
        .then(data=>{ if(data.success) location.reload(); else alert('Lưu thất bại!'); })
        .catch(err=>console.error(err));
    }

    deleteTkBtn.addEventListener('click', ()=>{
        const ids=Array.from(document.querySelectorAll('.row-check-tk:checked')).map(cb=>parseInt(cb.dataset.id));
        if(ids.length && confirm(`Bạn có chắc muốn xóa ${ids.length} mục?`)){
            fetch("{{ route('admin.truongkhoa.deleteMultiple') }}", {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body:JSON.stringify({ids})
            })
            .then(res=>res.json())
            .then(data=>{ if(data.success) location.reload(); })
            .catch(err=>console.error(err));
        }
    });

    function updateDeleteTkButton(){
        const n=document.querySelectorAll('.row-check-tk:checked').length;
        if(n>0){
            deleteTkBtn.disabled=false;
            deleteTkBtn.classList.remove('btn-disabled');
            deleteTkBtn.classList.add('btn-secondary');
            deleteTkBtn.textContent=`Xóa ${n} mục`;
        }else{
            deleteTkBtn.disabled=true;
            deleteTkBtn.classList.add('btn-disabled');
            deleteTkBtn.classList.remove('btn-secondary');
            deleteTkBtn.textContent='Xóa 0 mục';
        }
    }
    document.querySelectorAll('.row-check-tk').forEach(cb=>cb.addEventListener('change', updateDeleteTkButton));
    checkAllTk.addEventListener('change', function(){
        document.querySelectorAll('.row-check-tk').forEach(cb=>cb.checked=this.checked);
        updateDeleteTkButton();
    });
    window.addEventListener('click',(e)=>{ if(e.target===document.getElementById('tkModal')) closeTkModal(); });

    // Lưu tất cả option gốc của tkLecture để lọc sau này
    document.querySelectorAll('#tkLecture option').forEach(opt => {
        if(opt.value) opt.dataset.original = "1";
    });
    // ==== Modal Khoa ====
    const deleteBtn = document.getElementById('deleteBtn');
    const checkAll = document.getElementById('checkAll');

    function saveFaculty() {
    const id = document.getElementById('facultyId').value;
    const name = document.getElementById('facultyName').value.trim();
    const status_id = parseInt(document.getElementById('status').value);

    if (!name) { alert('Vui lòng nhập tên Khoa/Viện!'); return; }

    const payload = { faculty_name: name, status_id: status_id };
    if (id) payload.id = parseInt(id);

    fetch("{{ route('admin.khoa.store') }}", {
        method: 'POST',
        headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { closeModal(); location.reload(); }
        else { alert('Lưu thất bại!\n' + (data.message || JSON.stringify(data.errors))); }
    })
    .catch(err => { console.error(err); alert('Có lỗi xảy ra!\n' + err.message); });
}


    function deleteFaculty(id) {
        if (!confirm('Bạn có chắc muốn xóa mục này?')) return;
        fetch(`{{ url('admin/khoa') }}/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'} })
        .then(res => res.json()).then(data => { if(data.success) location.reload(); }).catch(err => console.error(err));
    }

    deleteBtn.addEventListener('click', function() {
        const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(cb => parseInt(cb.dataset.id));
        if (ids.length && confirm(`Bạn có chắc muốn xóa ${ids.length} mục?`)) {
            fetch("{{ route('admin.khoa.destroyMultiple') }}", {
                method:'DELETE',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body:JSON.stringify({ ids })
            }).then(res => res.json()).then(data => { if(data.success) location.reload(); }).catch(err => console.error(err));
        }
    });

    function selectedCount() { return document.querySelectorAll('.row-check:checked').length; }
    function updateDeleteButton() {
        const n = selectedCount();
        if (n > 0) { deleteBtn.disabled=false; deleteBtn.classList.remove('btn-disabled'); deleteBtn.classList.add('btn-secondary'); deleteBtn.textContent=`Xóa ${n} mục`; }
        else { deleteBtn.disabled=true; deleteBtn.classList.add('btn-disabled'); deleteBtn.classList.remove('btn-secondary'); deleteBtn.textContent='Xóa 0 mục'; }
    }

    document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', () => {
        const all = document.querySelectorAll('.row-check').length;
        checkAll.checked = (selectedCount() === all);
        updateDeleteButton();
    }));
    checkAll.addEventListener('change', function(){ document.querySelectorAll('.row-check').forEach(cb => cb.checked=this.checked); updateDeleteButton(); });

    function openModal(type, facultyName = '', status = 1, id = '') {
        const modal = document.getElementById('facultyModal');
        const title = document.getElementById('modalTitle');
        const nameInput = document.getElementById('facultyName');
        const statusInput = document.getElementById('status');
        const hiddenId = document.getElementById('facultyId');

        if(type === 'add') {
            title.textContent = 'Thêm Khoa/Viện';
            nameInput.value = '';
            statusInput.value = 1;
            hiddenId.value = '';
        } else {
            title.textContent = 'Sửa Khoa/Viện';
            nameInput.value = facultyName;
            statusInput.value = status;
            hiddenId.value = id;
        }

        modal.style.display = 'flex';
    }

    function closeModal() { document.getElementById('facultyModal').style.display = 'none'; }
    window.addEventListener('click', (e) => { if(e.target === document.getElementById('facultyModal')) closeModal(); });
</script>
<script>
    const existingTk = [
        @foreach($truongKhoa as $tk)
            { lecture_id: {{ $tk->lecture_id }}, faculty_id: {{ $tk->faculty_id }} },
        @endforeach
    ];
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        placeholder: 'Chọn Giảng Viên',
        allowClear: true
    });
});
</script>
<script>
    // ==== Tìm kiếm realtime Khoa/Viện ====
    const searchInput = document.getElementById('searchInput');
    const facultyTable = document.getElementById('facultyTable');

    searchInput.addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase(); 
        const rows = facultyTable.querySelectorAll('tr');

        rows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)'); 
            if (nameCell) {
                const text = nameCell.textContent.toLowerCase();
                if (text.includes(keyword)) {
                    row.style.display = ''; 
                } else {
                    row.style.display = 'none'; 
                }
            }
        });
    });



    // ==== Tìm kiếm realtime Trưởng Khoa ====
const searchTkInput = document.getElementById('searchTkInput');
const tkTable = document.getElementById('tkTable');

searchTkInput.addEventListener('keyup', function () {
    const keyword = this.value.toLowerCase();
    const rows = tkTable.querySelectorAll('tr');

    rows.forEach(row => {
        const nameCell = row.querySelector('td:nth-child(2)');
        const emailCell = row.querySelector('td:nth-child(3)');
        const facultyCell = row.querySelector('td:nth-child(5)');

        const text = (
            (nameCell?.textContent || '') + ' ' +
            (emailCell?.textContent || '') + ' ' +
            (facultyCell?.textContent || '')
        ).toLowerCase();

        row.style.display = text.includes(keyword) ? '' : 'none';
    });
});

</script>

@endsection
