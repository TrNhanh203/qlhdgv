@extends($layout ?? 'layouts.app')
@section('content')
    @include('components.crud-style')

    <div class="container py-3">
        <h3 class="mb-4">⚙️ Quản lý khung chương trình đào tạo</h3>

        {{-- Dropdown chọn phiên bản CTĐT --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Chọn Phiên bản CTĐT:</label>
            <select class="form-select w-auto d-inline-block" id="programVersionSelect">
                <option selected value="1">CNTT – Khóa 47 (K47-IT)</option>
                <option value="2">CNTT – Khóa 48 (K48-IT)</option>
                <option value="3">QTKD – Khóa 47 (K47-BBA)</option>
            </select>
            <button class="btn btn-primary ms-2" onclick="openAdd()">+ Thêm học phần</button>
            <button id="deleteBtn" class="btn btn-outline-danger ms-1">Xóa đã chọn</button>
        </div>

        {{-- Bảng danh sách --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-secondary text-center">
                    <tr>
                        <th><input type="checkbox" id="selectAll" onclick="CRUD.toggleAll(this,'.row-check')"></th>
                        <th>Học kỳ</th>
                        <th>Mã HP</th>
                        <th>Tên học phần</th>
                        <th>Loại kiến thức</th>
                        <th>Nhóm HP</th>
                        <th>Bắt buộc?</th>
                        <th>LT</th>
                        <th>TH</th>
                        <th>Tổng TC</th>
                        <th>Ghi chú</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {{-- dữ liệu mẫu --}}
                    @foreach ([
            ['id' => 1, 'semester_no' => 1, 'course_code' => 'PLDC', 'course_name' => 'Pháp luật đại cương', 'knowledge_type' => 'Kiến thức chung', 'course_group' => 'Nhóm HP bắt buộc', 'is_compulsory' => 1, 'credit_theory' => 2, 'credit_practice' => 0, 'note' => ''],
            ['id' => 2, 'semester_no' => 2, 'course_code' => 'TCCA1', 'course_name' => 'Toán cao cấp A1', 'knowledge_type' => 'Kiến thức khoa học cơ bản', 'course_group' => 'Nhóm HP bắt buộc', 'is_compulsory' => 1, 'credit_theory' => 2, 'credit_practice' => 0, 'note' => ''],
            ['id' => 3, 'semester_no' => 5, 'course_code' => 'CNWUD', 'course_name' => 'Công nghệ web và ứng dụng', 'knowledge_type' => 'Kiến thức chuyên ngành', 'course_group' => 'Nhóm HP bắt buộc', 'is_compulsory' => 1, 'credit_theory' => 3, 'credit_practice' => 0, 'note' => 'Bao gồm thiết kế web'],
            ['id' => 4, 'semester_no' => 6, 'course_code' => 'IOT', 'course_name' => 'Công nghệ Internet Of Things', 'knowledge_type' => 'Kiến thức chuyên ngành', 'course_group' => 'Module tự chọn 2', 'is_compulsory' => 0, 'credit_theory' => 3, 'credit_practice' => 0, 'note' => 'Tự chọn chuyên ngành'],
        ] as $it)
                        <tr>
                            <td><input type="checkbox" class="row-check" value="{{ $it['id'] }}"></td>
                            <td class="text-center">{{ $it['semester_no'] }}</td>
                            <td>{{ $it['course_code'] }}</td>
                            <td>{{ $it['course_name'] }}</td>
                            <td>{{ $it['knowledge_type'] }}</td>
                            <td>{{ $it['course_group'] }}</td>
                            <td class="text-center">
                                @if ($it['is_compulsory'])
                                    <span class="badge bg-success">Bắt buộc</span>
                                @else
                                    <span class="badge bg-warning text-dark">Tự chọn</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $it['credit_theory'] }}</td>
                            <td class="text-center">{{ $it['credit_practice'] }}</td>
                            <td class="text-center fw-bold">{{ $it['credit_theory'] + $it['credit_practice'] }}</td>
                            <td>{{ $it['note'] }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="openEdit({{ json_encode($it) }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal thêm/sửa --}}
    <div id="crudModal" class="modal">
        <div class="modal-content" style="max-width: 700px">
            <h4 id="modalTitle">Thêm học phần vào CTĐT</h4>
            <form id="crudForm">
                <input type="hidden" id="crudId">
                <div class="form-grid">

                    <div class="form-group">
                        <label>Học phần</label>
                        <select data-field="course_id">
                            <option value="">-- chọn học phần --</option>
                            <option value="1">Pháp luật đại cương (2+0)</option>
                            <option value="2">Toán cao cấp A1 (2+0)</option>
                            <option value="3">Công nghệ web và ứng dụng (3+0)</option>
                            <option value="4">Công nghệ Internet Of Things (3+0)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Loại kiến thức</label>
                        <select data-field="knowledge_type">
                            <option value="kien_thuc_chung">Kiến thức chung</option>
                            <option value="kien_thuc_khoa_hoc_co_ban">Kiến thức khoa học cơ bản</option>
                            <option value="kien_thuc_bo_tro">Kiến thức bổ trợ</option>
                            <option value="kien_thuc_co_so_nganh_lien_nganh">Kiến thức cơ sở ngành/liên ngành</option>
                            <option value="kien_thuc_chuyen_nganh">Kiến thức chuyên ngành</option>
                            <option value="do_an_thuc_tap">Đồ án - Thực tập</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nhóm học phần</label>
                        <input type="text" data-field="course_group"
                            placeholder="VD: Nhóm HP bắt buộc, Module tự chọn 1">
                    </div>

                    <div class="form-group">
                        <label>Bắt buộc?</label>
                        <select data-field="is_compulsory">
                            <option value="1">Bắt buộc</option>
                            <option value="0">Tự chọn</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Học kỳ</label>
                        <input type="number" data-field="semester_no" min="1" max="10">
                    </div>

                    <div class="form-group">
                        <label>Tín chỉ lý thuyết</label>
                        <input type="number" data-field="credit_theory" min="0" value="0">
                    </div>

                    <div class="form-group">
                        <label>Tín chỉ thực hành</label>
                        <input type="number" data-field="credit_practice" min="0" value="0">
                    </div>

                    <div class="form-group" style="grid-column: span 2">
                        <label>Ghi chú</label>
                        <input type="text" data-field="note" placeholder="VD: Tự giảng, ≥12 TC, ...">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <button type="button" class="btn btn-secondary"
                        onclick="document.getElementById('crudModal').style.display='none'">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    @include('components.crud-script')

    <script>
        function openAdd() {
            document.getElementById('crudId').value = '';
            document.querySelectorAll('#crudForm [data-field]').forEach(i => i.value = '');
            document.getElementById('modalTitle').innerText = 'Thêm học phần vào CTĐT';
            document.getElementById('crudModal').style.display = 'flex';
        }

        function openEdit(data) {
            document.getElementById('crudId').value = data.id;
            document.querySelectorAll('#crudForm [data-field]').forEach(i => {
                i.value = data[i.dataset.field] ?? '';
            });
            document.getElementById('modalTitle').innerText = 'Chỉnh sửa học phần';
            document.getElementById('crudModal').style.display = 'flex';
        }
    </script>
@endsection
