còn code này nữa @extends('layouts.app')

@section('content')
<style>
    /* ===== Light (mặc định) ===== */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f6fa;
        margin: 0;
    }
    .semester-badge {
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
    text-transform: capitalize;
    }

    .semester-1 { background: #dcfce7; color: #16a34a; }   
    .semester-2 { background: #fef9c3; color: #ca3c04ff; }   
    .semester-3 { background: #dbeafe; color: #2563eb; } 
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
    table th, table td { padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center; }
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

    /* ====== DARK MODE OVERRIDES (theo body.dark-mode từ app.blade.php) ====== */
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
    #searchInput:focus {
    border-color: #2563eb;
    box-shadow: 0 0 6px rgba(37, 99, 235, 0.4);
    outline: none;
}

/* hiệu ứng ẩn/hiện hàng */
.fade-out {
    animation: fadeOut 0.3s forwards;
}
.fade-in {
    animation: fadeInRow 0.3s forwards;
}
@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; height: 0; padding: 0; margin: 0; }
}
@keyframes fadeInRow {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

{{-- === ALERT === --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif


{{-- === QUẢN LÝ NĂM HỌC === --}}
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1>Năm Học</h1>
        <input type="text" id="searchYear" placeholder="Tìm kiếm năm học..." 
               style="width:260px; padding:8px 12px; border:1px solid #d1d5db; border-radius:8px;">
    </div>

    <button class="btn btn-primary" onclick="openModal('year','add')">+ Thêm Năm học</button>
    <button id="deleteYearBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>

    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAllYears"></th>
                <th>Mã năm học</th>
                <th>Bắt đầu</th>
                <th>Kết thúc</th>
                <th>Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="yearTable">
            @foreach($academicYears as $year)
            <tr>
                <td><input type="checkbox" class="year-check" value="{{ $year->id }}"></td>
                <td>{{ $year->year_code }}</td>
                <td>{{ \Carbon\Carbon::parse($year->start_date)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($year->end_date)->format('d-m-Y') }}</td>
                
                <td><button class="action-icon" onclick="openModal('year','edit',{{ $year->id }})">⚙️</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-3">
    {{ $academicYears->onEachSide(2)->links('pagination::bootstrap-5') }}
</div>
{{-- === MODAL NĂM HỌC === --}}
<div id="yearModal" class="modal">
    <div class="modal-content">
        <h2 id="yearModalTitle">Thêm Năm học</h2>
        <form method="POST" action="{{ route('admin.years.store') }}">
            @csrf
            <input type="hidden" id="yearId" name="id">
            <div class="form-group">
                <label>Mã năm học</label>
                <input type="text" id="yearCode" name="year_code">
            </div>
            <div class="form-group">
                <label>Ngày bắt đầu</label>
                <input type="date" id="startDate" name="start_date">
            </div>
            <div class="form-group">
                <label>Ngày kết thúc</label>
                <input type="date" id="endDate" name="end_date">
            </div>
            
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('yearModal')">Hủy</button>
            </div>
        </form>
    </div>
</div>

<hr style="margin:40px 0;">

{{-- === QUẢN LÝ HỌC KỲ === --}}
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1>Học Kỳ</h1>
        <input type="text" id="searchSemester" placeholder="Tìm kiếm học kỳ..." 
               style="width:260px; padding:8px 12px; border:1px solid #d1d5db; border-radius:8px;">
    </div>

    <button class="btn btn-primary" onclick="openModal('semester','add')">+ Thêm Học kỳ</button>
    <button id="deleteSemesterBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>

    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAllSemesters"></th>
                <th>Tên học kỳ</th>
                <th>Thứ tự</th>
                <th>Năm học</th>
                <th>Hiệu chỉnh</th>
            </tr>
        </thead>
        <tbody id="semesterTable">
            @foreach($semesters as $semester)
            <tr>
                <td><input type="checkbox" class="semester-check" value="{{ $semester->id }}"></td>
                @php
                    $name = strtolower($semester->semester_name);
                    $cls = '';
                    if (in_array($name, ['học kỳ 1','học kỳ i'])) $cls = 'semester-1';
                    elseif (in_array($name, ['học kỳ 2','học kỳ ii'])) $cls = 'semester-2';
                    elseif (in_array($name, ['học kỳ 3','học kỳ iii'])) $cls = 'semester-3';
                @endphp
                <td><span class="semester-badge {{ $cls }}">{{ $semester->semester_name }}</span></td>

                <td>{{ $semester->order_number }}</td>
                <td>{{ $semester->academicYear?->year_code }}</td>
                <td><button class="action-icon" onclick="openModal('semester','edit',{{ $semester->id }})">⚙️</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-3">
    {{ $semesters->onEachSide(2)->links('pagination::bootstrap-5') }}
</div>


{{-- === MODAL HỌC KỲ === --}}
<div id="semesterModal" class="modal">
    <div class="modal-content">
        <h2 id="semesterModalTitle">Thêm Học kỳ</h2>
        <form method="POST" action="{{ route('admin.semesters.store') }}">
            @csrf
            <input type="hidden" id="semesterId" name="id">
            <div class="form-group">
                <label>Tên học kỳ</label>
                <input type="text" id="semesterName" name="semester_name">
            </div>
            <div class="form-group">
                <label>Thứ tự</label>
                <input type="number" id="orderNumber" name="order_number" min="1">
            </div>
            <div class="form-group">
                <label>Năm học</label>
                <select id="semesterYear" name="academic_year_id">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->year_code }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('semesterModal')">Hủy</button>
            </div>
        </form>
    </div>
</div>

<script>
// === MODAL OPEN/CLOSE ===
function openModal(type, action, id = null) {
    if(type === 'year') {
        document.getElementById('yearModal').style.display = 'flex';
        document.getElementById('yearModalTitle').textContent = action === 'add' ? 'Thêm Năm học' : 'Sửa Năm học';
        document.getElementById('yearId').value = id || '';
    }
    if(type === 'semester') {
        document.getElementById('semesterModal').style.display = 'flex';
        document.getElementById('semesterModalTitle').textContent = action === 'add' ? 'Thêm Học kỳ' : 'Sửa Học kỳ';
        document.getElementById('semesterId').value = id || '';
    }
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
// ==== Tìm kiếm realtime Năm học ====
function setupSearchYear() {
    document.getElementById('searchYear').addEventListener('keyup', function() {
        const keyword = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#yearTable tr');

        rows.forEach(row => {
            const codeCell = row.querySelector('td:nth-child(2)');
            const startCell = row.querySelector('td:nth-child(3)');
            const endCell = row.querySelector('td:nth-child(4)');

            const text = (
                (codeCell?.textContent || '') + ' ' +
                (startCell?.textContent || '') + ' ' +
                (endCell?.textContent || '')
            ).toLowerCase();

            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    });
}

function setupSearchSemester() {
    document.getElementById('searchSemester').addEventListener('keyup', function() {
        const keyword = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#semesterTable tr');

        rows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)');
            const orderCell = row.querySelector('td:nth-child(3)');
            const yearCell = row.querySelector('td:nth-child(4)');

            const text = (
                (nameCell?.textContent || '') + ' ' +
                (orderCell?.textContent || '') + ' ' +
                (yearCell?.textContent || '')
            ).toLowerCase();

            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    });
}
setupSearchYear();
setupSearchSemester();
function handleBulkDelete(deleteBtnId, checkboxClass, actionUrl) {
    const deleteBtn = document.getElementById(deleteBtnId);
    deleteBtn.addEventListener('click', function() {
        const checked = document.querySelectorAll(`.${checkboxClass}:checked`);
        if (checked.length === 0) return;

        if (!confirm(`Bạn có chắc muốn xóa ${checked.length} mục?`)) return;

        // Tạo form ẩn
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = actionUrl;

        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrf}">
            <input type="hidden" name="_method" value="DELETE">
            ${Array.from(checked).map(chk => `<input type="hidden" name="ids[]" value="${chk.value}">`).join('')}
        `;

        document.body.appendChild(form);
        form.submit();
    });
}
// === CHECKBOX + DELETE ===
function setupDelete(selectAllId, checkboxClass, deleteBtnId) {
    const selectAll = document.getElementById(selectAllId);
    const deleteBtn = document.getElementById(deleteBtnId);

    function updateBtn() {
        const checked = document.querySelectorAll(`.${checkboxClass}:checked`).length;
        if(checked > 0) {
            deleteBtn.textContent = `Xóa ${checked} mục`;
            deleteBtn.classList.remove('btn-disabled');
            deleteBtn.disabled = false;
        } else {
            deleteBtn.textContent = "Xóa 0 mục";
            deleteBtn.classList.add('btn-disabled');
            deleteBtn.disabled = true;
        }
    }
    selectAll.addEventListener('change', () => {
        document.querySelectorAll(`.${checkboxClass}`).forEach(chk => chk.checked = selectAll.checked);
        updateBtn();
    });
    document.querySelectorAll(`.${checkboxClass}`).forEach(chk => chk.addEventListener('change', updateBtn));
}
setupDelete('selectAllYears','year-check','deleteYearBtn');
setupDelete('selectAllSemesters','semester-check','deleteSemesterBtn');
handleBulkDelete('deleteYearBtn', 'year-check', '{{ route("admin.years.deleteMultiple") }}');
handleBulkDelete('deleteSemesterBtn', 'semester-check', '{{ route("admin.semesters.deleteMultiple") }}');

</script>
@endsection