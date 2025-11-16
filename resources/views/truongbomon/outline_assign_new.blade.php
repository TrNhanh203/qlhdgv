@extends('layouts.appbomon')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                Phân công soạn đề cương (soạn mới)
            </h5>
            <a href="{{ route('truongbomon.quanlyhocphan.phancongdecuong.index', [
                'program_version_id' => request('program_version_id'),
                'course_id' => request('course_id'),
            ]) }}"
                class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Về danh sách
            </a>
        </div>

        {{-- Thông tin học phần + CTĐT --}}
        <div class="card mb-3">
            <div class="card-body">
                <p class="mb-1">
                    <strong>Học phần:</strong>
                    {{ $info->course_code }} - {{ $info->course_name }}
                </p>
                <p class="mb-1">
                    <strong>Chương trình:</strong>
                    {{ $info->program_code }} - {{ $info->program_name }}
                </p>
                <p class="mb-1">
                    <strong>Khóa CTĐT:</strong>
                    {{ $info->program_version_code }}
                </p>
                <p class="mb-1">
                    <strong>Năm học / Học kỳ (dự kiến):</strong>
                    {{ $info->year_code ?? '—' }} / {{ $info->semester_name ?? '—' }}
                </p>
                <p class="mb-1">
                    <strong>Trạng thái đề cương:</strong>
                    <span class="badge bg-warning text-dark">
                        Chưa có phiên bản đề cương – đang phân công soạn mới
                    </span>
                </p>
            </div>
        </div>

        {{-- Bảng phân công giảng viên --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Chọn giảng viên được phân công soạn đề cương</span>
                <button id="btnSaveAssignments" class="btn btn-primary btn-sm">
                    💾 Lưu phân công
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="chkAll">
                                </th>
                                <th>Giảng viên</th>
                                <th>Vai trò</th>
                                <th>Hạn hoàn thành</th>
                                <th>Ghi chú</th>
                                <th>Trạng thái hiện tại</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lecturers as $lec)
                                @php
                                    $assign = $existingAssignments[$lec->id] ?? null;
                                @endphp
                                <tr data-lecture-id="{{ $lec->id }}">
                                    <td>
                                        <input type="checkbox" class="chk-assign" {{ $assign ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        {{ $lec->full_name }}
                                        @if (!empty($lec->lecturer_code))
                                            <div class="text-muted small">
                                                {{ $lec->lecturer_code }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm sel-role">
                                            <option value="">-- Chọn vai trò --</option>
                                            <option value="chu_bien"
                                                {{ $assign && $assign->role === 'chu_bien' ? 'selected' : '' }}>Chủ biên
                                            </option>
                                            <option value="dong_bien"
                                                {{ $assign && $assign->role === 'dong_bien' ? 'selected' : '' }}>Đồng biên
                                            </option>
                                            <option value="tham_gia"
                                                {{ $assign && $assign->role === 'tham_gia' ? 'selected' : '' }}>Tham gia
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="date" class="form-control form-control-sm inp-due-date"
                                            value="{{ $assign && $assign->due_date ? $assign->due_date : '' }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm inp-note"
                                            value="{{ $assign->note ?? '' }}">
                                    </td>
                                    <td>
                                        @if ($assign)
                                            <span class="badge bg-secondary">{{ $assign->status }}</span>
                                        @else
                                            <span class="text-muted small">Chưa phân công</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chkAll = document.getElementById('chkAll');
            const saveBtn = document.getElementById('btnSaveAssignments');

            if (chkAll) {
                chkAll.addEventListener('change', function() {
                    const checked = this.checked;
                    document.querySelectorAll('.chk-assign').forEach(cb => {
                        cb.checked = checked;
                    });
                });
            }

            function collectAssignments() {
                const assignments = [];

                document.querySelectorAll('tbody tr[data-lecture-id]').forEach(row => {
                    const lectureId = row.dataset.lectureId;
                    const chk = row.querySelector('.chk-assign');

                    if (!chk || !chk.checked) return;

                    const roleEl = row.querySelector('.sel-role');
                    const dueEl = row.querySelector('.inp-due-date');
                    const noteEl = row.querySelector('.inp-note');

                    const role = roleEl ? roleEl.value.trim() : '';
                    const dueDate = dueEl ? dueEl.value : '';
                    const note = noteEl ? noteEl.value.trim() : '';

                    if (!role) {
                        throw new Error('Vui lòng chọn vai trò cho tất cả giảng viên được tick.');
                    }

                    assignments.push({
                        lecture_id: parseInt(lectureId, 10),
                        role: role,
                        due_date: dueDate || null,
                        note: note || null,
                    });
                });

                if (assignments.length === 0) {
                    throw new Error('Bạn chưa chọn giảng viên nào để phân công.');
                }

                return {
                    assignments
                };
            }

            if (saveBtn) {
                saveBtn.addEventListener('click', async function() {
                    try {
                        const payload = collectAssignments();

                        const res = await fetch(
                            "{{ route('truongbomon.quanlyhocphan.phancongdecuong.saveNew', $info->program_course_id) }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name=\"csrf-token\"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify(payload),
                            }
                        );

                        const data = await res.json();

                        if (!res.ok || !data.success) {
                            throw new Error(data.message || 'Không thể lưu phân công.');
                        }

                        alert('✅ Đã lưu phân công soạn mới cho học phần.');
                    } catch (e) {
                        console.error(e);
                        alert('❌ Lưu thất bại: ' + e.message);
                    }
                });
            }
        });
    </script>
@endpush
