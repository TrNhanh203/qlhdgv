@extends($layout ?? 'layouts.apptruongkhoa')

@section('content')
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                🧭 Khung Chương trình Đào tạo – {{ $version->program_code }} ({{ $version->version_code }})
            </h3>
            <a href="{{ route('truongkhoa.ctdtkhung.index', ['version_id' => $version->id]) }}"
                class="btn btn-outline-primary">
                🛠 Quản lý khung CTĐT
            </a>
        </div>

        <form method="GET" id="versionForm" class="mb-4">
            <label class="form-label fw-semibold me-2">Chọn phiên bản CTĐT:</label>
            <select class="form-select w-auto d-inline-block" onchange="if(this.value) window.location.href=this.value;">
                @foreach ($allVersions as $v)
                    <option value="{{ route('truongkhoa.ctdtkhung.overview', ['version_id' => $v->id]) }}"
                        {{ $v->id == $version->id ? 'selected' : '' }}>
                        {{ $v->program_code }} – {{ $v->version_code }}
                    </option>
                @endforeach
            </select>
        </form>

        {{-- ==================== DANH SÁCH HỌC PHẦN THEO NHÓM ==================== --}}
        @foreach ($groups as $type => $items)
            @php
                $typeLabel = match ($type) {
                    'kien_thuc_chung' => 'Kiến thức chung',
                    'kien_thuc_khoa_hoc_co_ban' => 'Kiến thức khoa học cơ bản',
                    'kien_thuc_bo_tro' => 'Kiến thức bổ trợ',
                    'kien_thuc_co_so_nganh_lien_nganh' => 'Kiến thức cơ sở ngành / liên ngành',
                    'kien_thuc_chuyen_nganh' => 'Kiến thức chuyên ngành',
                    'hoc_phan_nghe_nghiep' => 'Học phần nghề nghiệp (trải nghiệm nghề nghiệp)',
                    'hoc_phan_thuc_tap_tot_nghiep' => 'Học phần thực tập tốt nghiệp (tập sự nghề nghiệp)',
                    'hoc_phan_tot_nghiep' => 'Học phần tốt nghiệp',
                    'khoi_kien_thuc_dieu_kien_tot_nghiep' => 'Khối kiến thức điều kiện xét tốt nghiệp',
                    'khoi_kien_thuc_ky_su_dac_thu' => 'Khối kiến thức học kỹ sư đặc thù',
                    'do_an_thuc_tap' => 'Đồ án / Thực tập',
                    default => 'Khác',
                };

                $borderColor = match ($type) {
                    'kien_thuc_chung' => 'primary',
                    'kien_thuc_khoa_hoc_co_ban' => 'secondary',
                    'kien_thuc_bo_tro' => 'info',
                    'kien_thuc_co_so_nganh_lien_nganh' => 'warning',
                    'kien_thuc_chuyen_nganh' => 'danger',
                    'hoc_phan_nghe_nghiep' => 'teal',
                    'hoc_phan_thuc_tap_tot_nghiep' => 'lime',
                    'hoc_phan_tot_nghiep' => 'cyan',
                    'khoi_kien_thuc_dieu_kien_tot_nghiep' => 'success',
                    'khoi_kien_thuc_ky_su_dac_thu' => 'dark',
                    'do_an_thuc_tap' => 'success',
                    default => 'secondary',
                };
            @endphp


            <h5 class="mt-5 bg-light p-2 border-start border-4 border-{{ $borderColor }}">
                {{ $typeLabel }}
            </h5>

            <table class="table table-bordered align-middle">
                <thead class="table-secondary text-center">
                    <tr>
                        <th>HK</th>
                        <th>Mã HP</th>
                        <th>Tên học phần</th>
                        <th>Nhóm</th>
                        <th>BB / TC</th>
                        <th>LT</th>
                        <th>TH</th>
                        <th>Tổng TC</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $row)
                        <tr>
                            <td class="text-center">{{ $row->semester_no }}</td>
                            <td>{{ $row->course_code }}</td>
                            <td>{{ $row->course_name }}</td>
                            <td>{{ $row->course_group }}</td>
                            <td class="text-center">
                                @if ($row->is_compulsory)
                                    <span class="badge bg-success">Bắt buộc</span>
                                @else
                                    <span class="badge bg-warning text-dark">Tự chọn</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $row->credit_theory }}</td>
                            <td class="text-center">{{ $row->credit_practice }}</td>
                            <td class="text-center fw-bold">{{ $row->credit_total }}</td>
                            <td>{{ $row->note }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
@endsection
