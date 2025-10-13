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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
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

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            color: #374151;
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

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #111827;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .btn-disabled {
            background: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        table th,
        table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        table th {
            background: #f1f5f9;
            font-weight: 700;
            font-size: 14px;
            color: #374151;
        }

        table tbody tr:hover {
            background: #f9fafb;
        }

        .action-icon {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #374151;
        }

        .action-icon:hover {
            transform: rotate(10deg);
        }

        /* ===== Modal overlay ===== */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
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
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-content h2 {
            margin-bottom: 16px;
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .modal-actions {
            margin-top: 16px;
            text-align: right;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* ====== DARK MODE OVERRIDES ====== */
        body.dark-mode .container {
            background: #2c2c3e;
            color: #f1f1f1;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
        }

        body.dark-mode h1 {
            color: #f3f4f6;
        }

        body.dark-mode table {
            color: #e5e7eb;
        }

        body.dark-mode table th {
            background: #1f2937;
            color: #e5e7eb;
            border-bottom-color: #374151;
        }

        body.dark-mode table td {
            border-bottom-color: #374151;
            color: #f3f4f6;
        }

        body.dark-mode table tbody tr:hover {
            background: #2b3443;
        }

        body.dark-mode .action-icon {
            color: #e5e7eb;
        }

        body.dark-mode .btn-secondary {
            background: #374151;
            color: #e5e7eb;
        }

        body.dark-mode .btn-secondary:hover {
            background: #4b5563;
        }

        body.dark-mode .modal-content {
            background: #2c2c3e;
            color: #f1f1f1;
            border: 1px solid #3b3b52;
        }

        body.dark-mode .form-group label {
            color: #e5e7eb;
        }

        body.dark-mode .form-group input,
        body.dark-mode .form-group select {
            background: #1f2937;
            color: #e5e7eb;
            border-color: #4b5563;
        }

        body.dark-mode .form-group input::placeholder {
            color: #9ca3af;
        }

        body.dark-mode .form-group input:focus,
        body.dark-mode .form-group select:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, .2);
        }
    </style>

    <div class="container">
        <h1>Quản lý Học phần</h1>

        @if (session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        {{-- @can('create', App\Models\Course::class)
            <button class="btn btn-primary" onclick="openModal('add')">+ Thêm mới</button>
        @endcan --}}
        @if (auth()->check() && auth()->user()->hasRole('truongbomon'))
            <button class="btn btn-primary" onclick="openModal('add')">+ Thêm mới</button>
        @endif

        <button id="deleteBtn" class="btn btn-disabled" disabled>Xóa 0 mục</button>

        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>Mã HP</th>
                    <th>Tên HP</th>
                    <th>Tín chỉ</th>
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
                @forelse($courses as $course)
                    <tr>
                        <td><input type="checkbox" class="row-check" data-id="{{ $course->id }}"></td>
                        <td>{{ $course->course_code }}</td>
                        <td>{{ $course->course_name }}</td>
                        <td>{{ $course->credit }}</td>

                        {{-- dùng alias lấy từ Query Builder --}}
                        <td>{{ $course->program_name }}</td>
                        <td>{{ $course->department_name }}</td>
                        <td>{{ $course->semester_name }}</td>
                        <td>{{ $course->creator_name }}</td>
                        <td>{{ $course->updater_name }}</td>

                        {{-- created_at/updated_at đang là DATE -> format an toàn --}}
                        <td>{{ $course->created_at ? \Carbon\Carbon::parse($course->created_at)->format('d-m-Y') : '' }}
                        </td>
                        <td>{{ $course->updated_at ? \Carbon\Carbon::parse($course->updated_at)->format('d-m-Y') : '' }}
                        </td>

                        <td>
                            {{-- Nút sửa sẽ bổ sung sau --}}
                            <button class="action-icon" disabled title="Sẽ cập nhật sau">⚙️</button>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="12" class="text-muted">Chưa có học phần.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $courses->links() }}
        </div>
    </div>

    <!-- Modal Thêm -->
    <!-- Modal Thêm Học phần (đẹp + có search CTĐT) -->
    <div id="courseModal" class="modal" aria-hidden="true">
        <div class="modal-content" style="max-width:720px">
            <h2 id="modalTitle" class="modal-title">Thêm Học phần</h2>

            <form id="courseForm" method="post" action="{{ route('truongbomon.quanlyhocphan.store') }}" novalidate>
                @csrf

                <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
                    {{-- Mã HP --}}
                    <div class="form-group">
                        <label for="courseCode">Mã học phần <span class="required">*</span></label>
                        <input type="text" id="courseCode" name="course_code" maxlength="100"
                            value="{{ old('course_code') }}" required placeholder="VD: MATH101">
                        @error('course_code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        <small class="text-muted">Đặt mã thống nhất toàn trường (không dấu, không khoảng trắng).</small>
                    </div>

                    {{-- Tên HP --}}
                    <div class="form-group">
                        <label for="courseName">Tên học phần <span class="required">*</span></label>
                        <input type="text" id="courseName" name="course_name" maxlength="250"
                            value="{{ old('course_name') }}" required placeholder="VD: Giải tích 1">
                        @error('course_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Tín chỉ --}}
                    <div class="form-group">
                        <label for="credits">Số tín chỉ <span class="required">*</span></label>
                        <input type="number" id="credits" name="credit" min="1" max="10"
                            value="{{ old('credit') }}" required placeholder="VD: 3">
                        @error('credit')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- CTĐT: Select + ô tìm kiếm filter (client-side) --}}
                    <div class="form-group">
                        <label for="programSearch">Chương trình đào tạo <span class="required">*</span></label>

                        <div style="display:grid; grid-template-columns: 1fr; gap:8px;">
                            <input id="programSearch" type="text" placeholder="Gõ để lọc CTĐT..."
                                oninput="filterPrograms(this.value)">
                            <select id="program" name="education_program_id" required size="6">
                                <option value="" hidden>-- Chọn Chương trình --</option>
                                @forelse($programs as $program)
                                    <option value="{{ $program->id }}">
                                        {{ $program->program_name }}
                                    </option>
                                @empty
                                    <option disabled>(Chưa có CTĐT nào)</option>
                                @endforelse
                            </select>

                        </div>
                        @error('education_program_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        <small class="text-muted">Mẹo: gõ vài ký tự để lọc nhanh danh sách.</small>
                    </div>

                    {{-- Bộ môn: chỉ hiện khi TBM có >1 bộ môn --}}
                    <div class="form-group">
                        <label for="department_id">Bộ môn <span class="required">*</span></label>
                        <select id="department_id" name="department_id" required>
                            <option value="">-- Chọn bộ môn --</option>
                            @foreach ($deptList as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if (isset($deptList) && $deptList->count() > 1)
                        {{-- <div class="form-group">
                            <label for="department_id">Bộ môn <span class="required">*</span></label>
                            <select id="department_id" name="department_id" required>
                                <option value="">-- Chọn bộ môn --</option>
                                @foreach ($deptList as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                    @endif


                    {{-- (Tùy chọn) Nhóm học phần – ẩn nếu bạn chưa dùng, bật khi cần
        <div class="form-group">
          <label for="courseGroup">Nhóm học phần (tùy chọn)</label>
          <input type="text" id="courseGroup" name="course_group" value="{{ old('course_group') }}" placeholder="VD: Tự chọn CN 1">
        </div>
        --}}
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                </div>

                @if (isset($programs) && $programs->count() === 0)
                    <div class="alert alert-warning" style="margin-top:12px">
                        Chưa có Chương trình đào tạo. Vui lòng thêm CTĐT trước khi tạo Học phần.
                    </div>
                @endif
            </form>
        </div>
    </div>



    <script>
        // Lọc options trong select CTĐT theo text gõ ở ô search
        function filterPrograms(keyword) {
            const kw = (keyword || '').toLowerCase().trim();
            const sel = document.getElementById('program');
            // Hiển thị tất cả trước
            for (const opt of sel.options) {
                if (!opt.value) continue; // bỏ placeholder hidden
                const show = !kw || opt.text.toLowerCase().includes(kw);
                opt.style.display = show ? '' : 'none';
            }
            // Nếu sau khi lọc mà option đang chọn bị ẩn -> bỏ chọn
            if (sel.selectedOptions.length && sel.selectedOptions[0].style.display === 'none') {
                sel.value = '';
            }
        }

        // Khi mở modal: reset form + clear filter
        function openModal(type) {
            const modal = document.getElementById('courseModal');
            const title = document.getElementById('modalTitle');
            const form = document.getElementById('courseForm');

            form.reset();
            document.getElementById('programSearch').value = '';
            filterPrograms('');

            title.textContent = 'Thêm Học phần';
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
        }

        function closeModal() {
            const modal = document.getElementById('courseModal');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }
        window.onclick = function(e) {
            const modal = document.getElementById('courseModal');
            if (e.target === modal) closeModal();
        }
    </script>
    <script>
        // Modal helpers
        function openModal(type) {
            const modal = document.getElementById('courseModal');
            const title = document.getElementById('modalTitle');
            const form = document.getElementById('courseForm');

            // reset form mỗi lần mở
            form.reset();
            title.textContent = 'Thêm Học phần';
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
        }

        function closeModal() {
            const modal = document.getElementById('courseModal');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }
        window.onclick = function(e) {
            const modal = document.getElementById('courseModal');
            if (e.target === modal) closeModal();
        }

        // Checkbox select all (UI)
        let selectedCount = 0;
        const deleteBtn = document.getElementById('deleteBtn');
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-check');

        function updateDeleteBtn() {
            if (selectedCount > 0) {
                deleteBtn.textContent = `Xóa ${selectedCount} mục`;
                deleteBtn.classList.remove('btn-disabled');
                deleteBtn.disabled = false;
            } else {
                deleteBtn.textContent = "Xóa 0 mục";
                deleteBtn.classList.add('btn-disabled');
                deleteBtn.disabled = true;
            }
        }
        checkboxes.forEach(chk => {
            chk.addEventListener('change', () => {
                selectedCount = document.querySelectorAll('.row-check:checked').length;
                updateDeleteBtn();
            });
        });
        if (selectAll) {
            selectAll.addEventListener('change', () => {
                const boxes = document.querySelectorAll('.row-check');
                boxes.forEach(chk => chk.checked = selectAll.checked);
                selectedCount = document.querySelectorAll('.row-check:checked').length;
                updateDeleteBtn();
            });
        }
    </script>


@endsection
