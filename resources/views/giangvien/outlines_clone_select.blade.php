@extends('layouts.appGV')

@section('content')
    <div class="container-fluid py-3">

        {{-- Breadcrumb + tiêu đề --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1">
                    Nhân bản đề cương cho học phần
                </h5>
                <div class="text-muted">
                    @if ($assignment)
                        <strong>{{ $assignment->course_code ?? '---' }} - {{ $assignment->course_name ?? '---' }}</strong>
                    @else
                        <span class="text-danger">Không tìm thấy thông tin phân công.</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('giangvien.outlines.index') }}" class="btn btn-outline-secondary btn-sm">
                ← Quay về danh sách phân công
            </a>
        </div>

        {{-- Thông tin assignment hiện tại --}}
        @if ($assignment)
            <div class="card mb-3">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="fw-bold">Học phần</div>
                            <div>{{ $assignment->course_code }} - {{ $assignment->course_name }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">Chương trình đào tạo</div>
                            <div>
                                {{ $assignment->program_code }} - {{ $assignment->program_name }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">Phiên bản CTĐT</div>
                            <div>
                                {{ $assignment->program_version_code ?? '---' }}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="fw-bold">Năm học</div>
                            <div>{{ $assignment->academic_year_code ?? '---' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">Học kỳ</div>
                            <div>{{ $assignment->semester_name ?? '---' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">Version hiện tại</div>
                            <div>
                                @if ($assignment->outline_course_version_id)
                                    ID: {{ $assignment->outline_course_version_id }}
                                @else
                                    <span class="text-muted">Chưa có phiên bản đề cương</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <small class="text-muted d-block mt-1">
                        Chọn một phiên bản đề cương cũ bên dưới (có thể thuộc CTĐT / khóa / năm học khác) để nhân bản nội
                        dung sang khoá hiện tại.
                    </small>
                </div>
            </div>
        @endif



        {{-- Danh sách phiên bản nguồn --}}
        <div class="card">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span class="fw-bold">Các phiên bản đề cương có thể dùng làm nguồn</span>
                <small class="text-muted">
                    Dữ liệu hiển thị theo thứ tự mới → cũ
                </small>
            </div>

            <div class="card-body p-0">
                @if ($sourceVersions->isEmpty())
                    <div class="p-3 text-muted">
                        Hiện chưa tìm thấy phiên bản đề cương nào khác cùng học phần
                        (theo mã học phần: <strong>{{ $assignment->course_code ?? '---' }}</strong>).
                        Bạn có thể quay lại và tạo đề cương mới từ đầu.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Version</th>
                                    <th>Học phần</th>
                                    <th>Chương trình / Khóa</th>
                                    <th>Năm học / Học kỳ</th>
                                    <th style="width: 120px;">Trạng thái</th>
                                    <th style="width: 220px;" class="text-end">Thao tác</th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach ($sourceVersions as $version)
                                    <tr>
                                        <td>
                                            V{{ $version->version_no }}
                                            <div class="text-muted small">#{{ $version->version_id }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $version->course_code }} - {{ $version->course_name }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $version->program_code }} - {{ $version->program_name }}</div>
                                            <div class="text-muted small">
                                                Phiên bản CTĐT: {{ $version->program_version_code ?? '---' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $version->academic_year_code ?? '---' }}</div>
                                            <div class="text-muted small">
                                                {{ $version->semester_name ?? '---' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $version->status ?? 'draft' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-outline-info btn-sm me-1 btn-preview"
                                                data-source-version="{{ $version->version_id }}">
                                                Xem trước
                                            </button>

                                            <form
                                                action="{{ route('giangvien.outlines.clone.perform', [
                                                    'assignment' => $assignment->id,
                                                    'sourceVersion' => $version->version_id,
                                                ]) }}"
                                                method="POST" class="d-inline-block form-clone">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    Nhân bản từ bản này
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>


                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal preview --}}
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title">Xem trước đề cương nguồn</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="previewModalBody">
                    <div class="text-center text-muted py-5">
                        Đang tải nội dung...
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const previewButtons = document.querySelectorAll('.btn-preview');
            const modalEl = document.getElementById('previewModal');
            const modalBody = document.getElementById('previewModalBody');

            let previewModal = new bootstrap.Modal(modalEl);

            previewButtons.forEach(btn => {
                btn.addEventListener('click', async function() {
                    const sourceId = this.getAttribute('data-source-version');

                    modalBody.innerHTML = `
                <div class="text-center text-muted py-5">
                    Đang tải nội dung đề cương...
                </div>
            `;
                    previewModal.show();

                    try {
                        const url =
                            "{{ route('giangvien.outlines.clone.preview', ['sourceVersion' => '_ID_']) }}"
                            .replace('_ID_', sourceId);

                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();

                        if (!data.success) {
                            throw new Error(data.message || "Không tải được nội dung.");
                        }

                        modalBody.innerHTML = data.html;

                    } catch (err) {
                        modalBody.innerHTML = `
                    <div class="alert alert-danger mt-3">
                        Lỗi: ${err.message}
                    </div>
                `;
                    }
                });
            });
        });


        document.querySelectorAll('.form-clone').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // chặn submit mặc định

                Swal.fire({
                    title: 'Xác nhận nhân bản đề cương',
                    text: 'Mọi nội dung hiện tại của đề cương này (bao gồm cả CLO và mapping nếu có) sẽ được thay thế bằng nội dung của phiên bản bạn vừa chọn. Bạn có chắc chắn muốn tiếp tục?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Đồng ý nhân bản',
                    cancelButtonText: 'Huỷ',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // chỉ submit thật sự khi GV đồng ý
                    }
                });
            });
        });
    </script>
@endpush
