@extends('layouts.app')

@section('content')
<div class="container mt-6 pt-3">
<style>
    .tab-pane-box {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .gradient-text {
        background: linear-gradient(to bottom right, #dfc42fff, #0077ffff, #f148b1ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
    }

    .nav-tabs .nav-link.active {
        background-color: #e2e6ea;
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: bold;
    }

    .nav-tabs .nav-link {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-tabs img {
        width: 20px;
        height: 20px;
    }
</style>

<h3 class="text-center py-3 gradient-text">TÌM KIẾM & TRA CỨU THÔNG TIN</h3>

<ul class="nav nav-tabs mb-3" id="timkiemTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="gv-tab" data-bs-toggle="tab" data-bs-target="#gv" type="button" role="tab">
            <img src="{{ asset('images/giangvien.png') }}"> Giảng viên
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="lich-tab" data-bs-toggle="tab" data-bs-target="#lich" type="button" role="tab">
            <img src="{{ asset('images/lichthi.png') }}"> Lịch giảng/thi
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="loc-tab" data-bs-toggle="tab" data-bs-target="#loc" type="button" role="tab">
            <img src="{{ asset('images/timkiem.png') }}"> Lọc hoạt động
        </button>
    </li>
</ul>

<div class="tab-content" id="timkiemTabContent">
    {{-- Giảng viên --}}
    <div class="tab-pane fade show active" id="gv" role="tabpanel">
        <div class="tab-pane-box">
            <form class="row g-2 mb-3">
                <div class="col-md-4"><input type="text" class="form-control" placeholder="Tên giảng viên"></div>
                <div class="col-md-4"><input type="text" class="form-control" placeholder="Mã số"></div>
                <div class="col-md-4"><input type="text" class="form-control" placeholder="Bộ môn"></div>
                <div class="col-md-12"><button class="btn btn-primary w-100">Tìm kiếm</button></div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="table-primary">
                        <tr><th>Họ tên</th><th>Mã số</th><th>Bộ môn</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Trần Thị B</td><td>GV001</td><td>Khoa học máy tính</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Lịch giảng/thi --}}
    <div class="tab-pane fade" id="lich" role="tabpanel">
        <div class="tab-pane-box">
            <form class="row g-2 mb-3">
                <div class="col-md-4"><input type="text" class="form-control" placeholder="Tuần"></div>
                <div class="col-md-4"><input type="text" class="form-control" placeholder="Học kỳ"></div>
                <div class="col-md-4"><button class="btn btn-primary w-100">Tra cứu</button></div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="table-primary">
                        <tr><th>Ngày</th><th>Tiết</th><th>Môn</th><th>Lớp</th><th>Phòng</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>10/10/2024</td><td>1-3</td><td>Toán rời rạc</td><td>D19CQCN01</td><td>201</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Lọc hoạt động --}}
    <div class="tab-pane fade" id="loc" role="tabpanel">
        <div class="tab-pane-box">
            <form class="row g-2 mb-3">
                <div class="col-md-4"><input type="text" class="form-control" placeholder="Loại hoạt động"></div>
                <div class="col-md-4"><input type="text" class="form-control" placeholder="Tiêu chí"></div>
                <div class="col-md-4"><button class="btn btn-primary w-100">Lọc</button></div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="table-primary">
                        <tr><th>Hoạt động</th><th>Giảng viên</th><th>Thời gian</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Đề tài NCKH</td><td>Trần Thị B</td><td>2024-05-10</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
