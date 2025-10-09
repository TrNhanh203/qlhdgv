@extends('layouts.app')

@section('content')
<style>
    /* ===== Light (mặc định) ===== */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f6fa;
        margin: 0;
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.08);
    }

    h1 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #1f2937;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 20px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    .form-group .required {
        color: red;
        font-weight: bold;
    }
    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-secondary { background: #f3f4f6; color: #111827; }
    .btn-secondary:hover { background: #e5e7eb; }
    .btn-disabled { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; }

    table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    table th, table td { padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    table th { background: #f1f5f9; font-weight: 700; font-size: 14px; color: #374151; }
    table tbody tr:hover { background: #f9fafb; }

    .status {
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
    }
    .status.active { background: #dcfce7; color: #16a34a; }
    .status.inactive { background: #fee2e2; color: #dc2626; }

    .action-icon {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #374151;
    }
    .action-icon:hover { transform: rotate(10deg); }

    /* ===== Modal overlay ===== */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        justify-content: center;
        align-items: center;
        z-index: 1000;
        padding: 16px;
    }
    .modal-title {
    text-align: center;
    color: red;
    font-weight: 800;
    font-size: 22px;
    margin-bottom: 20px;
    }
    .modal-content {
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        width: 480px;
        max-width: 100%;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        animation: fadeIn 0.2s ease;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    .modal-content h2 { margin-bottom: 16px; font-size: 20px; font-weight: 800; color: #111827; }
    .form-group { margin-bottom: 12px; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #374151; }
    .form-group input, .form-group select {
        width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #d1d5db; outline: none;
        transition: border-color .2s, box-shadow .2s; background: #fff; color: #111827;
    }
    .form-group input:focus, .form-group select:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.15); }
    .modal-actions { margin-top: 16px; text-align: right; display: flex; gap: 10px; justify-content: flex-end; }

    /* ====== DARK MODE OVERRIDES ====== */
    body.dark-mode .container { background: #2c2c3e; color: #f1f1f1; box-shadow: 0 6px 18px rgba(0,0,0,0.35); }
    body.dark-mode h1 { color: #f3f4f6; }
    body.dark-mode table { color: #e5e7eb; }
    body.dark-mode table th { background: #1f2937; color: #e5e7eb; border-bottom-color: #374151; }
    body.dark-mode table td { border-bottom-color: #374151; color: #f3f4f6; }
    body.dark-mode table tbody tr:hover { background: #2b3443; }
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
    <h1>Quản lý Học phần</h1>

    <button class="btn btn-primary" onclick="openModal('add')">+ Thêm mới</button>
    <button id="deleteBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>

    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>Mã HP</th>
                <th>Tên HP</th>
                <th>Mã Nhóm HP</th>
                <th>Nhóm HP</th>
                <th>tín chỉ</th>
                <th>CTĐT</th>
                <th>Bộ môn</th>
                <th>Học kỳ</th>
                <th>Tạo bởi</th>
                <th>Cập nhật bởi</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th>Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $course)
            <tr>
                <td><input type="checkbox" class="row-check" data-id="{{ $course->id }}"></td>
                <td>{{ $course->id }}</td>
                <td>{{ $course->course_code }}</td>
                <td>{{ $course->course_name }}</td>
                <td>{{ $course->course_group_id }}</td>
                <td>{{ $course->course_group }}</td>
                <td>{{ $course->credit }}</td>
                <td>{{ $course->education_program_id }}</td>
                <td>{{ $course->department_id }}</td>
                <td>{{ $course->semester_id }}</td>
                <td>{{ $course->created_by }}</td>
                <td>{{ $course->updated_by }}</td>
                <td>{{ $course->created_at->format('d-m-Y H:i') }}</td>
                <td>{{ $course->updated_at->format('d-m-Y H:i') }}</td>
                <td>
                    <button class="action-icon" 
                        onclick="openModal(
                            'edit',
                            '{{ $course->id }}',
                            '{{ $course->course_code }}',
                            '{{ $course->course_name }}',
                            '{{ $course->course_group_id }}',
                            '{{ $course->course_group }}',
                            '{{ $course->credit }}',
                            '{{ $course->education_program_id }}',
                            '{{ $course->department_id }}',
                            '{{ $course->semester_id }}',
                            '{{ $course->created_by }}',
                            '{{ $course->updated_by }}'
                        )">⚙️</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="courseModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle" class="modal-title">Thêm Học phần</h2>
        <form id="courseForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="courseCode">Mã học phần <span class="required">*</span></label>
                    <input type="text" id="courseCode" required>
                </div>

                <div class="form-group">
                    <label for="courseName">Tên học phần <span class="required">*</span></label>
                    <input type="text" id="courseName" required>
                </div>

                <div class="form-group">
                    <label for="courseGroupId">Nhóm học phần ID</label>
                    <input type="text" id="courseGroupId">
                </div>

                <div class="form-group">
                    <label for="courseGroup">Nhóm học phần</label>
                    <input type="text" id="courseGroup">
                </div>

                <div class="form-group">
                    <label for="credits">Số tín chỉ <span class="required">*</span></label>
                    <input type="number" id="credits" required>
                </div>

                <div class="form-group">
                    <label for="program">Chương trình đào tạo <span class="required">*</span></label>
                    <select id="program" required>
                        <option value="">-- Chọn Chương trình --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="departmentId">Bộ môn</label>
                    <input type="text" id="departmentId">
                </div>

                <div class="form-group">
                    <label for="semesterId">Học kỳ</label>
                    <input type="text" id="semesterId">
                </div>

                <div class="form-group">
                    <label for="createdBy">Tạo bởi</label>
                    <input type="text" id="createdBy">
                </div>

                <div class="form-group">
                    <label for="updatedBy">Cập nhật bởi</label>
                    <input type="text" id="updatedBy">
                </div>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
            </div>
        </form>
    </div>
</div>




<script>
    let selectedCount = 0;

    function openModal(type, code = '', name = '', groupId = '', group = '', credits = '', programId = '', departmentId = '', semesterId = '', createdBy = '', updatedBy = '') {
        const modal = document.getElementById('courseModal');
        const title = document.getElementById('modalTitle');

        document.getElementById('courseCode').value = code;
        document.getElementById('courseName').value = name;
        document.getElementById('courseGroupId').value = groupId;
        document.getElementById('courseGroup').value = group;
        document.getElementById('credits').value = credits;
        document.getElementById('program').value = programId;
        document.getElementById('departmentId').value = departmentId;
        document.getElementById('semesterId').value = semesterId;
        document.getElementById('createdBy').value = createdBy;
        document.getElementById('updatedBy').value = updatedBy;

        title.textContent = type === 'add' ? 'Thêm Học phần' : 'Sửa Học phần';
        modal.style.display = 'flex';
    }

    function closeModal() { document.getElementById('courseModal').style.display = 'none'; }

    window.onclick = function(e) {
        const modal = document.getElementById('courseModal');
        if(e.target === modal) closeModal();
    }

    const deleteBtn = document.getElementById('deleteBtn');
    const checkboxes = document.querySelectorAll('.row-check');
    const selectAll = document.getElementById('selectAll');

    checkboxes.forEach(chk => {
        chk.addEventListener('change', () => {
            selectedCount = document.querySelectorAll('.row-check:checked').length;
            updateDeleteBtn();
        });
    });

    selectAll.addEventListener('change', () => {
        checkboxes.forEach(chk => chk.checked = selectAll.checked);
        selectedCount = document.querySelectorAll('.row-check:checked').length;
        updateDeleteBtn();
    });

    function updateDeleteBtn() {
        if(selectedCount > 0) {
            deleteBtn.textContent = `Xóa ${selectedCount} mục`;
            deleteBtn.classList.remove('btn-disabled');
            deleteBtn.disabled = false;
        } else {
            deleteBtn.textContent = "Xóa 0 mục";
            deleteBtn.classList.add('btn-disabled');
            deleteBtn.disabled = true;
        }
    }
</script>
@endsection