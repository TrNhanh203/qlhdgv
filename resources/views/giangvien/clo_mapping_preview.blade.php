@extends('layouts.appGV')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">
            <i class="bi bi-eye"></i>
            Xem bảng tổng hợp mapping CLO – PI
        </h4>

        <div class="card mb-3">
            <div class="card-body">
                <div><strong>Học phần:</strong>
                    {{ $courseVersion->course_code }} – {{ $courseVersion->course_name }}
                </div>
                <div><strong>CTĐT:</strong>
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        @endif

        @php
            // Lọc PI nào thực sự có mapping
            $usedPiIds = [];
            foreach ($cloPiMatrix as $cloId => $row) {
                foreach ($row as $piId => $level) {
                    if (!empty($level)) {
                        $usedPiIds[$piId] = true;
                    }
                }
            }
            $filteredPis = $pis->filter(fn($pi) => isset($usedPiIds[$pi->id]));
            $pisByPlo = $filteredPis->groupBy('plo_id');

            // Lọc CLO nào có mapping
            $usedCloIds = [];
            foreach ($cloPiMatrix as $cloId => $row) {
                $has = false;
                foreach ($row as $piId => $level) {
                    if (!empty($level)) {
                        $has = true;
                        break;
                    }
                }
                if ($has) {
                    $usedCloIds[$cloId] = true;
                }
            }
            $filteredClos = $clos->filter(fn($clo) => isset($usedCloIds[$clo->id]));

            // Nếu không có dữ liệu thì out luôn
            $hasData = $filteredPis->isNotEmpty() && $filteredClos->isNotEmpty();

            // Chuẩn bị số liệu tổng hợp
            $sumTotal = [];
            $sumI = [];
            $sumR = [];
            $sumM = [];
            $sumA = [];

            if ($hasData) {
                foreach ($filteredClos as $clo) {
                    foreach ($pisByPlo as $ploId => $pisOfPlo) {
                        foreach ($pisOfPlo as $pi) {
                            $level = $cloPiMatrix[$clo->id][$pi->id] ?? '';
                            if ($level === '' || $level === null) {
                                continue;
                            }

                            // Tổng số lượng đóng góp
                            $sumTotal[$pi->id] = ($sumTotal[$pi->id] ?? 0) + 1;

                            // Có thể là "I", "R", "M", "A" hoặc "I,A"...
                            if (strpos($level, 'I') !== false) {
                                $sumI[$pi->id] = ($sumI[$pi->id] ?? 0) + 1;
                            }
                            if (strpos($level, 'R') !== false) {
                                $sumR[$pi->id] = ($sumR[$pi->id] ?? 0) + 1;
                            }
                            if (strpos($level, 'M') !== false) {
                                $sumM[$pi->id] = ($sumM[$pi->id] ?? 0) + 1;
                            }
                            if (strpos($level, 'A') !== false) {
                                $sumA[$pi->id] = ($sumA[$pi->id] ?? 0) + 1;
                            }
                        }
                    }
                }
            }
        @endphp

        @if (!$hasData)
            <div class="alert alert-warning">
                Chưa có dữ liệu mapping CLO – PI nào. Vui lòng quay lại màn hình mapping và thiết lập mức độ trước.
            </div>
        @else
            {{-- Bảng tổng hợp giống format đề cương --}}
            <div class="card mb-3">
                <div class="card-header">
                    <strong>Bảng PLO và PI (mapping với CLO)</strong>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-bordered mb-0 align-middle">
                        <thead>
                            {{-- Hàng 1: tiêu đề chung --}}
                            <tr class="table-light">
                                <th rowspan="3" style="width: 200px; text-align:center; vertical-align:middle;">
                                    CLO
                                </th>
                                <th colspan="{{ $filteredPis->count() }}" style="text-align:center;">
                                    PLO và PI
                                </th>
                            </tr>
                            {{-- Hàng 2: PLO --}}
                            <tr class="table-light">
                                @foreach ($pisByPlo as $ploId => $pisOfPlo)
                                    @php
                                        $plo = $plos->firstWhere('id', $ploId);
                                    @endphp
                                    <th class="text-center" colspan="{{ $pisOfPlo->count() }}"
                                        title="{{ $plo?->description }}">
                                        {{ $plo?->code ?? 'PLO ?' }}
                                    </th>
                                @endforeach
                            </tr>
                            {{-- Hàng 3: PI --}}
                            <tr class="table-light">
                                @foreach ($pisByPlo as $ploId => $pisOfPlo)
                                    @php
                                        $plo = $plos->firstWhere('id', $ploId);
                                    @endphp
                                    @foreach ($pisOfPlo as $pi)
                                        <th class="text-center small" title="{{ $pi->description }}">
                                            {{ $plo?->code }}.{{ $pi->code }}
                                        </th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Các hàng CLO --}}
                            @foreach ($filteredClos as $clo)
                                <tr>
                                    <th scope="row" title="{{ $clo->description }}" style="vertical-align:middle;">
                                        {{ $clo->code }}
                                    </th>
                                    @foreach ($pisByPlo as $ploId => $pisOfPlo)
                                        @foreach ($pisOfPlo as $pi)
                                            @php
                                                $level = $cloPiMatrix[$clo->id][$pi->id] ?? '';
                                            @endphp
                                            <td class="text-center">
                                                {{ $level }}
                                            </td>
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach

                            {{-- Dòng: Tổng hợp số lượng mức độ đóng góp --}}
                            <tr>
                                <th>
                                    Tổng hợp số lượng<br />mức độ đóng góp
                                </th>
                                @foreach ($pisByPlo as $ploId => $pisOfPlo)
                                    @foreach ($pisOfPlo as $pi)
                                        <td class="text-center">
                                            {{ $sumTotal[$pi->id] ?? '' }}
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>

                            {{-- Dòng: Mức I --}}
                            <tr>
                                <th>Mức I</th>
                                @foreach ($pisByPlo as $ploId => $pisOfPlo)
                                    @foreach ($pisOfPlo as $pi)
                                        <td class="text-center">
                                            {{ $sumI[$pi->id] ?? '' }}
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>

                            {{-- Dòng: Mức R --}}
                            <tr>
                                <th>Mức R</th>
                                @foreach ($pisByPlo as $ploId => $pisOfPlo)
                                    @foreach ($pisOfPlo as $pi)
                                        <td class="text-center">
                                            {{ $sumR[$pi->id] ?? '' }}
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>

                            {{-- Dòng: Mức M --}}
                            <tr>
                                <th>Mức M</th>
                                @foreach ($pisByPlo as $ploId => $pisOfPlo)
                                    @foreach ($pisOfPlo as $pi)
                                        <td class="text-center">
                                            {{ $sumM[$pi->id] ?? '' }}
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>

                            {{-- Nếu muốn thêm Mức A thì mở comment dưới --}}
                            {{-- <tr>
                            <th>Mức A</th>
                            @foreach ($pisByPlo as $ploId => $pisOfPlo)
                                @foreach ($pisOfPlo as $pi)
                                    <td class="text-center">
                                        {{ $sumA[$pi->id] ?? '' }}
                                    </td>
                                @endforeach
                            @endforeach
                        </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Form chọn section + cách chèn --}}
        <form method="POST"
            action="{{ route('giangvien.outlines.cloMapping.render', ['courseVersion' => $courseVersion->id]) }}">
            @csrf

            <div class="card mb-4">
                <div class="card-header">
                    <strong>Chèn bảng mapping vào đề cương</strong>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="section_select" class="form-label">Chọn mục đề cương</label>
                        <select name="section_template_id" id="section_select" class="form-select">
                            @foreach ($sections as $sec)
                                <option value="{{ $sec->id }}">
                                    {{ $sec->code }} – {{ $sec->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cách chèn nội dung</label>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="insert_mode" id="insert_append"
                                value="append" checked>
                            <label class="form-check-label" for="insert_append">
                                Chèn <strong>phía dưới</strong> nội dung hiện tại của mục đã chọn
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="insert_mode" id="insert_prepend"
                                value="prepend">
                            <label class="form-check-label" for="insert_prepend">
                                Chèn <strong>phía trên</strong> nội dung hiện tại của mục đã chọn
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="insert_mode" id="insert_replace"
                                value="replace">
                            <label class="form-check-label" for="insert_replace">
                                <strong>Ghi đè hoàn toàn</strong> nội dung của mục đã chọn bằng bảng mapping
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        📥 Chèn bảng vào đề cương
                    </button>
                    <a href="{{ route('giangvien.outlines.cloMapping.index', ['courseVersion' => $courseVersion->id]) }}"
                        class="btn btn-outline-secondary">
                        Quay lại màn hình mapping
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
