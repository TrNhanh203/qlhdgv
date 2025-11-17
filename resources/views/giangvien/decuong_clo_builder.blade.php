@extends('layouts.appGV')

@section('content')
    <div class="container-fluid">

        {{-- Header + nút về editor --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1">
                    Tiện ích xây dựng CLO cho đề cương
                </h5>
                <div class="small text-muted">
                    Học phần:
                    <strong>{{ $courseVersion->course_code }} - {{ $courseVersion->course_name }}</strong><br>
                    CTĐT: {{ $courseVersion->program_code }} - {{ $courseVersion->program_name }}
                    (Khóa: {{ $courseVersion->program_version_code }})<br>
                    Phiên bản đề cương: V{{ $courseVersion->version_no }}
                </div>
            </div>

            <a href="{{ route('giangvien.outlines.edit', ['courseVersion' => $courseVersion->id]) }}"
                class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Về trang soạn đề cương
            </a>
        </div>

        {{-- Panel chính --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><strong>Danh sách CLO của học phần</strong></span>

                {{-- Sau này sẽ gắn JS mở modal tạo mới --}}
                <button class="btn btn-primary btn-sm" type="button" id="btnAddClo">
                    <i class="bi bi-plus-circle"></i> Thêm CLO
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">Mã</th>
                                <th>Mô tả CLO</th>
                                <th style="width: 140px;">Mức Bloom</th>
                                <th style="width: 80px;" class="text-center">Sửa</th>
                                <th style="width: 80px;" class="text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clos as $clo)
                                <tr>
                                    <td><strong>{{ $clo->code }}</strong></td>
                                    <td>{{ $clo->description }}</td>
                                    <td>{{ $clo->bloom_level ?? '—' }}</td>
                                    <td class="text-center">
                                        {{-- Sau sẽ gắn JS mở modal sửa --}}
                                        <button class="btn btn-outline-secondary btn-sm btn-edit-clo"
                                            data-id="{{ $clo->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        {{-- Sau sẽ gắn JS xóa --}}
                                        <button class="btn btn-outline-danger btn-sm btn-delete-clo"
                                            data-id="{{ $clo->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Chưa có CLO nào được khai báo cho phiên bản đề cương này.
                                        <br>
                                        Nhấn <strong>"Thêm CLO"</strong> để bắt đầu soạn.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


            </div>

            {{-- Khối sau này dùng cho preview + ghi vào section --}}
            <div class="card-footer">
                <small class="text-muted d-block mb-2">
                    Sau khi hoàn thành danh sách CLO, hệ thống sẽ hỗ trợ sinh ra nội dung HTML
                    để chèn vào đề cương (mục Chuẩn đầu ra học phần).
                </small>

                {{-- Sau này thêm nút "Xem trước & Ghi vào section..." ở đây --}}

                <div class="text-end mt-3">
                    <button id="btnRenderCloToOutline" class="btn btn-success">
                        📄 Lưu CLO vào đề cương
                    </button>
                </div>

            </div>
        </div>
    </div>


    {{-- Modal Thêm / Sửa CLO --}}
    <div class="modal fade" id="cloModal" tabindex="-1" aria-labelledby="cloModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="cloForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cloModalLabel">Thêm CLO</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" id="clo_id" value="">

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Mã CLO <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="clo_code"
                                    placeholder="CLO1, CLO2,...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mức Bloom</label>
                                <select id="clo_bloom" class="form-select form-select-sm">
                                    <option value="">-- Chọn mức Bloom --</option>
                                    <option value="Remember">Remember (Nhớ)</option>
                                    <option value="Understand">Understand (Hiểu)</option>
                                    <option value="Apply">Apply (Vận dụng)</option>
                                    <option value="Analyze">Analyze (Phân tích)</option>
                                    <option value="Evaluate">Evaluate (Đánh giá)</option>
                                    <option value="Create">Create (Sáng tạo)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả CLO <span class="text-danger">*</span></label>
                            <textarea id="clo_description" rows="4" class="form-control form-control-sm" placeholder="Sinh viên có thể..."></textarea>
                        </div>

                        <div class="alert alert-warning py-2 small mb-0">
                            Gợi ý: Mô tả CLO nên bắt đầu bằng một động từ mức Bloom (ví dụ: trình bày, phân tích,
                            vận dụng, đánh giá, thiết kế, ...).
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                            Đóng
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            💾 Lưu CLO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="renderCloModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview nội dung CLO sau khi render</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn section muốn ghi vào:</label>
                        <select id="render_section_id" class="form-select">
                            <option value="">-- Chọn section --</option>

                            @foreach ($sections as $sec)
                                <option value="{{ $sec->section_template_id }}">
                                    {{ $sec->code }} - {{ $sec->title }}
                                </option>
                            @endforeach

                        </select>

                        @isset($sections)
                            @if ($sections->isEmpty())
                                <small class="text-danger">
                                    Chưa có cấu trúc section cho đề cương này. Vui lòng quay lại màn hình soạn đề cương,
                                    chọn mẫu đề cương trước rồi mới render CLO.
                                </small>
                            @endif
                        @endisset
                    </div>

                    <div class="border p-3 bg-light page" id="renderPreview"
                        style="min-height: 200px; white-space: pre-wrap;">
                        (Đang tạo preview...)
                    </div>

                </div>


                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <div style="max-width: 300px;">
                        <select id="render_mode" class="form-select">
                            <option value="replace">🔄 Ghi đè nội dung cũ</option>
                            <option value="prepend">⬆️ Chèn lên trên nội dung cũ</option>
                            <option value="append">⬇️ Chèn xuống dưới nội dung cũ</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" id="btnConfirmRender">Ghi vào đề cương</button>
                </div>
            </div>
        </div>
    </div>



    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const courseVersionId = {{ $courseVersion->id }};
                const baseUrl = "{{ url('giangvien/decuong/version/' . $courseVersion->id . '/clo') }}";
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const cloModalEl = document.getElementById('cloModal');
                const cloModal = new bootstrap.Modal(cloModalEl);

                const cloIdInput = document.getElementById('clo_id');
                const cloCodeInput = document.getElementById('clo_code');
                const cloDescInput = document.getElementById('clo_description');
                const cloBloomSelect = document.getElementById('clo_bloom');
                const cloForm = document.getElementById('cloForm');

                // === 1. Click Thêm CLO ===
                document.getElementById('btnAddClo')?.addEventListener('click', function() {
                    cloIdInput.value = '';
                    cloCodeInput.value = '';
                    cloDescInput.value = '';
                    cloBloomSelect.value = '';

                    document.getElementById('cloModalLabel').textContent = 'Thêm CLO';
                    cloModal.show();
                });

                // === 2. Click Sửa CLO ===
                document.querySelectorAll('.btn-edit-clo').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const cloId = this.dataset.id;
                        if (!cloId) return;

                        try {
                            const res = await fetch(`${baseUrl}/${cloId}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();

                            if (!res.ok || !data.success) {
                                throw new Error(data.message || 'Không thể tải CLO.');
                            }

                            const clo = data.data;
                            cloIdInput.value = clo.id;
                            cloCodeInput.value = clo.code || '';
                            cloDescInput.value = clo.description || '';
                            cloBloomSelect.value = clo.bloom_level || '';

                            document.getElementById('cloModalLabel').textContent = 'Chỉnh sửa CLO';
                            cloModal.show();
                        } catch (e) {
                            alert('Lỗi: ' + e.message);
                        }
                    });
                });

                // === 3. Submit form (Thêm / Sửa) ===
                cloForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const cloId = cloIdInput.value.trim();
                    const payload = {
                        code: cloCodeInput.value.trim(),
                        description: cloDescInput.value.trim(),
                        bloom_level: cloBloomSelect.value.trim(),
                    };

                    if (!payload.code) {
                        alert('Vui lòng nhập mã CLO.');
                        return;
                    }
                    if (!payload.description) {
                        alert('Vui lòng nhập mô tả CLO.');
                        return;
                    }

                    let url = baseUrl;
                    let method = 'POST';

                    if (cloId) {
                        url = `${baseUrl}/${cloId}`;
                        method = 'PUT';
                    }

                    try {
                        const res = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await res.json();

                        if (!res.ok || !data.success) {
                            throw new Error(data.message || 'Không thể lưu CLO.');
                        }

                        alert(data.message || 'Đã lưu CLO.');
                        cloModal.hide();
                        // Đơn giản: reload lại để đồng bộ bảng
                        window.location.reload();
                    } catch (e) {
                        alert('Lỗi: ' + e.message);
                    }
                });

                // === 4. Xóa CLO ===
                document.querySelectorAll('.btn-delete-clo').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const cloId = this.dataset.id;
                        if (!cloId) return;

                        if (!confirm('Bạn chắc chắn muốn xóa CLO này?')) {
                            return;
                        }

                        try {
                            const res = await fetch(`${baseUrl}/${cloId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                }
                            });

                            const data = await res.json();

                            if (!res.ok || !data.success) {
                                throw new Error(data.message || 'Không thể xóa CLO.');
                            }

                            alert(data.message || 'Đã xóa CLO.');
                            window.location.reload();
                        } catch (e) {
                            alert('Lỗi: ' + e.message);
                        }
                    });
                });
            });


            document.getElementById('btnRenderCloToOutline')
                .addEventListener('click', async () => {

                    const preview = document.getElementById('renderPreview');
                    preview.innerHTML = "Đang tạo preview...";

                    try {
                        // GỌI ĐÚNG ROUTE preview (KHÔNG sử dụng preview=1 nữa)
                        const res = await fetch(
                            "{{ route('giangvien.outlines.clo.preview', $courseVersion->id) }}", {
                                headers: {
                                    'Accept': 'text/html'
                                }
                            }
                        );

                        const html = await res.text();

                        // ĐÚNG: đưa HTML preview vào modal
                        preview.innerHTML = html;

                    } catch (e) {
                        preview.innerHTML = "Lỗi tải preview";
                    }

                    new bootstrap.Modal(document.getElementById('renderCloModal')).show();
                });



            document.getElementById('btnConfirmRender')
                .addEventListener('click', async () => {
                    const sectionId = document.getElementById('render_section_id').value;
                    if (!sectionId) {
                        alert("Vui lòng chọn section để ghi vào.");
                        return;
                    }

                    const res = await fetch("{{ route('giangvien.outlines.clo.render', $courseVersion->id) }}", {
                        method: 'POST',
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },

                        body: JSON.stringify({
                            section_template_id: sectionId,
                            mode: document.getElementById('render_mode').value
                        })

                    });

                    const data = await res.json();

                    if (!data.success) {
                        alert(data.message);
                        return;
                    }

                    alert("Đã ghi CLO vào đề cương!");

                    // Điều hướng về trang soạn chính
                    window.location.href = "{{ route('giangvien.outlines.edit', $courseVersion->id) }}";
                });
        </script>
    @endpush
@endsection
