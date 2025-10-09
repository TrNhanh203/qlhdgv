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


{{-- ===================== BẢNG LOẠI KỲ THI ===================== --}}
<div class="container">
    <h1>Loại Kỳ Thi</h1>
    
    <div style="display:flex; gap:10px; margin-bottom:10px;">
        <button class="btn btn-primary" onclick="openExamTypeModal('add')">+ Thêm mới</button>
        <button id="deleteExamTypeBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>
    <div style="margin-bottom:10px;">
    <input type="text" id="searchExamType" placeholder="Tìm kiếm Loại Kỳ Thi..." style="width:300px; padding:8px; border-radius:6px; border:1px solid #d1d5db;">
</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="checkAllExamType"></th>
                <th>Tên Loại Kỳ Thi</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th>Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="examTypeTable">
           @foreach($examTypes as $type)
            <tr>
                <td><input type="checkbox" class="row-check-exam-type" data-id="{{ $type['code'] }}"></td>
                <td>{{ $type['name'] }}</td>
            <td>
                @php $statusId = $type['status_id'] ?? null; @endphp
                <span class="badge
                    @if($statusId == 1) bg-success
                    @elseif($statusId == 2) bg-danger
                    @else bg-secondary
                    @endif">
                    {{ $statusId == 1 ? 'Đang hoạt động' : ($statusId == 2 ? 'Không hoạt động' : 'Chưa xác định') }}
                </span>
            </td>

            <td>{{ $type['created_at'] ?? '-' }}</td>
            <td>{{ $type['updated_at'] ?? '-' }}</td>
            <td>
                <button class="action-icon" onclick="openExamTypeModal('edit','{{ $type['code'] }}','{{ $type['name'] }}','{{ $type['status_id'] ?? 1 }}')">⚙️</button>
            </td>
            </tr>
            @endforeach



        </tbody>
    </table>
    <div class="mt-3">
        {{ $examTypes->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- ===================== BẢNG HÌNH THỨC THI ===================== --}}
<div class="container">
    <h1>Hình Thức Thi</h1>
    <div style="display:flex; gap:10px; margin-bottom:10px;">
        <button class="btn btn-primary" onclick="openExamFormModal('add')">+ Thêm mới</button>
        <button id="deleteExamFormBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>
    <div style="margin-bottom:10px;">
    <input type="text" id="searchExamForm" placeholder="Tìm kiếm Hình Thức Thi..." style="width:300px; padding:8px; border-radius:6px; border:1px solid #d1d5db;">
</div>
    </div>
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="checkAllExamForm"></th>
              <th>Tên Hình Thức Thi</th>
<th>Ghi chú</th>
<th>Trạng thái</th>
<th>Ngày tạo</th>
<th>Ngày cập nhật</th>
<th>Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="examFormTable">
            @foreach($examForms as $form)
            <tr>
                    <td><input type="checkbox" class="row-check-exam-form" data-id="{{ $form['code'] }}"></td>
                    <td>{{ $form['name'] }}</td>
            <td>{{ $form['note'] ?? '' }}</td>
            <td>
                @php $statusId = $type['status_id'] ?? null; @endphp
                <span class="badge
                    @if($statusId == 1) bg-success
                    @elseif($statusId == 2) bg-danger
                    @else bg-secondary
                    @endif">
                    {{ $statusId == 1 ? 'Đang hoạt động' : ($statusId == 2 ? 'Không hoạt động' : 'Chưa xác định') }}
                </span>
            </td>

            <td>{{ $form['created_at'] ?? '-' }}</td>
            <td>{{ $form['updated_at'] ?? '-' }}</td>
            <td>
                <button class="action-icon" onclick="openExamFormModal('edit','{{ $form['code'] }}','{{ $form['name'] }}','{{ $form['note'] ?? '' }}')">⚙️</button>
            </td>
                </tr>
            @endforeach

        </tbody>
    </table>
    <div class="mt-3">
        {{ $examForms->links('pagination::bootstrap-5') }}
    </div>

</div>

{{-- ===================== MODALS ===================== --}}
<div id="examTypeModal" class="modal">
    <div class="modal-content">
        <h2 id="examTypeModalTitle">Thêm Loại Kỳ Thi</h2>
        <form id="examTypeFormModal" onsubmit="event.preventDefault(); saveExamType();">
            <input type="hidden" id="examTypeId">
            <div class="form-group"><label>Tên Loại Kỳ Thi</label><input type="text" id="examTypeName" required></div>
            <div class="form-group"><label>Trạng thái</label>
                <select id="examTypeStatus">
                    <option value="1">Đang hoạt động</option>
                    <option value="2">Không hoạt động</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeExamTypeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<div id="examFormModal" class="modal">
    <div class="modal-content">
        <h2 id="examFormModalTitle">Thêm Hình Thức Thi</h2>
        <form id="examFormFormModal" onsubmit="event.preventDefault(); saveExamForm();">
            <input type="hidden" id="examFormId">
            <div class="form-group"><label>Tên Hình Thức Thi</label><input type="text" id="examFormName" required></div>
            <div class="form-group"><label>Ghi chú</label><textarea id="examFormNote" rows="3"></textarea></div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeExamFormModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>
<script>
// Hàm chuẩn hóa chuỗi: bỏ dấu, chuyển chữ thường
function normalizeString(str) {
    return str.toLowerCase()
              .normalize('NFD')
              .replace(/[\u0300-\u036f]/g, '')
              .trim();
}

// Tìm kiếm Loại Kỳ Thi
document.getElementById('searchExamType').addEventListener('input', function(){
    const keyword = normalizeString(this.value);
    document.querySelectorAll('#examTypeTable tr').forEach(row => {
        const text = Array.from(row.querySelectorAll('td'))
                          .map(td => normalizeString(td.textContent))
                          .join(' ');
        row.style.display = text.includes(keyword) ? '' : 'none';
    });
});

// Tìm kiếm Hình Thức Thi
document.getElementById('searchExamForm').addEventListener('input', function(){
    const keyword = normalizeString(this.value);
    document.querySelectorAll('#examFormTable tr').forEach(row => {
        const text = Array.from(row.querySelectorAll('td'))
                          .map(td => normalizeString(td.textContent))
                          .join(' ');
        row.style.display = text.includes(keyword) ? '' : 'none';
    });
});
</script>
<script>
/* ==== KỲ THI ==== */
function openExamModal(type,id='',name='',etype='',eform='',start='',end='',status='1'){
    document.getElementById('examModalTitle').textContent = type==='add'?'Thêm Kỳ Thi':'Sửa Kỳ Thi';
    document.getElementById('examId').value=id;
    document.getElementById('examName').value=name;
    document.getElementById('examTypeName').value=etype;
    document.getElementById('examFormName').value=eform;
    document.getElementById('examStart').value=start?start.split(' ')[0]:'';
    document.getElementById('examEnd').value=end?end.split(' ')[0]:'';
    document.getElementById('examStatus').value=status;
    document.getElementById('examModal').style.display='flex';
}
function closeExamModal(){ document.getElementById('examModal').style.display='none'; }
function saveExam(){
    const id=document.getElementById('examId').value;
    const exam_name=document.getElementById('examName').value;
    const exam_type=document.getElementById('examTypeName').value;
    const exam_form=document.getElementById('examFormName').value;
    const exam_start=document.getElementById('examStart').value;
    const exam_end=document.getElementById('examEnd').value;
    const status_id=document.getElementById('examStatus').value;
    fetch("",{method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}, body:JSON.stringify({id,exam_name,exam_type,exam_form,exam_start,exam_end,status_id})}).then(r=>r.json()).then(d=>{if(d.success) location.reload(); else alert('Lưu thất bại');});
}


/* ==== SEARCH ==== */
document.getElementById('searchExam').addEventListener('keyup', function(){
    const keyword=this.value.toLowerCase();
    document.querySelectorAll('#examTable tr').forEach(row=>{
        const text=Array.from(row.querySelectorAll('td')).slice(1,7).map(td=>td.textContent.toLowerCase()).join(' ');
        row.style.display=text.includes(keyword)?'':'none';
    });
});
document.getElementById('searchExamType').addEventListener('keyup', function(){
    const keyword=this.value.toLowerCase();
    document.querySelectorAll('#examTypeTable tr').forEach(row=>{
        const text=Array.from(row.querySelectorAll('td')).slice(1,4).map(td=>td.textContent.toLowerCase()).join(' ');
        row.style.display=text.includes(keyword)?'':'none';
    });
});
document.getElementById('searchExamForm').addEventListener('keyup', function(){
    const keyword=this.value.toLowerCase();
    document.querySelectorAll('#examFormTable tr').forEach(row=>{
        const text=Array.from(row.querySelectorAll('td')).slice(1,6).map(td=>td.textContent.toLowerCase()).join(' ');
        row.style.display=text.includes(keyword)?'':'none';
    });
});
</script>
<script>
/* ==== CRUD LOẠI KỲ THI ==== */
function openExamTypeModal(mode,id='',name='',status='1'){
    document.getElementById('examTypeModalTitle').textContent = mode==='add'?'Thêm Loại Kỳ Thi':'Sửa Loại Kỳ Thi';
    document.getElementById('examTypeId').value=id;
    document.getElementById('examTypeName').value=name;
    document.getElementById('examTypeStatus').value=status;
    document.getElementById('examTypeModal').style.display='flex';
}
function closeExamTypeModal(){ document.getElementById('examTypeModal').style.display='none'; }
function saveExamType(){
    const id = document.getElementById('examTypeId').value || 'exam_type_' + Date.now();
    const name = document.getElementById('examTypeName').value;
    const status_id = document.getElementById('examTypeStatus').value; // lấy status_id

    fetch("{{ route('admin.thongtindotthi.storeConfig', ['type'=>'exam_types']) }}",{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({code:id,name,status_id})
    }).then(r=>r.json()).then(d=>{
        if(d.success) location.reload(); 
        else alert('Lưu thất bại: ' + (d.message || JSON.stringify(d.errors)));
    });
}



/* ==== CRUD HÌNH THỨC THI ==== */
function openExamFormModal(mode,id='',name='',note=''){
    document.getElementById('examFormModalTitle').textContent = mode==='add'?'Thêm Hình Thức Thi':'Sửa Hình Thức Thi';
    document.getElementById('examFormId').value=id;
    document.getElementById('examFormName').value=name;
    document.getElementById('examFormNote').value=note;
    document.getElementById('examFormModal').style.display='flex';
}
function closeExamFormModal(){ document.getElementById('examFormModal').style.display='none'; }
function saveExamForm(){
    const id = document.getElementById('examFormId').value || 'exam_form_' + Date.now();
    const name = document.getElementById('examFormName').value;
    const note = document.getElementById('examFormNote').value;
    const status_id = 1; // hoặc có thêm select nếu muốn sửa status ở form

    fetch("{{ route('admin.thongtindotthi.storeConfig', ['type'=>'exam_forms']) }}",{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({code:id,name,note,status_id})
    }).then(r=>r.json()).then(d=>{
        if(d.success) location.reload(); 
        else alert('Lưu thất bại: ' + (d.message || JSON.stringify(d.errors)));
    });
}


</script>
<script>
/* ==== CHỨC NĂNG CHUNG CHO CHECKBOX VÀ XÓA NHIỀU ==== */
function setupBulkDelete(tableSelector, checkAllSelector, rowCheckboxSelector, deleteBtnSelector, deleteUrl) {
    const checkAll = document.getElementById(checkAllSelector);
    const deleteBtn = document.getElementById(deleteBtnSelector);
    const tableRows = document.querySelectorAll(`#${tableSelector} .${rowCheckboxSelector}`);

    function updateDeleteBtn() {
        const checkedCount = document.querySelectorAll(`#${tableSelector} .${rowCheckboxSelector}:checked`).length;
        deleteBtn.textContent = `Xóa ${checkedCount} mục`;
        deleteBtn.disabled = checkedCount === 0;
        deleteBtn.classList.toggle('btn-disabled', checkedCount === 0);
    }

    checkAll.addEventListener('change', () => {
        tableRows.forEach(row => row.checked = checkAll.checked);
        updateDeleteBtn();
    });

    tableRows.forEach(row => {
        row.addEventListener('change', () => {
            const allChecked = Array.from(tableRows).every(r => r.checked);
            checkAll.checked = allChecked;
            updateDeleteBtn();
        });
    });

    deleteBtn.addEventListener('click', () => {
        const ids = Array.from(document.querySelectorAll(`#${tableSelector} .${rowCheckboxSelector}:checked`))
                        .map(r => r.dataset.id);
        if(ids.length === 0) return;
        if(!confirm(`Bạn có chắc chắn muốn xóa ${ids.length} mục?`)) return;

        fetch(deleteUrl, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({ids})
        }).then(r => r.json()).then(d => {
            if(d.success) location.reload(); else alert('Xóa thất bại');
        });
    });
}

/* ==== ÁP DỤNG CHO CÁC BẢNG ==== */
setupBulkDelete('examTable', 'checkAllExam', 'row-check-exam', 'deleteExamBtn', "{{ route('admin.thongtindotthi.delete', ['type'=>'exams']) }}");
setupBulkDelete('examTypeTable', 'checkAllExamType', 'row-check-exam-type', 'deleteExamTypeBtn', "{{ route('admin.thongtindotthi.delete', ['type'=>'exam_types']) }}");
setupBulkDelete('examFormTable', 'checkAllExamForm', 'row-check-exam-form', 'deleteExamFormBtn', "{{ route('admin.thongtindotthi.delete', ['type'=>'exam_forms']) }}");
</script>

@endsection


