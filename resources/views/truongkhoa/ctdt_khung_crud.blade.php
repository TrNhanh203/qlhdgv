@extends($layout ?? 'layouts.apptruongkhoa')

@section('title', 'Quản lý Khung CTĐT')

@section('content')
    @include('components.crud-style')

    <div class="container-fluid py-3">
        <h3 class="mb-4">⚙️ Quản lý khung chương trình đào tạo</h3>

        {{-- Thông tin phiên bản CTĐT --}}
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <div>
                <strong>CTĐT:</strong> {{ $version->program_name ?? '---' }} <br>
                <strong>Mã:</strong> {{ $version->program_code ?? '' }} |
                <strong>Phiên bản:</strong> {{ $version->version_code ?? '' }}
            </div>
            <div>
                <button class="btn btn-primary" onclick="openAdd()">+ Thêm học phần</button>
                <button id="deleteBtn" class="btn btn-outline-danger ms-1">Xóa đã chọn</button>
            </div>
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
                    @forelse($courses as $it)
                        <tr>
                            <td><input type="checkbox" class="row-check" value="{{ $it->id }}"></td>
                            <td class="text-center">{{ $it->semester_no ?? '-' }}</td>
                            <td>{{ $it->course_code }}</td>
                            <td>{{ $it->course_name }}</td>
                            <td>{{ $it->knowledge_type }}</td>
                            <td>{{ $it->course_group }}</td>
                            <td class="text-center">
                                @if ($it->is_compulsory)
                                    <span class="badge bg-success">Bắt buộc</span>
                                @else
                                    <span class="badge bg-warning text-dark">Tự chọn</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $it->credit_theory }}</td>
                            <td class="text-center">{{ $it->credit_practice }}</td>
                            <td class="text-center fw-bold">{{ $it->credit_total }}</td>
                            <td>{{ $it->note }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick='openEdit(@json($it))'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-3">Chưa có học phần nào trong khung CTĐT
                                này.</td>
                        </tr>
                    @endforelse
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
                        <select data-field="course_id" required>
                            <option value="">-- chọn học phần --</option>
                            @foreach ($courseOptions as $opt)
                                <option value="{{ $opt->id }}">
                                    {{ $opt->course_code }} – {{ $opt->course_name }}
                                </option>
                            @endforeach
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
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const ROUTE_STORE = "{{ route('truongkhoa.ctdtkhung.store', ['version_id' => $version->id]) }}";


            const ROUTE_DELETE_MULTI =
                "{{ route('truongkhoa.ctdtkhung.destroyMultiple', ['version_id' => $version->id]) }}";

            console.log("ROUTE_STORE =", ROUTE_STORE);

            function openAdd() {
                document.getElementById('crudId').value = '';
                document.querySelectorAll('#crudForm [data-field]').forEach(i => i.value = '');
                document.getElementById('modalTitle').innerText = 'Thêm học phần vào CTĐT';
                document.getElementById('crudModal').style.display = 'flex';
            }
            window.openAdd = openAdd;


            function openEdit(data) {
                document.getElementById('crudId').value = data.id;

                // Gán riêng cho select học phần (vì có nhiều option)
                const courseSelect = document.querySelector('#crudForm [data-field="course_id"]');
                if (courseSelect) {
                    courseSelect.value = data.course_id ?? '';
                }

                // Gán các trường khác
                document.querySelectorAll('#crudForm [data-field]').forEach(i => {
                    if (i.dataset.field !== 'course_id') {
                        i.value = data[i.dataset.field] ?? '';
                    }
                });

                document.getElementById('modalTitle').innerText = 'Chỉnh sửa học phần';
                document.getElementById('crudModal').style.display = 'flex';
            }
            window.openEdit = openEdit;

            // const form = document.getElementById('crudForm');
            // if (form) {
            //     form.addEventListener('submit', async e => {
            //         e.preventDefault();
            //         const data = {};
            //         document.querySelectorAll('#crudForm [data-field]').forEach(i => data[i.dataset
            //             .field] = i.value);
            //         if (document.getElementById('crudId').value) data.id = document.getElementById(
            //             'crudId').value;

            //         try {
            //             const res = await CRUD.postJson(ROUTE_STORE, data);
            //             CRUD.toast(res.success ? "✅ Lưu thành công" : "❌ Lỗi khi lưu", res.success);
            //             if (res.success) location.reload();
            //         } catch (err) {
            //             CRUD.toast("❌ Request lỗi: " + err);
            //         }
            //     });
            // }

            // const delBtn = document.getElementById('deleteBtn');
            // if (delBtn) {
            //     delBtn.addEventListener('click', async () => {
            //         const ids = CRUD.getSelectedIds();
            //         if (ids.length === 0) return CRUD.toast("⚠️ Chưa chọn học phần nào để xóa.");
            //         if (!confirm("Xóa " + ids.length + " học phần đã chọn?")) return;
            //         try {
            //             const res = await CRUD.postJson(ROUTE_DELETE_MULTI, {
            //                 ids
            //             });
            //             CRUD.toast(res.success ? "🗑️ Đã xóa thành công" : "❌ Lỗi khi xóa", res
            //                 .success);
            //             if (res.success) location.reload();
            //         } catch (err) {
            //             CRUD.toast("❌ Request lỗi: " + err);
            //         }
            //     });
            // }

            const form = document.getElementById('crudForm');
            if (form) {
                form.addEventListener('submit', async e => {
                    e.preventDefault();

                    const btn = form.querySelector('button[type="submit"]');
                    const originalText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '⏳ Đang lưu...';

                    const data = {};
                    document.querySelectorAll('#crudForm [data-field]').forEach(i => data[i.dataset
                        .field] = i.value);
                    if (document.getElementById('crudId').value) data.id = document.getElementById(
                        'crudId').value;

                    try {
                        const res = await CRUD.postJson(ROUTE_STORE, data);
                        CRUD.toast(res.success ? "Lưu thành công" : "Lỗi khi lưu", res.success);

                        if (res.success) setTimeout(() => location.reload(), 700);
                    } catch (err) {
                        CRUD.toast("Request lỗi: " + err);
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                });
            }

            const delBtn = document.getElementById('deleteBtn');
            if (delBtn) {
                delBtn.addEventListener('click', async () => {
                    const ids = CRUD.getSelectedIds();
                    if (ids.length === 0) return CRUD.toast("⚠️ Chưa chọn học phần nào để xóa.");
                    if (!confirm("Xóa " + ids.length + " học phần đã chọn?")) return;

                    const originalText = delBtn.innerHTML;
                    delBtn.disabled = true;
                    delBtn.innerHTML = '🗑️ Đang xóa...';

                    try {
                        const res = await CRUD.postJson(ROUTE_DELETE_MULTI, {
                            ids
                        });
                        CRUD.toast(res.success ? "🗑️ Đã xóa thành công" : "❌ " + (res.message ||
                            "Lỗi khi xóa"), res.success);
                        if (res.success) setTimeout(() => location.reload(), 700);
                    } catch (err) {
                        CRUD.toast("❌ Request lỗi: " + err);
                    } finally {
                        delBtn.disabled = false;
                        delBtn.innerHTML = originalText;
                    }
                });
            }

        });
    </script>
@endpush
