@extends('layouts.appGV')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="container-fluid">

        <h4 class="mb-3">
            <i class="bi bi-diagram-3"></i>
            Mapping CLO – PI (tự suy ra PLO)
        </h4>

        {{-- THÔNG TIN HỌC PHẦN / CTĐT --}}
        <div class="card mb-3">
            <div class="card-body">
                <div><strong>Học phần:</strong>
                    {{ $courseVersion->course_code }} – {{ $courseVersion->course_name }}
                </div>
                <div><strong>Chương trình đào tạo:</strong>
                    {{ $courseVersion->program_code }} – {{ $courseVersion->program_name }}
                    ({{ $courseVersion->program_version_code }})
                </div>
                <div><strong>Năm học / Học kỳ:</strong>
                    {{ $courseVersion->academic_year_code ?? '---' }}
                    @if ($courseVersion->semester_name)
                        – {{ $courseVersion->semester_name }}
                    @endif
                </div>
                <div><strong>Phiên bản đề cương:</strong>
                    V{{ $courseVersion->version_no }} – {{ $courseVersion->status }}
                </div>
            </div>
        </div>

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        @endif

        <div class="alert alert-info small">
            <strong>Quy ước mức độ:</strong>
            I – Introduce (Giới thiệu),
            R – Reinforce (Củng cố),
            M – Master (Làm chủ),
            A – Assessment (Đánh giá).
            <br>
            Giảng viên chỉ cần chọn mức độ cho từng cặp CLO – PI. Hệ thống có thể tự tổng hợp tương ứng sang PLO.
        </div>

        @php
            // Group PI theo PLO để hiển thị gọn hơn
            $pisByPlo = $pis->groupBy('plo_id');
            $plosById = $plos->keyBy('id');
        @endphp

        <form method="POST"
            action="{{ route('giangvien.outlines.cloMapping.save', ['courseVersion' => $courseVersion->id]) }}">
            @csrf

            <div class="row">
                {{-- CỘT TRÁI: DANH SÁCH CLO --}}
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-header py-2">
                            <strong>Các CLO của học phần</strong>
                        </div>
                        <div class="list-group list-group-flush" id="cloList">
                            @foreach ($clos as $index => $clo)
                                <button type="button"
                                    class="list-group-item list-group-item-action clo-item {{ $index === 0 ? 'active' : '' }}"
                                    data-clo-id="{{ $clo->id }}">
                                    <div class="fw-bold">{{ $clo->code }}</div>
                                    <div class="small text-muted">
                                        {{ Str::limit($clo->description, 80) }}
                                    </div>
                                </button>
                            @endforeach

                        </div>
                        <a href="{{ route('giangvien.outlines.cloMapping.preview', ['courseVersion' => $courseVersion->id]) }}"
                            class="btn btn-outline-secondary">
                            👁 Xem bảng tổng hợp & chèn vào đề cương
                        </a>
                    </div>
                </div>

                {{-- CỘT PHẢI: VÙNG MAPPING CHO CLO ĐANG CHỌN --}}
                <div class="col-md-9 mb-3">
                    @foreach ($clos as $index => $clo)
                        <div class="card clo-panel {{ $index === 0 ? '' : 'd-none' }}" data-clo-id="{{ $clo->id }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">
                                        {{ $clo->code }} – {{ $clo->description }}
                                    </div>
                                    <div class="small text-muted">
                                        Mapping chuẩn đầu ra học phần này với các chỉ báo PI của CTĐT.
                                    </div>
                                </div>
                                <span class="badge bg-primary">CLO hiện tại</span>
                            </div>

                            <div class="card-body p-2">
                                @foreach ($pisByPlo as $ploId => $pisOfPlo)
                                    @php
                                        $plo = $plosById[$ploId] ?? null;
                                    @endphp

                                    <div class="mb-3 border rounded">
                                        <div class="px-2 py-1 bg-light">
                                            <strong>{{ $plo?->code ?? 'PLO ?' }}</strong>
                                            @if ($plo && $plo->description)
                                                – <span class="small">{{ $plo->description }}</span>
                                            @endif
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0 align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 140px;">Mã PI</th>
                                                        <th>Mô tả PI</th>
                                                        <th style="width: 140px;" class="text-center">Mức độ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pisOfPlo as $pi)
                                                        @php
                                                            $selected = $cloPiMatrix[$clo->id][$pi->id] ?? '';
                                                        @endphp
                                                        <tr>
                                                            <td class="text-nowrap">
                                                                {{ $plo?->code }}.{{ $pi->code }}
                                                            </td>
                                                            <td class="small">
                                                                {{ $pi->description }}
                                                            </td>
                                                            <td class="text-center">
                                                                <select
                                                                    name="clo_pi[{{ $clo->id }}][{{ $pi->id }}]"
                                                                    class="form-select form-select-sm d-inline-block w-auto">
                                                                    <option value="">—</option>
                                                                    <option value="I"
                                                                        {{ $selected === 'I' ? 'selected' : '' }}>I
                                                                    </option>
                                                                    <option value="R"
                                                                        {{ $selected === 'R' ? 'selected' : '' }}>R
                                                                    </option>
                                                                    <option value="M"
                                                                        {{ $selected === 'M' ? 'selected' : '' }}>M
                                                                    </option>
                                                                    <option value="A"
                                                                        {{ $selected === 'A' ? 'selected' : '' }}>A
                                                                    </option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            💾 Lưu mapping CLO – PI
                        </button>
                        <a href="{{ route('giangvien.outlines.edit', ['courseVersion' => $courseVersion->id]) }}"
                            class="btn btn-outline-secondary">
                            Quay lại đề cương
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cloItems = document.querySelectorAll('.clo-item');
            const cloPanels = document.querySelectorAll('.clo-panel');

            cloItems.forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.cloId;

                    // active bên list CLO
                    cloItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    // show panel tương ứng, hide panel khác
                    cloPanels.forEach(panel => {
                        if (panel.dataset.cloId === id) {
                            panel.classList.remove('d-none');
                        } else {
                            panel.classList.add('d-none');
                        }
                    });
                });
            });
        });
    </script>
@endpush
