@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <h3 class="mb-4">🧭 Khung Chương trình Đào tạo – Demo</h3>

        <div class="mb-3">
            <label class="form-label fw-semibold">Chọn phiên bản CTĐT:</label>
            <select class="form-select w-auto d-inline-block">
                <option selected>CNTT – Khóa 47 (K47-IT)</option>
                <option>CNTT – Khóa 48 (K48-IT)</option>
                <option>QTKD – Khóa 47 (K47-BBA)</option>
            </select>
        </div>

        {{-- ==================== KIẾN THỨC CHUNG ==================== --}}
        <h5 class="mt-4 bg-light p-2 border-start border-4 border-primary">
            Kiến thức chung
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
                <tr>
                    <td class="text-center">1</td>
                    <td>PLDC</td>
                    <td>Pháp luật đại cương</td>
                    <td>Nhóm HP bắt buộc</td>
                    <td class="text-center"><span class="badge bg-success">Bắt buộc</span></td>
                    <td class="text-center">2</td>
                    <td class="text-center">0</td>
                    <td class="text-center fw-bold">2</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-center">1</td>
                    <td>PPNCKH</td>
                    <td>Phương pháp nghiên cứu khoa học</td>
                    <td>Nhóm HP bắt buộc</td>
                    <td class="text-center"><span class="badge bg-success">Bắt buộc</span></td>
                    <td class="text-center">3</td>
                    <td class="text-center">0</td>
                    <td class="text-center fw-bold">3</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        {{-- ==================== KIẾN THỨC KHOA HỌC CƠ BẢN ==================== --}}
        <h5 class="mt-5 bg-light p-2 border-start border-4 border-secondary">
            Kiến thức khoa học cơ bản
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
                <tr>
                    <td class="text-center">2</td>
                    <td>TCCA1</td>
                    <td>Toán cao cấp A1</td>
                    <td>Nhóm HP bắt buộc</td>
                    <td class="text-center"><span class="badge bg-success">Bắt buộc</span></td>
                    <td class="text-center">2</td>
                    <td class="text-center">0</td>
                    <td class="text-center fw-bold">2</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>TCCA2</td>
                    <td>Toán cao cấp A2</td>
                    <td>Nhóm HP bắt buộc</td>
                    <td class="text-center"><span class="badge bg-success">Bắt buộc</span></td>
                    <td class="text-center">2</td>
                    <td class="text-center">0</td>
                    <td class="text-center fw-bold">2</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        {{-- ==================== KIẾN THỨC CHUYÊN NGÀNH ==================== --}}
        <h5 class="mt-5 bg-light p-2 border-start border-4 border-danger">
            Kiến thức chuyên ngành
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
                <tr>
                    <td class="text-center">5</td>
                    <td>CNWUD</td>
                    <td>Công nghệ web và ứng dụng</td>
                    <td>Nhóm HP bắt buộc</td>
                    <td class="text-center"><span class="badge bg-success">Bắt buộc</span></td>
                    <td class="text-center">3</td>
                    <td class="text-center">0</td>
                    <td class="text-center fw-bold">3</td>
                    <td>Bao gồm thiết kế web</td>
                </tr>
                <tr>
                    <td class="text-center">6</td>
                    <td>IOT</td>
                    <td>Công nghệ Internet Of Things</td>
                    <td>Module tự chọn 2</td>
                    <td class="text-center"><span class="badge bg-warning text-dark">Tự chọn</span></td>
                    <td class="text-center">3</td>
                    <td class="text-center">0</td>
                    <td class="text-center fw-bold">3</td>
                    <td>Thuộc nhóm phát triển ứng dụng và phân tích dữ liệu</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
