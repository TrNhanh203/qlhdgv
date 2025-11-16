@extends('layouts.appbomon')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <i class="bi bi-journal-text me-2"></i>
                Phân công soạn đề cương theo CTĐT
            </h4>
        </div>

        {{-- 🔎 Bộ lọc: chọn KHÓA CTĐT + HỌC PHẦN --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('truongbomon.quanlyhocphan.phancongdecuong.index') }}" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Chọn khóa CTĐT</label>
                        <select name="program_version_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Chọn một khóa CTĐT --</option>
                            @foreach ($programVersions as $pv)
                                <option value="{{ $pv->id }}"
                                    {{ $selectedProgramVersion == $pv->id ? 'selected' : '' }}>
                                    {{ $pv->program_code }} - {{ $pv->program_name }} | Khóa: {{ $pv->version_code }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Chọn học phần</label>
                        <select name="course_id" class="form-select" {{ $selectedProgramVersion ? '' : 'disabled' }}
                            onchange="this.form.submit()">
                            <option value="">-- Tất cả học phần --</option>
                            @foreach ($coursesInProgram as $c)
                                <option value="{{ $c->id }}" {{ $selectedCourseId == $c->id ? 'selected' : '' }}>
                                    {{ $c->course_code }} - {{ $c->course_name }}
                                </option>
                            @endforeach
                        </select>
                        @if (!$selectedProgramVersion)
                            <small class="text-muted">Vui lòng chọn khóa CTĐT trước.</small>
                        @endif
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================== CARD CHÍNH ====================== --}}
        <div class="card">
            <div class="card-header">
                @if ($selectedProgramVersion)
                    <strong>Các phiên bản đề cương trong khóa CTĐT đã chọn</strong>
                @else
                    <strong>Vui lòng chọn một khóa CTĐT</strong>
                @endif
            </div>

            <div class="card-body p-0">

                {{-- === ACTION PANEL === --}}
                @if ($selectedProgramVersion && $selectedCourseId)
                    @php
                        $currentCourse = $coursesInProgram->firstWhere('id', $selectedCourseId);
                        $hasOutline = $outlineVersions->isNotEmpty();
                    @endphp

                    <div class="px-3 pt-3 pb-2 border-bottom">

                        <div class="fw-semibold">Học phần đang chọn:</div>

                        <div class="small text-muted mb-2">
                            {{ $currentCourse?->course_code }} - {{ $currentCourse?->course_name }}
                            <br>

                            @if ($hasOutline)
                                <span class="badge bg-success mt-1">
                                    Đã có {{ $outlineVersions->count() }} phiên bản đề cương
                                </span>
                            @else
                                <span class="badge bg-warning text-dark mt-1">Chưa có đề cương nào</span>
                            @endif

                            @if ($initialAssignments > 0)
                                <span class="badge bg-info mt-1">
                                    Đã phân công soạn mới ({{ $initialAssignments }} GV)
                                </span>
                            @endif
                        </div>

                        {{-- BUTTONS --}}
                        <div class="d-flex gap-2">


                            <a href="{{ route('truongbomon.quanlyhocphan.phancongdecuong.assignNew', [
                                'program_version_id' => $selectedProgramVersion,
                                'course_id' => $selectedCourseId,
                            ]) }}"
                                class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil-square"></i> Phân công soạn mới
                            </a>
                        </div>

                    </div>
                @endif

                {{-- === TABLE VERSION LIST === --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Học phần</th>
                                <th>Phiên bản đề cương</th>
                                <th>Năm học / Học kỳ</th>
                                <th>Đã phân công</th>
                                <th style="width: 120px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if (!$selectedProgramVersion || !$selectedCourseId)
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Vui lòng chọn Khóa CTĐT và Học phần để xem đề cương.
                                    </td>
                                </tr>
                            @else
                                @forelse ($outlineVersions as $row)
                                    <tr>
                                        <td><strong>{{ $row->course_code }}</strong> – {{ $row->course_name }}</td>

                                        <td>
                                            <span class="badge bg-secondary">V{{ $row->version_no }}</span>
                                            <div class="small text-muted">{{ $row->status }}</div>
                                        </td>

                                        <td>{{ $row->year_code ?? '—' }} / {{ $row->semester_name ?? '—' }}</td>

                                        <td>{{ $assignmentCounts[$row->id] ?? 0 }} giảng viên</td>

                                        <td>
                                            <a href="{{ route('truongbomon.quanlyhocphan.phancongdecuong.edit', $row->id) }}"
                                                class="btn btn-sm btn-primary">
                                                Phân công
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Chưa có phiên bản đề cương nào cho học phần này.
                                        </td>
                                    </tr>
                                @endforelse
                            @endif

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('select[name="program_version_id"]').select2({
                    placeholder: "Chọn khóa CTĐT",
                    width: '100%'
                });
                $('select[name="course_id"]').select2({
                    placeholder: "Chọn học phần",
                    width: '100%'
                });
            });
        </script>
    @endpush
@endsection
